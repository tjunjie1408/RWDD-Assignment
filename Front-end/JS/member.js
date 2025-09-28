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
  document.querySelectorAll(".member-close").forEach((closeBtn) => {
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
      .filter(
        (member) =>
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
