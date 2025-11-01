/**
 * Toggles the visibility of a mobile navigation menu.
 * Note: The '.nav' class selector might be from a different page context, as it's not on the signup page itself.
 */
function toggleMenu() {
  document.querySelector('.nav').classList.toggle('show');
}

// --- Login/Register Form Toggle ---

// Element references for the toggle buttons and form containers.
var a = document.getElementById("loginBtn");
var b = document.getElementById("registerBtn");
var x = document.getElementById("login");
var y = document.getElementById("register");

/**
 * Switches the view to the login form.
 * It uses CSS transforms to create a sliding animation.
 */
function login() {
    // Slides the login form into view and the register form out of view.
    x.style.transform = "translate(-50%, -50%) translateX(0)";
    y.style.transform = "translate(-50%, -50%) translateX(100%)";
    x.style.opacity = 1;
    y.style.opacity = 0;
    // Updates button styles to indicate the active form.
    a.className += " white-btn";
    b.className = "btn";
}

/**
 * Switches the view to the register form.
 * It uses CSS transforms to create a sliding animation.
 */
function register() {
    // Slides the register form into view and the login form out of view.
    x.style.transform = "translate(-50%, -50%) translateX(-100%)";
    y.style.transform = "translate(-50%, -50%) translateX(0)";
    x.style.opacity = 0;
    y.style.opacity = 1;
    // Updates button styles to indicate the active form.
    a.className = "btn";
    b.className += " white-btn";
}