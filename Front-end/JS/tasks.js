document.addEventListener('DOMContentLoaded', () => {
    // Main container
    const taskListContainer = document.getElementById('task-list-container');
    const projectId = new URLSearchParams(window.location.search).get('project_id');

    // Modal elements
    const taskModal = document.getElementById('taskModal');
    const taskForm = document.getElementById('taskForm');
    const taskModalTitle = document.getElementById('taskModalTitle');
    const cancelTaskBtn = document.getElementById('cancelTaskBtn');
    const newTaskBtn = document.getElementById('newTaskBtn');
    const assigneeSelect = document.getElementById('assignee');

    if (!projectId) {
        taskListContainer.innerHTML = '<p>No project selected.</p>';
        return;
    }

    async function fetchProjectMembers() {
        try {
            const response = await fetch(`Config/get_project_members.php?project_id=${projectId}`);
            const data = await response.json();
            if (data.success) {
                populateAssigneeDropdown(data.members);
            } else {
                console.error('Could not fetch project members.');
            }
        } catch (error) {
            console.error('Error fetching project members:', error);
        }
    }

    function populateAssigneeDropdown(members) {
        assigneeSelect.innerHTML = '<option value="">Select a member</option>';
        members.forEach(member => {
            const option = document.createElement('option');
            option.value = member.user_ID;
            option.textContent = member.username;
            assigneeSelect.appendChild(option);
        });
    }

    // --- MODAL HANDLING ---
    function showTaskModal() {
        taskForm.reset();
        document.getElementById('projectId').value = projectId; // Ensure project ID is set
        taskModalTitle.textContent = 'New Task';
        fetchProjectMembers(); // Populate dropdown when modal is opened
        taskModal.style.display = 'flex';
    }

    function hideTaskModal() {
        taskModal.style.display = 'none';
    }

    // --- EVENT LISTENERS ---
    newTaskBtn.addEventListener('click', showTaskModal);
    cancelTaskBtn.addEventListener('click', hideTaskModal);




});