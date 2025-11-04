// This script appears to be for a version of the member page that uses local, in-memory data
// instead of fetching from the database. It might be outdated or for testing purposes.
document.addEventListener("DOMContentLoaded", () => {
  // --- Element References ---
  const memberList = document.getElementById("memberList");
  const addMemberBtn = document.getElementById("addMemberBtn");
  const addMemberModal = document.getElementById("addMemberModal");
  const viewMemberModal = document.getElementById("viewMemberModal");
  const addMemberForm = document.getElementById("addMemberForm");
  const memberDetails = document.getElementById("memberDetails");
  const deleteMemberBtn = document.getElementById("deleteMemberBtn");
  const searchInput = document.getElementById("searchMember");

  // --- Local State ---
  let members = []; // Holds the list of members in memory.
  let selectedMemberIndex = null; // Tracks the currently selected member.

  // --- Event Listeners ---

  // Opens the "Add Member" modal.
  if (addMemberBtn) {
    addMemberBtn.addEventListener("click", () => {
      addMemberModal.style.display = "flex";
    });
  }

  // Adds close functionality to all modal close buttons.
  document.querySelectorAll(".member-close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", () => {
      if (addMemberModal) addMemberModal.style.display = "none";
      if (viewMemberModal) viewMemberModal.style.display = "none";
    });
  });

  // Handles the form submission for adding a new member.
  if (addMemberForm) {
    addMemberForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = document.getElementById("memberName").value;
      const company = document.getElementById("memberCompany").value;
      const task = document.getElementById("memberTask").value;

      // Adds the new member to the local array and re-renders the list.
      members.push({ name, company, task });
      renderMembers();
      addMemberForm.reset();
      addMemberModal.style.display = "none";
    });
  }

  /**
   * Renders the list of members to the DOM, with an optional filter.
   * @param {string} filter - A string to filter the members by.
   */
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
        // When a card is clicked, show the member's details.
        card.addEventListener("click", () => {
          selectedMemberIndex = index;
          viewMember(member);
        });
        memberList.appendChild(card);
      });
  }

  // Adds a listener to the search input to filter the list in real-time.
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      const filter = e.target.value.toLowerCase();
      renderMembers(filter);
    });
  }

  /**
   * Displays the details of a selected member in a modal.
   * @param {object} member - The member object to display.
   */
  function viewMember(member) {
    memberDetails.innerHTML = `
      <p><strong>Name:</strong> ${member.name}</p>
      <p><strong>Company:</strong> ${member.company}</p>
      <p><strong>Previous Task:</strong> ${member.task}</p>
    `;
    viewMemberModal.style.display = "flex";
  }

  // Deletes the selected member from the local array.
  if (deleteMemberBtn) {
    deleteMemberBtn.addEventListener("click", () => {
      if (selectedMemberIndex !== null) {
        members.splice(selectedMemberIndex, 1);
        renderMembers();
        viewMemberModal.style.display = "none";
      }
    });
  }
});
