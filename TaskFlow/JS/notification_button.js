// This script handles the simple toggling of a notification badge visibility.
// NOTE: This appears to be a mock implementation, as it only toggles visibility
// and does not fetch or display actual notifications.

// Gets the notification button and badge elements from the DOM.
const notificationBtn = document.getElementById("notificationBtn");
const notificationBadge = document.getElementById("notificationBadge");

// Checks if both the button and the badge exist on the page before adding an event listener.
if (notificationBtn && notificationBadge) {
  // Adds a click event listener to the notification button.
  notificationBtn.addEventListener("click", () => {
    // Toggles the display style of the badge between 'none' (hidden) and 'inline' (visible).
    if (notificationBadge.style.display === "none") {
      notificationBadge.style.display = "inline";
    } else {
      notificationBadge.style.display = "none";
    }
  });
}