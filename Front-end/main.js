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

