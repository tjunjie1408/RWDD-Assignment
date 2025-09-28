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