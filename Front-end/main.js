function closeAllDropdowns() {
    // 以后如果有下拉菜单，可以在这里写关闭逻辑
}

// 选择 sidebar
const sidebar = document.querySelector(".sidebar");
const toggleButtons = document.querySelectorAll(".sidebar-toggler, .sidebar-menu-button");

// 点击按钮（只在小屏幕生效）
toggleButtons.forEach(button => {
  button.addEventListener("click", () => {
    if (window.innerWidth <= 1024) {
      sidebar.classList.toggle("collapsed");
    }
  });
});

// 监听窗口大小变化
window.addEventListener("resize", () => {
  if (window.innerWidth > 1024) {
    // 大屏幕时保证 sidebar 永远是展开的
    sidebar.classList.remove("collapsed");
  }
});



// Collapse sidebar by default on smaller screens
if (window.innerWidth <= 1024) document.querySelector(".sidebar").classList.add("collapsed");