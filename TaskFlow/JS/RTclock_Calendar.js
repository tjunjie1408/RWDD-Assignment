// This file appears to contain a mix of functionalities: a real-time clock and a
// client-side calendar implementation that uses localStorage for persistence.
// NOTE: The calendar logic in this file seems to be a separate, older implementation
// and might conflict with the server-based logic in `goal_calendar.js` and `goals.js`.

/**
 * A placeholder function, likely for future dropdown menu logic.
 */
function closeAllDropdowns() {
  // Future dropdown logic here
}

// ===== Real-Time Clock =====

/**
 * Updates the content of the real-time clock element every second.
 */
function updateClock() {
  const clock = document.getElementById("realTimeClock");
  if (!clock) return; // Exit if the clock element doesn't exist on the page.

  const now = new Date();
  const day = now.getDate().toString().padStart(2, "0");
  const month = (now.getMonth() + 1).toString().padStart(2, "0");
  const year = now.getFullYear();

  const weekdays = ["SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY"];
  const weekday = weekdays[now.getDay()];

  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  const seconds = now.getSeconds().toString().padStart(2, "0");

  // Formats the date and time string and sets it as the clock's text content.
  clock.textContent = `${day}/${month}/${year} (${weekday})   ${hours}:${minutes}:${seconds}`;
}
// Initial call to display the clock immediately.
updateClock();
// Sets an interval to update the clock every 1000 milliseconds (1 second).
setInterval(updateClock, 1000);


// ===== LocalStorage-Based Calendar Implementation =====
// This section runs when the DOM is fully loaded.
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");

  // Only executes if a calendar element is found on the page.
  if (calendarEl) {
    // Retrieves events from the browser's localStorage or initializes an empty array.
    let savedEvents = JSON.parse(localStorage.getItem("calendarEvents")) || [];

    // Initializes a new FullCalendar instance.
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      editable: false, // Events are not draggable or resizable.
      selectable: true, // Dates can be selected.
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "",
      },
      events: savedEvents, // Loads the events from localStorage.

      // Customizes the appearance of events after they are rendered.
      eventDidMount: function (info) {
        const completed = info.event.extendedProps.isCompleted;
        info.el.style.backgroundColor = completed ? "#28a745" : "#808080";
        info.el.style.borderColor = completed ? "#28a745" : "#808080";
        info.el.style.color = "#fff";
      },

      // Opens the create modal when a date is clicked.
      dateClick: function (info) {
        openCreateModal(info.dateStr);
      },

      // Opens the detail modal when an event is clicked.
      eventClick: function (info) {
        openDetailModal(info.event);
      },
    });

    calendar.render();

    // --- Modal and Form Elements for the Local Calendar ---
    const modal = document.getElementById("goaltaskModal");
    const closeBtn = document.getElementById("closeModalBtn");
    const saveBtn = document.getElementById("saveTaskBtn");
    const detailModal = document.getElementById("goalDetailsModal");
    const markCompletedBtn = document.getElementById("markCompletedBtn");
    const deleteGoalBtn = document.getElementById("deleteGoalBtn");
    const closeDetailBtn = document.getElementById("closeDetailBtn");
    let currentEvent = null; // Tracks the currently selected event.

    // --- Modal Functions ---
    function openCreateModal(dateStr) {
        // ... (Implementation for opening the create modal)
    }
    function closeCreateModal() {
        // ... (Implementation for closing the create modal)
    }
    function openDetailModal(event) {
        // ... (Implementation for opening the detail modal)
    }
    function closeDetailModal() {
        // ... (Implementation for closing the detail modal)
    }

    // --- Event Handlers for Local Calendar Actions ---
    if (saveBtn) {
        saveBtn.addEventListener("click", () => {
            // ... (Logic to save a new goal to localStorage)
        });
    }
    if (markCompletedBtn) {
        markCompletedBtn.addEventListener("click", () => {
            // ... (Logic to mark a goal as completed in localStorage)
        });
    }
    if (deleteGoalBtn) {
        deleteGoalBtn.addEventListener("click", () => {
            // ... (Logic to delete a goal from localStorage)
        });
    }
    
    // --- Helper Functions ---
    function getAllDates(start, end) {
        // ... (Helper to get all dates in a range)
    }
    function getNextDate(dateStr) {
        // ... (Helper to get the next day)
    }
  }
});
