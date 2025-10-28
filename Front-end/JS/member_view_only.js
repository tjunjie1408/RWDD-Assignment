document.addEventListener('DOMContentLoaded', () => {
    const memberList = document.getElementById('memberList');
    const searchMemberInput = document.getElementById('searchMember');
    const viewMemberModal = document.getElementById('viewMemberModal');
    const memberDetailsDiv = document.getElementById('memberDetails');
    const closeViewModalBtn = viewMemberModal.querySelector('.member-close');

    let allMembers = []; // Store all members for searching

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
        // Find the member data from the already fetched allMembers array
        const member = allMembers.find(m => m.user_ID == memberId);

        if (member) {
            memberDetailsDiv.innerHTML = `
                <p><strong>Username:</strong> ${member.username}</p>
                <p><strong>Email:</strong> ${member.email}</p>
                <p><strong>Company:</strong> ${member.company}</p>
                <p><strong>Position:</strong> ${member.position}</p>
            `;
            viewMemberModal.style.display = 'block';
        } else {
            alert('Could not find member details.');
        }
    }

    // Close view member modal
    closeViewModalBtn.addEventListener('click', () => {
        viewMemberModal.style.display = 'none';
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