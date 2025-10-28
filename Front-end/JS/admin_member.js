document.addEventListener('DOMContentLoaded', () => {
    const addMemberBtn = document.getElementById('addMemberBtn');
    const memberFormModal = document.getElementById('memberFormModal');
    const memberFormModalTitle = document.getElementById('memberFormModalTitle');
    const memberForm = document.getElementById('memberForm');
    const memberIdInput = document.getElementById('memberId');
    const memberNameInput = document.getElementById('memberName');
    const memberEmailInput = document.getElementById('memberEmail');
    const memberPasswordInput = document.getElementById('memberPassword');
    const memberCompanyInput = document.getElementById('memberCompany');
    const memberPositionInput = document.getElementById('memberPosition');
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

    let allMembers = []; // Store all members for searching
    let currentMemberId = null; // To keep track of the member being viewed/edited/deleted

    // Function to fetch and display members
    async function fetchAndDisplayMembers() {
        try {
            const response = await fetch('Config/get_all_users.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                allMembers = data.members;
                renderMembers(allMembers);
            } else {
                console.error('Failed to fetch members:', data.error);
                memberList.innerHTML = '<p>Error loading members. Please try again later.</p>';
            }
        } catch (error) {
            console.error('Error fetching members:', error);
            memberList.innerHTML = '<p>Error loading members. Please try again later.</p>';
        }
    }

    // Function to render member cards
    function renderMembers(membersToRender) {
        memberList.innerHTML = ''; // Clear existing members
        if (membersToRender.length === 0) {
            memberList.innerHTML = '<p>No members found.</p>';
            return;
        }

        membersToRender.forEach(member => {
            const memberCard = document.createElement('div');
            memberCard.classList.add('member-card');
            memberCard.dataset.memberId = member.user_ID;
            const avatarSrc = member.avatar_url ? member.avatar_url : 'https://via.placeholder.com/50';
            memberCard.innerHTML = `
                <img src="${avatarSrc}" alt="${member.username}" class="member-avatar">
                <div class="member-info">
                    <h4>${member.username}</h4>
                    <p>${member.position} at ${member.company}</p>
                </div>
            `;
            memberCard.addEventListener('click', () => showMemberDetails(member.user_ID));
            memberList.appendChild(memberCard);
        });
    }

    // Function to show member details in a modal
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
                viewMemberModal.style.display = 'block';
            } else {
                alert('Failed to fetch member details: ' + data.error);
            }
        } catch (error) {
            console.error('Error fetching member details:', error);
            alert('An error occurred while fetching member details.');
        }
    }

    // Open Add Member Modal
    addMemberBtn.addEventListener('click', () => {
        memberForm.reset(); // Clear form fields
        memberIdInput.value = ''; // Ensure no ID is set for new member
        memberPasswordInput.required = true; // Password is required for new member
        memberFormModalTitle.textContent = 'Add Member';
        memberFormSubmitBtn.textContent = 'Add Member';
        memberFormModal.style.display = 'block';
    });

    // Open Edit Member Modal
    editMemberBtn.addEventListener('click', async () => {
        if (!currentMemberId) return;

        try {
            const response = await fetch(`Config/admin_get_member_details.php?id=${currentMemberId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                const member = data.member;
                memberIdInput.value = member.user_ID;
                memberNameInput.value = member.username;
                memberEmailInput.value = member.email;
                memberCompanyInput.value = member.company;
                memberPositionInput.value = member.position;
                memberPasswordInput.value = ''; // Clear password field for security, user can set new one
                memberPasswordInput.required = false; // Password is optional for edit
                memberFormModalTitle.textContent = 'Edit Member';
                memberFormSubmitBtn.textContent = 'Save Changes';
                viewMemberModal.style.display = 'none'; // Close view modal
                memberFormModal.style.display = 'block'; // Open edit modal
            } else {
                alert('Failed to load member for editing: ' + data.error);
            }
        } catch (error) {
            console.error('Error loading member for editing:', error);
            alert('An error occurred while loading member for editing.');
        }
    });

    // Handle Add/Edit Member Form Submission
    memberForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(memberForm);
        const url = memberIdInput.value ? 'Config/admin_update_member.php' : 'Config/admin_add_member.php';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert(memberIdInput.value ? 'Member updated successfully!' : 'Member added successfully!');
                memberFormModal.style.display = 'none';
                fetchAndDisplayMembers(); // Refresh the list
            } else {
                alert('Operation failed: ' + data.error);
            }
        } catch (error) {
            console.error('Error submitting member form:', error);
            alert('An error occurred during the operation.');
        }
    });

    // Delete Member (show confirmation modal first)
    deleteMemberBtn.addEventListener('click', () => {
        if (currentMemberId) {
            confirmDeleteModal.style.display = 'block';
        }
    });

    confirmDeleteYesBtn.addEventListener('click', async () => {
        if (!currentMemberId) return;

        try {
            const formData = new FormData();
            formData.append('memberId', currentMemberId);

            const response = await fetch('Config/admin_delete_member.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert('Member deleted successfully!');
                viewMemberModal.style.display = 'none';
                confirmDeleteModal.style.display = 'none';
                currentMemberId = null;
                fetchAndDisplayMembers(); // Refresh the list
            } else {
                alert('Deletion failed: ' + data.error);
            }
        } catch (error) {
            console.error('Error deleting member:', error);
            alert('An error occurred during deletion.');
        }
    });

    confirmDeleteNoBtn.addEventListener('click', () => {
        confirmDeleteModal.style.display = 'none';
    });

    // Close modals
    closeFormModalBtn.addEventListener('click', () => {
        memberFormModal.style.display = 'none';
    });

    closeViewModalBtn.addEventListener('click', () => {
        viewMemberModal.style.display = 'none';
    });

    // Close modals if clicked outside
    window.addEventListener('click', (event) => {
        if (event.target == memberFormModal) {
            memberFormModal.style.display = 'none';
        }
        if (event.target == viewMemberModal) {
            viewMemberModal.style.display = 'none';
        }
        if (event.target == confirmDeleteModal) {
            confirmDeleteModal.style.display = 'none';
        }
    });

    // Search functionality
    searchMemberInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredMembers = allMembers.filter(member =>
            member.username.toLowerCase().includes(searchTerm) ||
            member.company.toLowerCase().includes(searchTerm) ||
            member.position.toLowerCase().includes(searchTerm)
        );
        renderMembers(filteredMembers);
    });

    // Initial fetch
    fetchAndDisplayMembers();
});