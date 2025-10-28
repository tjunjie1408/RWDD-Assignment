// This script manages the behavior of the responsive sidebar.

// --- Element References ---
const sidebar = document.querySelector(".sidebar");
const sidebarMenuBtn = document.querySelector(".sidebar-menu-button"); // The hamburger menu for mobile
const sidebarToggler = document.querySelector(".sidebar-toggler"); // The chevron icon to collapse the sidebar

// --- Event Listeners ---

// Handles clicks on the mobile menu button (hamburger).
if (sidebarMenuBtn) {
  sidebarMenuBtn.addEventListener("click", () => {
    // This logic is for screen sizes 1024px or less.
    if (window.innerWidth <= 1024) {
      // Toggles the 'active' class to show or hide the sidebar.
      sidebar.classList.toggle("active");

      // Hides the hamburger button when the sidebar is open.
      if (sidebar.classList.contains("active")) {
        sidebarMenuBtn.style.display = "none";
      } else {
        sidebarMenuBtn.style.display = "flex";
      }
    }
  });
}

// Handles clicks on the sidebar's internal toggle button (chevron).
if (sidebarToggler) {
  sidebarToggler.addEventListener("click", () => {
    // On smaller screens, this button's job is to close the sidebar.
    if (window.innerWidth <= 1024) {
      sidebar.classList.remove("active");
      sidebarMenuBtn.style.display = "flex"; // Show the hamburger button again.
    }
    // Note: On larger screens, the toggling is likely handled by CSS transitions on the 'collapsed' class,
    // which seems to be missing from this script but present in the HTML/CSS.
  });
}

// Adjusts the sidebar state when the browser window is resized.
window.addEventListener("resize", () => {
  if (window.innerWidth > 1024) {
    // On large screens, ensure the mobile 'active' state is removed and the hamburger button is hidden.
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none";
  } else {
    // On small screens, ensure the sidebar is closed by default and the hamburger button is visible.
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex";
  }
});

// --- Initial State ---
// Sets the correct initial state of the sidebar and hamburger button when the page first loads.
if (window.innerWidth <= 1024) {
  sidebar.classList.remove("active");
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex";
} else {
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none";
}