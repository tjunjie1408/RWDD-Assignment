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
  selectedMembers = [];
  renderMembers();
}

// 创建任务
document.getElementById("createBtn").addEventListener("click", () => {
  const title = document.getElementById("taskTitle").value.trim();
  const category = document.getElementById("taskCategory").value;
  const dueDate = document.getElementById("taskDate").value;
  const progress = parseInt(document.getElementById("taskProgress").value);
  const description = document.getElementById("taskDescription").value.trim();

  if (!title || !dueDate) {
    alert("Must fill up the Tittle and Due Date！");
    return;
  }

  const task = {
    title,
    category,
    dueDate,
    progress,
    description,
    status: getStatus(progress, dueDate),
    members: [...selectedMembers],
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
    filteredTasks = tasks.filter((task) => task.status === filterValue);
  }

  // 更新 Kanban 数字
  document.getElementById("inProgressCount").textContent = tasks.filter(
    (t) => t.status === "progress"
  ).length;
  document.getElementById("completedCount").textContent = tasks.filter(
    (t) => t.status === "completed"
  ).length;
  document.getElementById("overdueCount").textContent = tasks.filter(
    (t) => t.status === "overdue"
  ).length;

  // 渲染任务卡片
  filteredTasks.forEach((task, index) => {
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
    taskEl.addEventListener("click", () => openTaskDetail(task, index));

    container.appendChild(taskEl);
  });
}

// 进度条颜色
function getProgressColor(percent) {
  if (percent <= 40) return "#ef4444"; // 红
  if (percent <= 70) return "#f97316"; // 橙
  return "#22c55e"; // 绿
}

// 筛选器
document.getElementById("statusFilter").addEventListener("change", renderTasks);

// 搜索功能
const searchInput = document.getElementById("searchInput");
const taskGroups = document.getElementById("taskGroups");

searchInput.addEventListener("input", () => {
  const searchText = searchInput.value.toLowerCase();
  const tasks = taskGroups.getElementsByClassName("task-card");

  Array.from(tasks).forEach((task) => {
    const text = task.innerText.toLowerCase();
    task.style.display = text.includes(searchText) ? "block" : "none";
  });
});

// 打开任务详情弹窗
function openTaskDetail(task, index) {
  const modal = document.getElementById("taskDetailModal");

  document.getElementById("detailTitle").innerText = task.title;
  document.getElementById("detailCategory").innerText = task.category;
  document.getElementById("detailDate").innerText = task.dueDate;
  document.getElementById("detailProgress").innerText = task.progress;
  document.getElementById("detailDescription").innerText = task.description;

  // 渲染成员
  renderDetailMembers(task, index);

  // 打开详情弹窗
  modal.classList.add("show");

  // 编辑按钮
  const editBtn = document.getElementById("editTaskBtn");
  if (editBtn) editBtn.onclick = () => openEditModal(task, index);
}

// 渲染详情页成员（可删除 + 可新增）
function renderDetailMembers(task, index) {
  const membersList = document.getElementById("detailMembers");
  membersList.innerHTML = "";

  if (task.members && task.members.length > 0) {
    task.members.forEach((member, i) => {
      const li = document.createElement("li");
      li.innerHTML = `${member} <span class="remove-member" data-index="${i}" style="cursor:pointer;color:red;">×</span>`;
      membersList.appendChild(li);
    });

    // 删除成员
    membersList.querySelectorAll(".remove-member").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const memberIndex = e.target.getAttribute("data-index");
        task.members.splice(memberIndex, 1);
        renderDetailMembers(task, index);
        renderTasks();
      });
    });
  } else {
    membersList.innerHTML = "<li>No members assigned</li>";
  }

  // 绑定新增成员按钮
  const editAddBtn = document.getElementById("editAddMemberBtn");
  const editMemberInput = document.getElementById("editMemberInput");

  if (editAddBtn && editMemberInput) {
    editAddBtn.onclick = () => {
      const name = editMemberInput.value.trim();
      if (name === "") {
        alert("Please enter a member name.");
        return;
      }
      if (task.members.includes(name)) {
        alert("This member is already added.");
        return;
      }
      task.members.push(name);
      editMemberInput.value = "";
      renderDetailMembers(task, index);
      renderTasks();
    };
  }
}

// 关闭任务详情弹窗
document.getElementById("closeDetailBtn").addEventListener("click", () => {
  document.getElementById("taskDetailModal").classList.remove("show");
});

// ====== Add Member 功能 (Create Modal) ======
let selectedMembers = [];

const addMemberBtn = document.getElementById("addMemberBtn");
const memberInput = document.getElementById("memberInput");
const memberList = document.getElementById("memberList");

addMemberBtn.addEventListener("click", () => {
  const name = memberInput.value.trim();
  if (name === "") {
    alert("Please enter a member name.");
    return;
  }

  if (selectedMembers.includes(name)) {
    alert("This member is already added.");
    return;
  }

  selectedMembers.push(name);
  renderMembers();
  memberInput.value = "";
});

function renderMembers() {
  memberList.innerHTML = "";
  selectedMembers.forEach((member, index) => {
    const li = document.createElement("li");
    li.innerHTML = `${member} <span class="remove-member" data-index="${index}">&times;</span>`;
    memberList.appendChild(li);
  });

  document.querySelectorAll(".remove-member").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const index = e.target.getAttribute("data-index");
      selectedMembers.splice(index, 1);
      renderMembers();
    });
  });
}

// 假设每个任务有一个 submissions 数组
const exampleTask = {
  title: "Website Redesign",
  submissions: [
    {
      username: "Alice",
      description: "Homepage layout update",
      fileName: "homepage.pdf",
      fileUrl: "uploads/homepage.pdf"
    },
    {
      username: "Bob",
      description: "Added animation",
      fileName: "banner.mp4",
      fileUrl: "uploads/banner.mp4"
    }
  ]
};

// 显示文件列表
function showTaskDetail(task) {
  const submissionList = document.getElementById("submissionList");
  submissionList.innerHTML = "";

  if (!task.submissions || task.submissions.length === 0) {
    submissionList.innerHTML = "<p>No submissions yet.</p>";
  } else {
    task.submissions.forEach(s => {
      const div = document.createElement("div");
      div.classList.add("submission-item");
      div.innerHTML = `
        <p><strong>${s.username}</strong>: ${s.description}</p>
        <a href="${s.fileUrl}" target="_blank">${s.fileName}</a>
      `;
      submissionList.appendChild(div);
    });
  }
}
