// This script handles the dynamic and interactive elements of the homepage.
document.addEventListener("DOMContentLoaded", () => {
  // --- Mobile Menu Toggle ---
  // This allows the navigation menu to be shown or hidden on mobile devices.
  const mobileToggle = document.getElementById("mobileToggle");
  const navMenu = document.getElementById("navMenu");
  mobileToggle.addEventListener("click", () =>
    navMenu.classList.toggle("active")
  );

  // --- Header Scroll Effect ---
  // Adds a 'scrolled' class to the header when the user scrolls down more than 50 pixels.
  // This is typically used for styling changes, like making the header smaller or adding a shadow.
  const header = document.querySelector(".site-header");
  window.addEventListener("scroll", () => {
    header.classList.toggle("scrolled", window.scrollY > 50);
  });

  // --- Scroll-in Animations ---
  // This uses the Intersection Observer API to add a 'visible' class to elements
  // as they enter the viewport, triggering a fade-in or slide-in animation.
  const animatedElements = document.querySelectorAll(".animated-card");
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          // Stop observing the element once it's visible to improve performance.
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 } // The animation triggers when 10% of the element is visible.
  );
  // Attaches the observer to each element with the 'animated-card' class.
  animatedElements.forEach((card) => observer.observe(card));
});

// --- Particles.js Configuration ---
// Initializes the Particles.js library to create an animated background effect.
particlesJS("particles-js", {
  particles: {
    number: { value: 80, density: { enable: true, value_area: 800 } },
    color: { value: "#2563eb" },
    shape: { type: "circle" },
    opacity: {
      value: 0.5,
      random: true,
      anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false },
    },
    size: { value: 3, random: true, anim: { enable: false } },
    line_linked: {
      enable: true,
      distance: 150,
      color: "#8b5cf6",
      opacity: 0.4,
      width: 1,
    },
    move: {
      enable: true,
      speed: 3,
      direction: "none",
      random: true,
      straight: false,
      out_mode: "out",
      bounce: false,
    },
  },
  interactivity: {
    detect_on: "canvas",
    events: {
      onhover: { enable: true, mode: "grab" },
      onclick: { enable: true, mode: "push" },
      resize: true,
    },
    modes: {
      grab: { distance: 140, line_linked: { opacity: 1 } },
      push: { particles_nb: 4 },
    },
  },
  retina_detect: true,
});


