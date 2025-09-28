// ===== Notification Button =====
const notificationBtn = document.getElementById("notificationBtn");
const notificationBadge = document.getElementById("notificationBadge");

if (notificationBtn && notificationBadge) {
  notificationBtn.addEventListener("click", () => {
    if (notificationBadge.style.display === "none") {
      notificationBadge.style.display = "inline";
    } else {
      notificationBadge.style.display = "none";
    }
  });
}