function closeAllDropdowns() {
  // Future dropdown logic here
}

// ===== Real Time Clock =====
function updateClock() {
  const clock = document.getElementById("realTimeClock");
  if (!clock) return;

  const now = new Date();
  const day = now.getDate().toString().padStart(2, "0");
  const month = (now.getMonth() + 1).toString().padStart(2, "0");
  const year = now.getFullYear();

  const weekdays = [
    "SUNDAY", "MONDAY", "TUESDAY", "WEDNESDAY",
    "THURSDAY", "FRIDAY", "SATURDAY"
  ];
  const weekday = weekdays[now.getDay()];

  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  const seconds = now.getSeconds().toString().padStart(2, "0");

  clock.textContent = `${day}/${month}/${year} (${weekday})   ${hours}:${minutes}:${seconds}`;
}
updateClock();
setInterval(updateClock, 1000);

document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  let savedEvents = JSON.parse(localStorage.getItem("calendarEvents")) || [];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    editable: false,
    selectable: true,
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "",
    },
    events: savedEvents,

    eventDidMount: function (info) {
      const completed = info.event.extendedProps.isCompleted;
      info.el.style.backgroundColor = completed ? "#28a745" : "#808080"; // green or gray
      info.el.style.borderColor = completed ? "#28a745" : "#808080";
      info.el.style.color = "#fff";
    },

    dateClick: function (info) {
      openCreateModal(info.dateStr);
    },

    eventClick: function (info) {
      openDetailModal(info.event);
    },
  });

  calendar.render();

  // ===== Create Goal Modal =====
  const modal = document.getElementById("goaltaskModal");
  const closeBtn = document.getElementById("closeModalBtn");
  const saveBtn = document.getElementById("saveTaskBtn");

  function openCreateModal(dateStr) {
    modal.classList.remove("hidden");

    const today = new Date().toISOString().split("T")[0];
    const startInput = document.getElementById("taskStart");
    const endInput = document.getElementById("taskEnd");

    // Disable past dates
    startInput.min = today;
    endInput.min = today;

    startInput.value = dateStr || today;
    endInput.value = dateStr || today;
  }

  function closeCreateModal() {
    modal.classList.add("hidden");
  }
  closeBtn.addEventListener("click", closeCreateModal);

  saveBtn.addEventListener("click", () => {
    const title = document.getElementById("taskTitle").value.trim();
    const details = document.getElementById("taskDetails").value.trim();
    const startDate = document.getElementById("taskStart").value;
    const endDate = document.getElementById("taskEnd").value;

    if (!title) {
      alert("Please enter a goal name!");
      return;
    }
    if (!startDate || !endDate) {
      alert("Please select start and end dates!");
      return;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const start = new Date(startDate);
    start.setHours(0, 0, 0, 0);
    if (start < today) {
      alert("You cannot create goals with a start date in the past!");
      return;
    }

    if (new Date(endDate) < new Date(startDate)) {
      alert("End date cannot be before start date!");
      return;
    }

    // Generate individual day bars
    const goalId = Date.now().toString();
    const allDates = getAllDates(startDate, endDate);
    const newEvents = allDates.map((date) => ({
      id: `${goalId}-${date}`,
      title: title,
      description: details,
      start: date,
      end: getNextDate(date),
      goalId: goalId,
      isCompleted: false,
      totalDays: allDates.length,
    }));

    savedEvents.push(...newEvents);
    localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));

    calendar.addEventSource(newEvents);
    closeCreateModal();

    document.getElementById("taskTitle").value = "";
    document.getElementById("taskDetails").value = "";
    document.getElementById("taskStart").value = "";
    document.getElementById("taskEnd").value = "";
  });

  // ===== Goal Details Modal =====
  const detailModal = document.getElementById("goalDetailsModal");
  const detailTitle = document.getElementById("detailTitle");
  const detailDescription = document.getElementById("detailDescription");
  const detailStart = document.getElementById("detailStart");
  const detailEnd = document.getElementById("detailEnd");
  const markCompletedBtn = document.getElementById("markCompletedBtn");
  const deleteGoalBtn = document.getElementById("deleteGoalBtn");
  const closeDetailBtn = document.getElementById("closeDetailBtn");
  let currentEvent = null;

  function openDetailModal(event) {
    currentEvent = event;
    detailTitle.textContent = event.title;
    detailDescription.textContent = event.extendedProps.description || "No details";

    const goalEvents = savedEvents.filter((ev) => ev.goalId === event.extendedProps.goalId);
    const dates = goalEvents.map((ev) => ev.start);
    dates.sort();
    detailStart.textContent = dates[0];
    detailEnd.textContent = dates[dates.length - 1];

    const completed = goalEvents.filter((ev) => ev.isCompleted).length;
    const total = goalEvents.length;
    markCompletedBtn.textContent = `Mark as Completed (${completed}/${total})`;
    markCompletedBtn.disabled = event.extendedProps.isCompleted;

    detailModal.classList.remove("hidden");
  }

  function closeDetailModal() {
    detailModal.classList.add("hidden");
    currentEvent = null;
  }
  closeDetailBtn.addEventListener("click", closeDetailModal);

  // ===== Sequential completion enforcement =====
  markCompletedBtn.addEventListener("click", () => {
    if (!currentEvent) return;

    const goalId = currentEvent.extendedProps.goalId;
    const goalEvents = savedEvents
      .filter((ev) => ev.goalId === goalId)
      .sort((a, b) => new Date(a.start) - new Date(b.start));

    // Find the index of the current event
    const currentIndex = goalEvents.findIndex((ev) => ev.id === currentEvent.id);

    // Check if previous goal day is completed (if not the first)
    if (currentIndex > 0 && !goalEvents[currentIndex - 1].isCompleted) {
      alert("You must complete the previous day's goal before marking this one.");
      return;
    }

    // Mark this day complete
    currentEvent.setExtendedProp("isCompleted", true);
    savedEvents = savedEvents.map((ev) =>
      ev.id === currentEvent.id ? { ...ev, isCompleted: true } : ev
    );
    localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));

    calendar.removeAllEvents();
    calendar.addEventSource(savedEvents);

    const allDone = goalEvents.every((ev) => ev.isCompleted);

    closeDetailModal();

    if (allDone) {
      alert(`Goal "${currentEvent.title}" fully completed! 🎉`);
    } else {
      alert("Progress updated successfully ✅");
    }
  });

  // ===== Delete whole goal =====
  deleteGoalBtn.addEventListener("click", () => {
    if (!currentEvent) return;
    if (confirm("Are you sure you want to delete this goal?")) {
      const goalId = currentEvent.extendedProps.goalId;
      savedEvents = savedEvents.filter((ev) => ev.goalId !== goalId);
      localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));
      calendar.removeAllEvents();
      calendar.addEventSource(savedEvents);
      closeDetailModal();
    }
  });

  // ===== Helpers =====
  function getAllDates(start, end) {
    const dateArray = [];
    let currentDate = new Date(start);
    const endDate = new Date(end);
    while (currentDate <= endDate) {
      dateArray.push(currentDate.toISOString().split("T")[0]);
      currentDate.setDate(currentDate.getDate() + 1);
    }
    return dateArray;
  }

  function getNextDate(dateStr) {
    const d = new Date(dateStr);
    d.setDate(d.getDate() + 1);
    return d.toISOString().split("T")[0];
  }
});
