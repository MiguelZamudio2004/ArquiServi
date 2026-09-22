const password = document.getElementById("password");
const bar = document.getElementById("bar");
const message = document.getElementById("message");
const eye = document.getElementById("eye");

password.addEventListener("input",()=> {
    const length =password.value.length;
    if(length === 0) {
        bar.style.width="0";
        message.textContent = "Start typing...";
    } else if(length<5){
        bar.style.width="30%";
        bar.style.background='#ef4444'
        message.textContent="Weak password";
    } else if (length<9){
        bar.style.width="65%";
        bar.style.background='#f59e0b'
        message.textContent="Medium password";
    } else {
        bar.style.width="100%";
        bar.style.background='#22c55e'
        message.textContent="Strong password";
    }
});

eye.addEventListener("click",() => {
    if(password.type ==="password"){
        password.type = "text";
    } else {
        password.type = "password";
    }
});