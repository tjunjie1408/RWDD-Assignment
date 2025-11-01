// This script handles the functionality for a "view-only" member page,
// where users can see a list of other members but cannot edit or delete them.
document.addEventListener('DOMContentLoaded', () => {
    // --- Element References ---
    const memberList = document.getElementById('memberList');
    const searchMemberInput = document.getElementById('searchMember');
    const viewMemberModal = document.getElementById('viewMemberModal');
    const memberDetailsDiv = document.getElementById('memberDetails');
    const closeViewModalBtn = viewMemberModal.querySelector('.member-close');

    // --- State Variable ---
    let allMembers = []; // Caches the full list of members for client-side searching and viewing.

    // --- Core Functions ---

    /**
     * Fetches the list of all users from the server and triggers the rendering process.
     */
    async function fetchAndDisplayMembers() {
        try {
            const response = await fetch('Config/get_all_users.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                allMembers = data.members; // Store the fetched data.
                renderMembers(allMembers); // Render the initial list.
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
        memberList.innerHTML = ''; // Clear the list first.
        if (membersToRender.length === 0) {
            memberList.innerHTML = '<p>No members found.</p>';
            return;
        }

        membersToRender.forEach(member => {
            const memberCard = document.createElement('div');
            memberCard.classList.add('member-card');
            memberCard.dataset.memberId = member.user_ID;
            const avatarSrc = member.avatar_url || 'https://via.placeholder.com/50';
            memberCard.innerHTML = `
                <img src="${avatarSrc}" alt="${member.username}" class="member-avatar">
                <div class="member-info">
                    <h4>${member.username}</h4>
                    <p>${member.position} at ${member.company}</p>
                </div>
            `;
            // Adds a click listener to show details when a card is clicked.
            memberCard.addEventListener('click', () => showMemberDetails(member.user_ID));
            memberList.appendChild(memberCard);
        });
    }

    /**
     * Displays the details of a specific member in a modal.
     * This version is optimized to use the already-fetched `allMembers` array, avoiding extra server requests.
     * @param {number} memberId - The ID of the member to display.
     */
    function showMemberDetails(memberId) {
        // Finds the member data from the local cache.
        const member = allMembers.find(m => m.user_ID == memberId);

        if (member) {
            memberDetailsDiv.innerHTML = `
                <p><strong>Username:</strong> ${member.username}</p>
                <p><strong>Email:</strong> ${member.email}</p>
                <p><strong>Company:</strong> ${member.company}</p>
                <p><strong>Position:</strong> ${member.position}</p>
            `;
            viewMemberModal.style.display = 'block'; // Shows the modal.
        } else {
            alert('Could not find member details.');
        }
    }

    // --- Event Listeners ---

    // Closes the view member modal.
    closeViewModalBtn.addEventListener('click', () => {
        viewMemberModal.style.display = 'none';
    });

    // Filters the displayed members based on the search input in real-time.
    searchMemberInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredMembers = allMembers.filter(member =>
            member.username.toLowerCase().includes(searchTerm) ||
            member.company.toLowerCase().includes(searchTerm) ||
            member.position.toLowerCase().includes(searchTerm)
        );
        renderMembers(filteredMembers);
    });

    // --- Initial Load ---
    // Fetches and displays the members when the page first loads.
    fetchAndDisplayMembers();
});