<?php

namespace App\Http\Controllers;

use App\Models\CodigoRecuperacion;
use App\Models\Usuario;
use App\Notifications\PasswordActualizada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RecuperacionController extends Controller
{
    private const CODIGO_MINIMO = 100000;
    private const CODIGO_MAXIMO = 999999;
    private const EXPIRACION_MINUTOS = 10;

    private const SESION_USUARIO = 'recuperacion_usuario_id';
    private const SESION_CODIGO = 'recuperacion_codigo_id';
    private const SESION_VERIFICADA = 'recuperacion_verificada';

    public function mostrarCorreo(): View
    {
        return view('recuperation');
    }

    public function enviarCodigo(Request $request): RedirectResponse
    {
        $datos = $request->validate(
            ['correo' => ['required', 'email', 'max:150']],
            [
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'Ingresa un correo electrónico válido.',
                'correo.max' => 'El correo electrónico es demasiado largo.',
            ]
        );

        $correo = strtolower(trim($datos['correo']));
        $usuario = Usuario::query()->where('correo', $correo)->first();

        if (!$usuario) {
            return back()
                ->withErrors(['correo' => '⚠ El correo ingresado no está registrado. ⚠'])
                ->onlyInput('correo');
        }

        [$recuperacion, $codigo] = $this->generarCodigoRecuperacion($usuario);

        $this->enviarCorreoRecuperacion($usuario, $codigo);

        $this->guardarSesionRecuperacion($usuario->id, $recuperacion->id);

        return redirect()
            ->route('recuperacion.codigo')
            ->with('success', 'Se envió un código de recuperación a tu correo.');
    }

    public function mostrarCodigo(): View|RedirectResponse
    {
        if (!session()->has(self::SESION_USUARIO)) {
            return redirect()->route('recuperacion');
        }

        return view('validar');
    }

    public function validarCodigo(Request $request): RedirectResponse
    {
        $datos = $request->validate(
            [
                'codigo' => ['required', 'array', 'size:6'],
                'codigo.*' => ['required', 'digits:1'],
            ],
            [
                'codigo.required' => 'Ingresa el código de recuperación.',
                'codigo.size' => 'El código debe contener 6 dígitos.',
                'codigo.*.required' => 'Debes completar todos los dígitos.',
                'codigo.*.digits' => 'El código solo puede contener números.',
            ]
        );

        $codigoIngresado = implode('', $datos['codigo']);

        if (!$this->tieneSesionRecuperacion()) {
            return redirect()
                ->route('recuperacion')
                ->withErrors(['correo' => 'La sesión de recuperación ha expirado.']);
        }

        $recuperacion = $this->obtenerRecuperacionSesion();

        if (!$recuperacion) {
            return redirect()
                ->route('recuperacion')
                ->withErrors(['correo' => 'No existe una recuperación válida.']);
        }

        if ($recuperacion->expira_en->isPast()) {
            $recuperacion->delete();

            session()->forget([
                self::SESION_CODIGO,
                self::SESION_VERIFICADA,
            ]);

            return redirect()
                ->route('recuperacion.codigo')
                ->withErrors(['codigo' => '⚠ El código ha expirado. Solicita uno nuevo. ⚠']);
        }

        if (!Hash::check($codigoIngresado, $recuperacion->codigo)) {
            return back()->withErrors([
                'codigo' => 'El código ingresado es incorrecto.',
            ]);
        }

        session([self::SESION_VERIFICADA => true]);

        return redirect()->route('recuperacion.password');
    }

    public function mostrarNuevaPassword(): View|RedirectResponse
    {
        if (!$this->sesionRecuperacionVerificada()) {
            return redirect()->route('recuperacion');
        }

        $recuperacion = $this->obtenerRecuperacionSesion();

        if (!$this->recuperacionEsValida($recuperacion)) {
            $this->limpiarSesionRecuperacion();

            return redirect()
                ->route('recuperacion')
                ->withErrors([
                    'correo' => 'La recuperación ha expirado. Solicita un nuevo código.',
                ]);
        }

        return view('newpassword');
    }

    public function cambiarPassword(Request $request): RedirectResponse
    {
        if (!$this->sesionRecuperacionVerificada()) {
            return redirect()->route('recuperacion');
        }

        $recuperacion = $this->obtenerRecuperacionSesion();

        if (!$this->recuperacionEsValida($recuperacion)) {
            $this->limpiarSesionRecuperacion();

            return redirect()
                ->route('recuperacion')
                ->withErrors([
                    'correo' => 'La recuperación ha expirado. Solicita un nuevo código.',
                ]);
        }

        $datos = $request->validate(
            ['password' => ['required', 'string', 'min:8', 'confirmed']],
            [
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        $usuario = Usuario::findOrFail(session(self::SESION_USUARIO));

        DB::transaction(function () use ($usuario, $recuperacion, $datos) {
            $usuario->update([
                'password' => Hash::make($datos['password']),
            ]);

            $recuperacion->update([
                'usado_en' => now(),
            ]);
        });

        $this->limpiarSesionRecuperacion();

        $usuario->notify(new PasswordActualizada());

        return redirect()
            ->route('login')
            ->with('success', 'Tu contraseña se actualizó correctamente.');
    }

    public function reenviarCodigo(): RedirectResponse
    {
        $usuarioId = session(self::SESION_USUARIO);

        if (!$usuarioId) {
            return redirect()
                ->route('recuperacion')
                ->withErrors(['correo' => 'La sesión de recuperación ha expirado.']);
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario) {
            $this->limpiarSesionRecuperacion();

            return redirect()
                ->route('recuperacion')
                ->withErrors(['correo' => 'No se pudo encontrar el usuario.']);
        }

        [$recuperacion, $codigo] = $this->generarCodigoRecuperacion($usuario);

        $this->enviarCorreoRecuperacion($usuario, $codigo, true);

        $this->guardarSesionRecuperacion($usuario->id, $recuperacion->id);

        return redirect()
            ->route('recuperacion.codigo')
            ->with('success', 'Se ha enviado un nuevo código a tu correo.');
    }

    private function generarCodigoRecuperacion(Usuario $usuario): array
    {
        return DB::transaction(function () use ($usuario) {
            CodigoRecuperacion::query()
                ->where('usuario_id', $usuario->id)
                ->delete();

            $codigo = (string) random_int(
                self::CODIGO_MINIMO,
                self::CODIGO_MAXIMO
            );

            $recuperacion = CodigoRecuperacion::create([
                'usuario_id' => $usuario->id,
                'codigo' => Hash::make($codigo),
                'expira_en' => now()->addMinutes(self::EXPIRACION_MINUTOS),
            ]);

            return [$recuperacion, $codigo];
        });
    }

    private function enviarCorreoRecuperacion(
        Usuario $usuario,
        string $codigo,
        bool $reenvio = false
    ): void {
        $texto = $reenvio
            ? "Tu nuevo código de recuperación de ArquiServi es: {$codigo}. Este código expirará en 10 minutos."
            : "Tu código de recuperación de ArquiServi es: {$codigo}. Este código expirará en 10 minutos.";

        $asunto = $reenvio
            ? 'Nuevo código de recuperación - ArquiServi'
            : 'Recuperación de contraseña - ArquiServi';

        Mail::raw($texto, function ($mensaje) use ($usuario, $asunto) {
            $mensaje
                ->to($usuario->correo)
                ->subject($asunto);
        });
    }

    private function obtenerRecuperacionSesion(): ?CodigoRecuperacion
    {
        $usuarioId = session(self::SESION_USUARIO);
        $codigoId = session(self::SESION_CODIGO);

        if (!$usuarioId || !$codigoId) {
            return null;
        }

        return CodigoRecuperacion::query()
            ->whereKey($codigoId)
            ->where('usuario_id', $usuarioId)
            ->whereNull('usado_en')
            ->first();
    }

    private function recuperacionEsValida(?CodigoRecuperacion $recuperacion): bool
    {
        return $recuperacion !== null
            && !$recuperacion->expira_en->isPast()
            && !$recuperacion->usado_en;
    }

    private function tieneSesionRecuperacion(): bool
    {
        return session()->has(self::SESION_USUARIO)
            && session()->has(self::SESION_CODIGO);
    }

    private function sesionRecuperacionVerificada(): bool
    {
        return $this->tieneSesionRecuperacion()
            && session(self::SESION_VERIFICADA) === true;
    }

    private function guardarSesionRecuperacion(int $usuarioId, int $codigoId): void
    {
        session([
            self::SESION_USUARIO => $usuarioId,
            self::SESION_CODIGO => $codigoId,
            self::SESION_VERIFICADA => false,
        ]);
    }

    private function limpiarSesionRecuperacion(): void
    {
        session()->forget([
            self::SESION_USUARIO,
            self::SESION_CODIGO,
            self::SESION_VERIFICADA,
        ]);
    }
}