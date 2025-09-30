let tasks = [];

// 打开和关闭创建任务模态框
document.getElementById("newTaskBtn").addEventListener("click", () => {
  document.getElementById("modal").classList.add("show");
});

document.getElementById("cancelBtn").addEventListener("click", () => {
  closeModal();
});

function closeModal() {
  document.getElementById("modal").classList.remove("show");
}

// 创建任务
document.getElementById("createBtn").addEventListener("click", () => {
  const title = document.getElementById("taskTitle").value.trim();
  const category = document.getElementById("taskCategory").value;
  const dueDate = document.getElementById("taskDate").value;
  const progress = parseInt(document.getElementById("taskProgress").value);
  const description = document.getElementById("taskDescription").value.trim();

  if (!title || !dueDate) {
    alert("Title 和 Due Date 必须填写！");
    return;
  }

  const task = {
    title,
    category,
    dueDate,
    progress,
    description,
    status: getStatus(progress, dueDate),
    members: [] // 默认空，可以后续加上成员选择
  };

  tasks.push(task);
  closeModal();
  renderTasks();
});

// 根据进度和截止日自动判断状态
function getStatus(progress, dueDate) {
  const today = new Date().toISOString().split("T")[0];
  if (progress >= 100) return "completed";
  if (dueDate < today) return "overdue";
  return "progress";
}

// 渲染任务
function renderTasks() {
  const filterValue = document.getElementById("statusFilter").value;
  const container = document.getElementById("taskGroups");
  container.innerHTML = "";

  let filteredTasks = tasks;

  if (filterValue !== "all") {
    filteredTasks = tasks.filter(task => task.status === filterValue);
  }

  // 更新 Kanban 数字
  document.getElementById("inProgressCount").textContent = tasks.filter(t => t.status === "progress").length;
  document.getElementById("completedCount").textContent = tasks.filter(t => t.status === "completed").length;
  document.getElementById("overdueCount").textContent = tasks.filter(t => t.status === "overdue").length;

  // 渲染任务卡片
  filteredTasks.forEach(task => {
    const taskEl = document.createElement("div");
    taskEl.classList.add("task-card");

    taskEl.innerHTML = `
      <div class="task-header">
        <h4>${task.title}</h4>
        <span class="task-category">${task.category}</span>
      </div>
      <p class="task-desc">${task.description || "No description provided."}</p>
      <div class="task-footer">
        <span class="task-date">📅 ${task.dueDate}</span>
        <div class="progress-bar">
          <div class="progress-fill" style="width: ${task.progress}%; background: ${getProgressColor(task.progress)}"></div>
        </div>
        <span class="progress-text">${task.progress}%</span>
      </div>
    `;

    // 点击任务 → 打开详情弹窗
    taskEl.addEventListener("click", () => openTaskDetail(task));

    container.appendChild(taskEl);
  });
}

// 进度条颜色
function getProgressColor(percent) {
  if (percent <= 40) return "#ef4444";   // 红
  if (percent <= 70) return "#f97316";   // 橙
  return "#22c55e";                       // 绿
}

// 筛选器
document.getElementById("statusFilter").addEventListener("change", renderTasks);

// 搜索功能
const searchInput = document.getElementById("searchInput");
const taskGroups = document.getElementById("taskGroups");

searchInput.addEventListener("input", () => {
  const searchText = searchInput.value.toLowerCase();
  const tasks = taskGroups.getElementsByClassName("task-card");

  Array.from(tasks).forEach(task => {
    const text = task.innerText.toLowerCase();
    if (text.includes(searchText)) {
      task.style.display = "block";
    } else {
      task.style.display = "none";
    }
  });
});

// 打开任务详情弹窗
function openTaskDetail(task) {
  const modal = document.getElementById("taskDetailModal");

  document.getElementById("detailTitle").innerText = task.title;
  document.getElementById("detailCategory").innerText = task.category;
  document.getElementById("detailDate").innerText = task.dueDate;
  document.getElementById("detailProgress").innerText = task.progress;
  document.getElementById("detailDescription").innerText = task.description;

  // 渲染成员
  const membersList = document.getElementById("detailMembers");
  membersList.innerHTML = "";
  if (task.members && task.members.length > 0) {
    task.members.forEach(member => {
      const li = document.createElement("li");
      li.innerText = member;
      membersList.appendChild(li);
    });
  } else {
    membersList.innerHTML = "<li>No members assigned</li>";
  }

  modal.classList.add("show");
}

// 关闭任务详情弹窗
document.getElementById("closeDetailBtn").addEventListener("click", () => {
  document.getElementById("taskDetailModal").classList.remove("show");
});

// === 提交工作内容表单 ===
const submissionForm = document.getElementById("submissionForm");

if (submissionForm) {
  submissionForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const desc = document.getElementById("submissionDescription").value;
    const file = document.getElementById("submissionFile").files[0];

    if (!file) {
      alert("Please upload a file.");
      return;
    }

    console.log("Work Submitted:");
    console.log("Description:", desc);
    console.log("File:", file.name);

    alert("Your work has been submitted!");

    submissionForm.reset();
  });
}