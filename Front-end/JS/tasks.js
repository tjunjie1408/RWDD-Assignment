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
    const fileUploadModal = document.getElementById('fileUploadModal');
    const cancelUploadBtn = document.getElementById('cancelUploadBtn');

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
        taskForm.action = 'Config/create_task.php';
        fetchProjectMembers(); // Populate dropdown when modal is opened
        taskModal.style.display = 'flex';
    }

    function showEditTaskModal(card) {
        taskForm.reset();
        document.getElementById('projectId').value = projectId;
        document.getElementById('taskId').value = card.dataset.taskId;
        document.getElementById('taskTitle').value = card.dataset.title;
        document.getElementById('taskDescription').value = card.dataset.description;
        document.getElementById('taskEndDate').value = card.dataset.endDate;
        taskModalTitle.textContent = 'Edit Task';
        taskForm.action = 'Config/update_task.php';
        fetchProjectMembers();
        taskModal.style.display = 'flex';
    }

    function hideTaskModal() {
        taskModal.style.display = 'none';
    }

    function showUploadModal(taskId) {
        document.getElementById('uploadTaskId').value = taskId;
        fileUploadModal.style.display = 'flex';
    }

    function hideUploadModal() {
        fileUploadModal.style.display = 'none';
    }

    // --- EVENT LISTENERS ---
    if(newTaskBtn) newTaskBtn.addEventListener('click', showTaskModal);
    cancelTaskBtn.addEventListener('click', hideTaskModal);
    cancelUploadBtn.addEventListener('click', hideUploadModal);

    taskListContainer.addEventListener('click', (e) => {
        const target = e.target;
        const card = target.closest('.task-card');

        if (target.classList.contains('edit-task-btn')) {
            showEditTaskModal(card);
        }

        if (target.classList.contains('delete-task-btn')) {
            if (confirm('Are you sure you want to delete this task?')) {
                const taskId = card.dataset.taskId;
                window.location.href = `Config/delete_task.php?task_id=${taskId}&project_id=${projectId}`;
            }
        }

        if (target.classList.contains('btn-upload-file')) {
            const taskId = card.dataset.taskId;
            showUploadModal(taskId);
        }
    });
});