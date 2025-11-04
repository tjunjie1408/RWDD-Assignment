// This script manages the behavior of the responsive sidebar.

// --- Element References ---
const sidebar = document.querySelector(".sidebar");
const sidebarMenuBtn = document.querySelector(".sidebar-menu-button"); // The hamburger menu for mobile
const sidebarToggler = document.querySelector(".sidebar-toggler"); // The chevron icon to collapse the sidebar

console.log("Sidebar element:", sidebar);
console.log("Sidebar Menu Button element:", sidebarMenuBtn);

// --- Event Listeners ---

// Handles clicks on the mobile menu button (hamburger).
if (sidebarMenuBtn) {
  sidebarMenuBtn.addEventListener("click", () => {
    console.log("Sidebar Menu Button clicked!");
    console.log("Current window width:", window.innerWidth);

    // This logic is for screen sizes 1024px or less.
    if (window.innerWidth <= 1024) {
      // Toggles the 'active' class to show or hide the sidebar.
      sidebar.classList.toggle("active");
      console.log("Sidebar active class toggled. Current classes:", sidebar.classList);

      // Hides the hamburger button when the sidebar is open.
      if (sidebar.classList.contains("active")) {
        sidebarMenuBtn.style.display = "none";
        console.log("Sidebar is active, hiding sidebarMenuBtn.");
      } else {
        sidebarMenuBtn.style.display = "flex";
        console.log("Sidebar is inactive, showing sidebarMenuBtn.");
      }
    }
  });
}

// Handles clicks on the sidebar's internal toggle button (chevron).
if (sidebarToggler) {
  sidebarToggler.addEventListener("click", () => {
    console.log("Sidebar Toggler clicked!");
    // On smaller screens, this button's job is to close the sidebar.
    if (window.innerWidth <= 1024) {
      sidebar.classList.remove("active");
      sidebarMenuBtn.style.display = "flex"; // Show the hamburger button again.
      console.log("Sidebar is inactive (via toggler), showing sidebarMenuBtn.");
    }
  });
}

// Add an event listener to close the sidebar when clicking outside it
document.addEventListener("click", (event) => {
  if (window.innerWidth <= 1024 && sidebar.classList.contains("active")) {
    // Check if the click is outside the sidebar and not on the sidebarMenuBtn itself
    if (!sidebar.contains(event.target) && !sidebarMenuBtn.contains(event.target)) {
      sidebar.classList.remove("active");
      sidebarMenuBtn.style.display = "flex"; // Show the hamburger button again
      console.log("Sidebar is inactive (via outside click), showing sidebarMenuBtn.");
    }
  }
});

// Adjusts the sidebar state when the browser window is resized.
window.addEventListener("resize", () => {
  if (window.innerWidth > 1024) {
    // On large screens, ensure the mobile 'active' state is removed and the hamburger button is hidden.
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none";
    console.log("Window resized > 1024px. Sidebar inactive, sidebarMenuBtn hidden.");
  } else {
    // On small screens, ensure the sidebar is closed by default and the hamburger button is visible.
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex";
    console.log("Window resized <= 1024px. Sidebar inactive, sidebarMenuBtn shown.");
  }
});

// --- Initial State ---
// Sets the correct initial state of the sidebar and hamburger button when the page first loads.
if (window.innerWidth <= 1024) {
  sidebar.classList.remove("active");
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex";
  console.log("Initial state (<= 1024px): Sidebar inactive, sidebarMenuBtn shown.");
} else {
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none";
  console.log("Initial state (> 1024px): Sidebar inactive, sidebarMenuBtn hidden.");
}