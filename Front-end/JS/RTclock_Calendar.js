function closeAllDropdowns() {
  // 以后如果有下拉菜单，可以在这里写关闭逻辑
}

// ===== Real Time Clock =====
function updateClock() {
  const clock = document.getElementById("realTimeClock");
  const now = new Date();
  

  // 获取年月日
  let day = now.getDate().toString().padStart(2, "0");
  let month = (now.getMonth() + 1).toString().padStart(2, "0"); // 月份从0开始
  let year = now.getFullYear();

  // 星期
  const weekdays = [
    "SUNDAY",
    "MONDAY",
    "TUESDAY",
    "WEDNESDAY",
    "THURSDAY",
    "FRIDAY",
    "SATURDAY",
  ];
  let weekday = weekdays[now.getDay()];

  // 时间
  let hours = now.getHours().toString().padStart(2, "0");
  let minutes = now.getMinutes().toString().padStart(2, "0");
  let seconds = now.getSeconds().toString().padStart(2, "0");

  // 拼接
  const currentTime = `${day}/${month}/${year} (${weekday})   ${hours}:${minutes}:${seconds}`;

  // 显示
  if (clock) {
    clock.textContent = currentTime;
  }
}

// 页面加载时先跑一次
updateClock();
// 每秒更新
setInterval(updateClock, 1000);

document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");

  // 从 localStorage 获取保存的 events
  let savedEvents = JSON.parse(localStorage.getItem("calendarEvents")) || [];

  // 初始化 FullCalendar
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    editable: false,
    selectable: true,
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "",
    },
    events: function(fetchInfo, successCallback) {
  successCallback(savedEvents);
},
    //* New: Adding CSS classes based on event attributes
    eventClassNames: function (arg) {
      if (arg.event.extendedProps.isCompleted) {
        return ["completed-event"];
      }
      return [];
    },
    //* New: Rendering of custom event content
    eventDidMount: function (info) {
      if (info.event.extendedProps.isCompleted) {
        info.el.style.backgroundColor = "#808080";
        info.el.style.borderColor = "#808080";
        info.el.style.color = "#fff";
        info.el.style.textDecoration = "line-through";
        info.el.style.opacity = "0.7";
      } else {
        info.el.style.backgroundColor = "";
        info.el.style.borderColor = "";
        info.el.style.color = "";
        info.el.style.textDecoration = "none";
        info.el.style.opacity = "1";
      }
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
    document.getElementById("taskStart").value = dateStr || "";
  }

  function closeCreateModal() {
    modal.classList.add("hidden");
  }

  closeBtn.addEventListener("click", closeCreateModal);

  saveBtn.addEventListener("click", () => {
    const title = document.getElementById("taskTitle").value;
    const details = document.getElementById("taskDetails").value;
    const startDate = document.getElementById("taskStart").value;
    const endDate = document.getElementById("taskEnd").value;

    if (title && startDate && endDate) {
    const endDateAdjusted = new Date(endDate);
    endDateAdjusted.setDate(endDateAdjusted.getDate() + 1);
    const endDateStr = endDateAdjusted.toISOString().split("T")[0];

    const newEvent = {
      id: Date.now().toString(),
      title: title,
      description: details,
      start: startDate,
      end: endDateStr,  // Now exclusive end is one day after
      completedDates: [],
      isCompleted: false,
    };

      calendar.addEvent(newEvent);
      savedEvents.push(newEvent);
      localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));

      closeCreateModal();

      // 清空输入
      document.getElementById("taskTitle").value = "";
      document.getElementById("taskDetails").value = "";
      document.getElementById("taskStart").value = "";
      document.getElementById("taskEnd").value = "";
    } else {
      alert("Please enter goal name, details and dates!");
    }
  });

  // ===== Details Modal =====
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
    detailDescription.textContent = event.extendedProps.description || "";
    detailStart.textContent = event.startStr;
    detailEnd.textContent = event.endStr || event.startStr;

    // 判断日期是否可以完成
    const today = new Date().toISOString().split("T")[0];
    if (event.startStr <= today) {
      markCompletedBtn.disabled = false;
    } else {
      markCompletedBtn.disabled = true;
    }

    detailModal.classList.remove("hidden");
  }

  function closeDetailModal() {
    detailModal.classList.add("hidden");
    currentEvent = null;
  }

  closeDetailBtn.addEventListener("click", closeDetailModal);

  // 用户点击完成某天
  markCompletedBtn.addEventListener("click", () => {
    if (currentEvent) {
      const todayStr = new Date().toISOString().split("T")[0];

      // 确认今天在 start~end 范围内，并且不是未来
      if (
        todayStr >= currentEvent.startStr &&
        todayStr <= currentEvent.endStr
      ) {
        // 如果事件还没有 completedDates，就新建
        if (!currentEvent.extendedProps.completedDates) {
          currentEvent.setExtendedProp("completedDates", []);
        }

        let completedDates = currentEvent.extendedProps.completedDates;

        //如果今天还没完成，就 push
        if (!completedDates.includes(todayStr)) {
          completedDates.push(todayStr);
          currentEvent.setExtendedProp("completedDates", completedDates);
        }

        // 检查是否所有日期都完成
        const allDates = getAllDates(
          currentEvent.startStr,
          currentEvent.endStr
        );
        const isAllCompleted = allDates.every((d) =>
          completedDates.includes(d)
        );

        if (isAllCompleted) {
          currentEvent.setExtendedProp("isCompleted", true);
        } else {
          currentEvent.setExtendedProp("isCompleted", false);
        }

        // 更新到 localStorage
        savedEvents = savedEvents.map((ev) =>
          ev.id === currentEvent.id
            ? {
                ...ev,
                completedDates: completedDates,
                isCompleted: isAllCompleted,
              }
            : ev
        );
        localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));
        calendar.refetchEvents();
      }
      closeDetailModal();
    }
  });


  // 工具函数：获取两个日期之间的所有日期
  function getAllDates(start, end) {
    let dateArray = [];
    let currentDate = new Date(start);
    const endDate = new Date(end);

    while (currentDate <= endDate) {
      dateArray.push(currentDate.toISOString().split("T")[0]);
      currentDate.setDate(currentDate.getDate() + 1);
    }
    return dateArray;
  }

  // 删除事件
  deleteGoalBtn.addEventListener("click", () => {
    if (currentEvent) {
      if (confirm("Are you sure you want to delete this goal?")) {
        currentEvent.remove();

        // 从 localStorage 删除
        savedEvents = savedEvents.filter((ev) => ev.id !== currentEvent.id);
        localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));

        closeDetailModal();
      }
    }
  });
});
