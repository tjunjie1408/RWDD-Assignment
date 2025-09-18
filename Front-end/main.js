function closeAllDropdowns() {
  // 以后如果有下拉菜单，可以在这里写关闭逻辑
}

// ===== Sidebar =====
const sidebar = document.querySelector(".sidebar");
const sidebarMenuBtn = document.querySelector(".sidebar-menu-button");
const sidebarToggler = document.querySelector(".sidebar-toggler");

// 点击 sidebar-menu-button 或 sidebar-toggler
if (sidebarMenuBtn) {
  sidebarMenuBtn.addEventListener("click", () => {
    if (window.innerWidth <= 1024) {
      sidebar.classList.toggle("active");

      if (sidebar.classList.contains("active")) {
        sidebarMenuBtn.style.display = "none"; // 展开时隐藏按钮
      } else {
        sidebarMenuBtn.style.display = "flex"; // 收起时再显示按钮
      }
    }
  });
}

if (sidebarToggler) {
  sidebarToggler.addEventListener("click", () => {
    if (window.innerWidth <= 1024) {
      sidebar.classList.remove("active");
      sidebarMenuBtn.style.display = "flex"; // 收起时显示按钮
    }
  });
}

// 窗口大小变化时
window.addEventListener("resize", () => {
  if (window.innerWidth > 1024) {
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none"; // 大屏幕隐藏按钮
  } else {
    sidebar.classList.remove("active");
    if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex"; // 小屏幕默认显示按钮
  }
});

// 初始状态
if (window.innerWidth <= 1024) {
  sidebar.classList.remove("active");
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "flex";
} else {
  if (sidebarMenuBtn) sidebarMenuBtn.style.display = "none";
}

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

// ===== Real Time Clock =====
function updateClock() {
  const clock = document.getElementById("realTimeClock");
  const now = new Date();

  // 获取年月日
  let day = now.getDate().toString().padStart(2, "0");
  let month = (now.getMonth() + 1).toString().padStart(2, "0"); // 月份从0开始
  let year = now.getFullYear();

  // 星期
  const weekdays = ["SUNDAY","MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURDAY"];
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
      right: ""
    },
    events: savedEvents,
    dateClick: function(info) {
      openCreateModal(info.dateStr);
    },
    eventClick: function(info) {
      openDetailModal(info.event);
    }
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
      const newEvent = {
        id: Date.now().toString(),
        title: title,
        description: details,
        start: startDate,
        end: endDate,
        completed: false
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
    if (todayStr >= currentEvent.startStr && todayStr <= currentEvent.endStr) {
      
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
      const allDates = getAllDates(currentEvent.startStr, currentEvent.endStr);
      const isAllCompleted = allDates.every(d => completedDates.includes(d));

      if (isAllCompleted) {
        currentEvent.setProp("color", "#20c997"); //! 变青色, error
      }

      // 更新到 localStorage
      savedEvents = savedEvents.map(ev =>
        ev.id === currentEvent.id
          ? { 
              ...ev, 
              completedDates: completedDates, 
              color: isAllCompleted ? "#20c997" : ev.color 
            }
          : ev
      );
      localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));
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
        savedEvents = savedEvents.filter(ev => ev.id !== currentEvent.id);
        localStorage.setItem("calendarEvents", JSON.stringify(savedEvents));

        closeDetailModal();
      }
    }
  });
});

// ===== Member Management =====
document.addEventListener("DOMContentLoaded", () => {
  const memberList = document.getElementById("memberList");
  const addMemberBtn = document.getElementById("addMemberBtn");
  const addMemberModal = document.getElementById("addMemberModal");
  const viewMemberModal = document.getElementById("viewMemberModal");
  const addMemberForm = document.getElementById("addMemberForm");
  const memberDetails = document.getElementById("memberDetails");
  const deleteMemberBtn = document.getElementById("deleteMemberBtn");
  const searchInput = document.getElementById("searchMember");

  let members = [];
  let selectedMemberIndex = null;

  // 打开 "Add Member" 弹窗
  addMemberBtn.addEventListener("click", () => {
    addMemberModal.style.display = "flex";
  });

  // 关闭所有弹窗
  document.querySelectorAll(".member-close").forEach(closeBtn => {
    closeBtn.addEventListener("click", () => {
      addMemberModal.style.display = "none";
      viewMemberModal.style.display = "none";
    });
  });

  // 保存新增成员
  addMemberForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = document.getElementById("memberName").value;
    const company = document.getElementById("memberCompany").value;
    const task = document.getElementById("memberTask").value;

    members.push({ name, company, task });
    renderMembers();
    addMemberForm.reset();
    addMemberModal.style.display = "none";
  });

  // 渲染成员列表
  function renderMembers(filter = "") {
    memberList.innerHTML = "";
    members
      .filter(member => 
        member.name.toLowerCase().includes(filter) ||
        member.company.toLowerCase().includes(filter) ||
        member.task.toLowerCase().includes(filter)
      )
      .forEach((member, index) => {
        const card = document.createElement("div");
        card.classList.add("member-card");
        card.innerHTML = `
          <div class="member-info">
            <strong>${member.name}</strong>
            <span>${member.company}</span>
          </div>
          <div class="member-task">
            <span>Task: ${member.task}</span>
          </div>
        `;
        card.addEventListener("click", () => {
          selectedMemberIndex = index;
          viewMember(member);
        });
        memberList.appendChild(card);
      });
  }

  // 搜索成员
  searchInput.addEventListener("input", (e) => {
    const filter = e.target.value.toLowerCase();
    renderMembers(filter);
  });

  // 查看成员详细资料
  function viewMember(member) {
    memberDetails.innerHTML = `
      <p><strong>Name:</strong> ${member.name}</p>
      <p><strong>Company:</strong> ${member.company}</p>
      <p><strong>Previous Task:</strong> ${member.task}</p>
    `;
    viewMemberModal.style.display = "flex";
  }

  // 删除成员
  deleteMemberBtn.addEventListener("click", () => {
    if (selectedMemberIndex !== null) {
      members.splice(selectedMemberIndex, 1);
      renderMembers();
      viewMemberModal.style.display = "none";
    }
  });
});
