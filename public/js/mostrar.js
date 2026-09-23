const password = document.getElementById("password");
const bar = document.getElementById("bar");
const message = document.getElementById("message");
const eye = document.getElementById("eye");
const confirmPassword = document.getElementById("confirmPassword");
const confirmMessage = document.getElementById("confirmMessage");

password.addEventListener("input",()=> {
    const length =password.value.length;
    if(length === 0) {
        bar.style.width="0";
        message.textContent = "Empieza a escribir...";
    } else if(length<8){
        bar.style.width="30%";
        bar.style.background='#ef4444'
        message.textContent="Contraseña inválida";
    } else if (length<12){
        bar.style.width="65%";
        bar.style.background='#f59e0b'
        message.textContent="Contraseña media";
    } else {
        bar.style.width="100%";
        bar.style.background='#22c55e'
        message.textContent="Contraseña segura";
    }

    // Verificar coincidencia mientras se escribe
    if (confirmPassword.value.length > 0) {
        checkPasswords();
    }
});

eye.addEventListener("click",() => {
    if(password.type ==="password"){
        password.type = "text";
    } else {
        password.type = "password";
    }
});

confirmPassword.addEventListener("input", checkPasswords);

function checkPasswords() {
    if (confirmPassword.value === password.value) {
        confirmMessage.textContent = "Las contraseñas coinciden";
        confirmMessage.style.color = "#22c55e";
    } else {
        confirmMessage.textContent = "Las contraseñas no coinciden";
        confirmMessage.style.color = "#ef4444";
    }
}