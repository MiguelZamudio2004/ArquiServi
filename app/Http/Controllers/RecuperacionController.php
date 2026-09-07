<?php

namespace App\Http\Controllers;

use App\Models\CodigoRecuperacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RecuperacionController extends Controller
{
    public function mostrarCorreo()
    {
        return view('recuperation');
    }

    public function enviarCodigo(Request $request)
    {
        $datos = $request->validate(
            [
                'correo' => 'required|email|max:150',
            ],
            [
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'Ingresa un correo electrónico válido.',
                'correo.max' => 'El correo electrónico es demasiado largo.',
            ]
        );

        $usuario = Usuario::where('correo', $datos['correo'])->first();

        if (!$usuario) {
            return back()->with(
                'success',
                'Si el correo está registrado, recibirás un código de recuperación.'
            );
        }

        CodigoRecuperacion::where('usuario_id', $usuario->id)->delete();

        $codigo = (string) random_int(100000, 999999);

        $codigoRecuperacion = CodigoRecuperacion::create([
            'usuario_id' => $usuario->id,
            'codigo' => Hash::make($codigo),
            'expira_en' => now()->addMinutes(10),
        ]);

        Mail::raw(
            "Tu código de recuperación de ArquiServi es: {$codigo}. "
            . "Este código expirará en 10 minutos.",
            function ($mensaje) use ($usuario) {
                $mensaje
                    ->to($usuario->correo)
                    ->subject('Recuperación de contraseña - ArquiServi');
            }
        );

        session([
            'recuperacion_usuario_id' => $usuario->id,
            'recuperacion_codigo_id' => $codigoRecuperacion->id,
        ]);

        return redirect()
            ->route('recuperacion.codigo')
            ->with('success', 'Se envió un código de recuperación a tu correo.');
    }

    public function mostrarCodigo()
    {
        if (!session()->has('recuperacion_usuario_id')) {
            return redirect()->route('recuperacion');
        }

        return view('validar');
    }

    public function validarCodigo(Request $request)
{
    $request->validate(
        [
            'codigo' => 'required|array|size:6',
            'codigo.*' => 'required|digits:1',
        ],
        [
            'codigo.required' => 'Ingresa el código de recuperación.',
            'codigo.size' => 'El código debe contener 6 dígitos.',
            'codigo.*.required' => 'Debes completar todos los dígitos.',
            'codigo.*.digits' => 'El código solo puede contener números.',
        ]
    );

    // Une las 6 cajas:
    // ['1','2','3','4','5','6'] → "123456"
    $codigoIngresado = implode('', $request->codigo);

    $usuarioId = session('recuperacion_usuario_id');
    $codigoId = session('recuperacion_codigo_id');

    if (!$usuarioId || !$codigoId) {
        return redirect()
            ->route('recuperacion')
            ->withErrors([
                'correo' => 'La sesión de recuperación ha expirado.',
            ]);
    }

    $recuperacion = CodigoRecuperacion::where('id', $codigoId)
        ->where('usuario_id', $usuarioId)
        ->whereNull('usado_en')
        ->first();

    if (!$recuperacion) {
        return redirect()
            ->route('recuperacion')
            ->withErrors([
                'correo' => 'No existe una recuperación válida.',
            ]);
    }

    if (now()->greaterThan($recuperacion->expira_en)) {

        $recuperacion->delete();

        session()->forget([
            'recuperacion_usuario_id',
            'recuperacion_codigo_id',
        ]);

        return redirect()
            ->route('recuperacion')
            ->withErrors([
                'correo' => 'El código ha expirado. Solicita uno nuevo.',
            ]);
    }

    if (!Hash::check($codigoIngresado, $recuperacion->codigo)) {
        return back()
            ->withErrors([
                'codigo' => 'El código ingresado es incorrecto.',
            ]);
    }

    session([
        'recuperacion_verificada' => true,
    ]);

    return redirect()->route('recuperacion.password');
}
    

    public function mostrarNuevaPassword()
    {
        if (
            !session('recuperacion_verificada') ||
            !session('recuperacion_usuario_id')
        ) {
            return redirect()->route('recuperacion');
        }

        return view('newpassword');
    }

    public function cambiarPassword(Request $request)
    {
        if (
            !session('recuperacion_verificada') ||
            !session('recuperacion_usuario_id')
        ) {
            return redirect()->route('recuperacion');
        }

        $datos = $request->validate(
            [
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'password.required' => 'La nueva contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
            ]
        );

        $usuario = Usuario::findOrFail(
            session('recuperacion_usuario_id')
        );

        $usuario->password = $datos['password'];
        $usuario->save();

        $codigo = CodigoRecuperacion::find(
            session('recuperacion_codigo_id')
        );

        if ($codigo) {
            $codigo->update([
                'usado_en' => now(),
            ]);
        }

        session()->forget([
            'recuperacion_usuario_id',
            'recuperacion_codigo_id',
            'recuperacion_verificada',
        ]);

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Tu contraseña se actualizó correctamente.'
            );
    }
    
}