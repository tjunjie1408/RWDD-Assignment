// Toggle mobile navigation menu
function toggleMenu() {
  document.querySelector('.nav').classList.toggle('show');
}

var a = document.getElementById("loginBtn");
var b = document.getElementById("registerBtn");
var x = document.getElementById("login");
var y = document.getElementById("register");

function login() {
    x.style.transform = "translate(-50%, -50%) translateX(0)";
    y.style.transform = "translate(-50%, -50%) translateX(100%)";
    x.style.opacity = 1;
    y.style.opacity = 0;
    a.className += " white-btn";
    b.className = "btn";
}

function register() {
    x.style.transform = "translate(-50%, -50%) translateX(-100%)";
    y.style.transform = "translate(-50%, -50%) translateX(0)";
    x.style.opacity = 0;
    y.style.opacity = 1;
    a.className = "btn";
    b.className += " white-btn";
}