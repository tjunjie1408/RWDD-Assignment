// Toggle mobile navigation menu
function toggleMenu() {
  document.querySelector('.nav').classList.toggle('show');
}

// Toggle between login and register forms
const container = document.querySelector('.container');
const LoginLink = document.querySelector('.SignInLink');
const RegisterLink = document.querySelector('.SignUpLink');

RegisterLink.addEventListener('click', () =>{
    container.classList.add('active');
})

LoginLink.addEventListener('click', () => {
    container.classList.remove('active');
})

