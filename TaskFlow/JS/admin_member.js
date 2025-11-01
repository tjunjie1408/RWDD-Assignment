// This script handles all the interactive functionality for the admin member management page.
document.addEventListener('DOMContentLoaded', () => {
    // --- Element References ---
    // Getting references to all necessary DOM elements for member management.
    const addMemberBtn = document.getElementById('addMemberBtn');
    const memberFormModal = document.getElementById('memberFormModal');
    const memberFormModalTitle = document.getElementById('memberFormModalTitle');
    const memberForm = document.getElementById('memberForm');
    const memberIdInput = document.getElementById('memberId');
    const memberPasswordInput = document.getElementById('memberPassword');
    const memberFormSubmitBtn = document.getElementById('memberFormSubmitBtn');
    const memberList = document.getElementById('memberList');
    const searchMemberInput = document.getElementById('searchMember');
    const viewMemberModal = document.getElementById('viewMemberModal');
    const memberDetailsDiv = document.getElementById('memberDetails');
    const editMemberBtn = document.getElementById('editMemberBtn');
    const deleteMemberBtn = document.getElementById('deleteMemberBtn');
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const confirmDeleteYesBtn = document.getElementById('confirmDeleteYes');
    const confirmDeleteNoBtn = document.getElementById('confirmDeleteNo');
    const closeFormModalBtn = memberFormModal.querySelector('.member-close');
    const closeViewModalBtn = viewMemberModal.querySelector('.member-close');

    // --- State Variables ---
    let allMembers = []; // Caches the full list of members for client-side searching.
    let currentMemberId = null; // Tracks the ID of the member currently being viewed or edited.

    // --- Core Functions ---

    /**
     * Fetches the list of all users from the server and triggers rendering.
     */
    async function fetchAndDisplayMembers() {
        try {
            const response = await fetch('Config/get_all_users.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                allMembers = data.members; // Store the fetched members.
                renderMembers(allMembers); // Render them on the page.
            } else {
                console.error('Failed to fetch members:', data.error);
                memberList.innerHTML = '<p>Error loading members. Please try again later.</p>';
            }
        } catch (error) {
            console.error('Error fetching members:', error);
            memberList.innerHTML = '<p>Error loading members. Please try again later.</p>';
        }
    }

    /**
     * Renders a list of member cards into the DOM.
     * @param {Array} membersToRender - The array of member objects to display.
     */
    function renderMembers(membersToRender) {
        memberList.innerHTML = ''; // Clear the list before rendering.
        if (membersToRender.length === 0) {
            memberList.innerHTML = '<p>No members found.</p>';
            return;
        }

        membersToRender.forEach(member => {
            const memberCard = document.createElement('div');
            memberCard.classList.add('member-card');
            memberCard.dataset.memberId = member.user_ID;
            // Use Gravatar URL or a placeholder for the avatar.
            const avatarSrc = member.avatar_url ? member.avatar_url : 'https://via.placeholder.com/50';
            memberCard.innerHTML = `
                <img src="${avatarSrc}" alt="${member.username}" class="member-avatar">
                <div class="member-info">
                    <h4>${member.username}</h4>
                    <p>${member.position} at ${member.company}</p>
                </div>
            `;
            // Add a click event to each card to show detailed view.
            memberCard.addEventListener('click', () => showMemberDetails(member.user_ID));
            memberList.appendChild(memberCard);
        });
    }

    /**
     * Fetches and displays the details of a specific member in a modal.
     * @param {number} memberId - The ID of the member to display.
     */
    async function showMemberDetails(memberId) {
        currentMemberId = memberId;
        try {
            const response = await fetch(`Config/admin_get_member_details.php?id=${memberId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                const member = data.member;
                memberDetailsDiv.innerHTML = `
                    <p><strong>Username:</strong> ${member.username}</p>
                    <p><strong>Email:</strong> ${member.email}</p>
                    <p><strong>Company:</strong> ${member.company}</p>
                    <p><strong>Position:</strong> ${member.position}</p>
                `;
                viewMemberModal.style.display = 'block'; // Show the modal.
            } else {
                alert('Failed to fetch member details: ' + data.error);
            }
        } catch (error) {
            console.error('Error fetching member details:', error);
            alert('An error occurred while fetching member details.');
        }
    }

    // --- Event Listeners ---

    // Opens the 'Add Member' modal.
    addMemberBtn.addEventListener('click', () => {
        memberForm.reset();
        memberIdInput.value = ''; // Clear ID for 'add' mode.
        memberPasswordInput.required = true; // Password is required for new members.
        memberFormModalTitle.textContent = 'Add Member';
        memberFormSubmitBtn.textContent = 'Add Member';
        memberFormModal.style.display = 'block';
    });

    // Fetches member data and opens the 'Edit Member' modal.
    editMemberBtn.addEventListener('click', async () => {
        if (!currentMemberId) return;
        try {
            const response = await fetch(`Config/admin_get_member_details.php?id=${currentMemberId}`);
            const data = await response.json();
            if (data.success) {
                const member = data.member;
                // Populate the form with the member's existing data.
                memberIdInput.value = member.user_ID;
                document.getElementById('memberName').value = member.username;
                document.getElementById('memberEmail').value = member.email;
                document.getElementById('memberCompany').value = member.company;
                document.getElementById('memberPosition').value = member.position;
                memberPasswordInput.value = ''; // Password field is cleared for security.
                memberPasswordInput.required = false; // Password is not required for updates.
                memberFormModalTitle.textContent = 'Edit Member';
                memberFormSubmitBtn.textContent = 'Save Changes';
                viewMemberModal.style.display = 'none';
                memberFormModal.style.display = 'block';
            } else {
                alert('Failed to load member for editing: ' + data.error);
            }
        } catch (error) {
            console.error('Error loading member for editing:', error);
            alert('An error occurred while loading member for editing.');
        }
    });

    // Handles the submission for both adding and editing a member.
    memberForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(memberForm);
        // The URL depends on whether we are adding (no ID) or updating (has ID).
        const url = memberIdInput.value ? 'Config/admin_update_member.php' : 'Config/admin_add_member.php';

        try {
            const response = await fetch(url, { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                alert(memberIdInput.value ? 'Member updated successfully!' : 'Member added successfully!');
                memberFormModal.style.display = 'none';
                fetchAndDisplayMembers(); // Refresh the member list.
            } else {
                alert('Operation failed: ' + data.error);
            }
        } catch (error) {
            console.error('Error submitting member form:', error);
            alert('An error occurred during the operation.');
        }
    });

    // Shows the delete confirmation modal.
    deleteMemberBtn.addEventListener('click', () => {
        if (currentMemberId) {
            confirmDeleteModal.style.display = 'block';
        }
    });

    // Handles the actual deletion after confirmation.
    confirmDeleteYesBtn.addEventListener('click', async () => {
        if (!currentMemberId) return;
        try {
            const formData = new FormData();
            formData.append('memberId', currentMemberId);
            const response = await fetch('Config/admin_delete_member.php', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                alert('Member deleted successfully!');
                viewMemberModal.style.display = 'none';
                confirmDeleteModal.style.display = 'none';
                currentMemberId = null;
                fetchAndDisplayMembers(); // Refresh the list.
            } else {
                alert('Deletion failed: ' + data.error);
            }
        } catch (error) {
            console.error('Error deleting member:', error);
            alert('An error occurred during deletion.');
        }
    });

    // Hides the delete confirmation modal.
    confirmDeleteNoBtn.addEventListener('click', () => {
        confirmDeleteModal.style.display = 'none';
    });

    // --- Modal Closing Logic ---
    closeFormModalBtn.addEventListener('click', () => memberFormModal.style.display = 'none');
    closeViewModalBtn.addEventListener('click', () => viewMemberModal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target == memberFormModal) memberFormModal.style.display = 'none';
        if (event.target == viewMemberModal) viewMemberModal.style.display = 'none';
        if (event.target == confirmDeleteModal) confirmDeleteModal.style.display = 'none';
    });

    // --- Search Functionality ---
    searchMemberInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        // Filters the cached member list based on the search term.
        const filteredMembers = allMembers.filter(member =>
            member.username.toLowerCase().includes(searchTerm) ||
            member.company.toLowerCase().includes(searchTerm) ||
            member.position.toLowerCase().includes(searchTerm)
        );
        renderMembers(filteredMembers); // Renders only the filtered members.
    });

    // --- Initial Load ---
    fetchAndDisplayMembers();
});