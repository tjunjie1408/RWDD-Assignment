document.getElementById('show-signup').addEventListener('click', function() {
    document.getElementById('login-container').classList.add('hidden');
    document.getElementById('signup-container').classList.remove('hidden');
});

document.getElementById('show-login').addEventListener('click', function() {
    document.getElementById('signup-container').classList.add('hidden');
    document.getElementById('login-container').classList.remove('hidden');
});

document.getElementById('signup-form').addEventListener('submit', function(event) {
    event.preventDefault();
    var response = grecaptcha.getResponse();
    if (response.length == 0) {
        document.getElementById('message').innerHTML = "Please complete the reCAPTCHA.";
        document.getElementById('message').style.color = "red";
    } else {
        document.getElementById('message').innerHTML = "Sign up successful!";
        document.getElementById('message').style.color = "green";
    }
});

document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();
    document.getElementById('message').innerHTML = "Login successful!";
    document.getElementById('message').style.color = "green";
});