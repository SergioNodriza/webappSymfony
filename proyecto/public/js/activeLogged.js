let currentLocation = window.location.href;
let toList = document.getElementById("toList");
let toNew = document.getElementById("toNew");

if (currentLocation === "http://localhost:250/new-item") {
    toList.className = toList.className.replace("active", "");
    toNew.className += " active";
}