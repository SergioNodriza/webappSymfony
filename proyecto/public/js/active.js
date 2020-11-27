let currentLocation = window.location.href;
let toLogIn = document.getElementById("toLogin");
let toRegister = document.getElementById("toRegister");

if (currentLocation === "http://localhost:250/register") {
    toLogIn.className = toLogIn.className.replace("active", "");
    toRegister.className += " active";
}