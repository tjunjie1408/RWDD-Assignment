// This script manages all the interactive elements on the project tasks page.
document.addEventListener('DOMContentLoaded', () => {
    // --- Element and State References ---
    const taskListContainer = document.getElementById('task-list-container');
    // Retrieves the project ID from the URL query parameters.
    const projectId = new URLSearchParams(window.location.search).get('project_id');

    // Modal elements for creating/editing tasks and uploading files.
    const taskModal = document.getElementById('taskModal');
    const taskForm = document.getElementById('taskForm');
    const taskModalTitle = document.getElementById('taskModalTitle');
    const cancelTaskBtn = document.getElementById('cancelTaskBtn');
    const newTaskBtn = document.getElementById('newTaskBtn');
    const assigneeSelect = document.getElementById('assignee');
    const fileUploadModal = document.getElementById('fileUploadModal');
    const cancelUploadBtn = document.getElementById('cancelUploadBtn');

    // If no project ID is found, display an error and stop execution.
    if (!projectId) {
        taskListContainer.innerHTML = '<p>No project selected.</p>';
        return;
    }

    // --- Core Functions ---

    /**
     * Fetches the list of members for the current project to populate the assignee dropdown.
     */
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

    /**
     * Populates the 'Assign To' dropdown menu with project members.
     * @param {Array} members - An array of member objects ({user_ID, username}).
     */
    function populateAssigneeDropdown(members) {
        assigneeSelect.innerHTML = '<option value="">Select a member</option>';
        members.forEach(member => {
            const option = document.createElement('option');
            option.value = member.user_ID;
            option.textContent = member.username;
            assigneeSelect.appendChild(option);
        });
    }

    // --- Modal Handling ---

    /**
     * Shows the task modal in 'create' mode.
     */
    function showTaskModal() {
        taskForm.reset();
        document.getElementById('projectId').value = projectId; // Pre-fill the project ID.
        taskModalTitle.textContent = 'New Task';
        taskForm.action = 'Config/create_task.php';
        fetchProjectMembers(); // Fetches members every time the modal is opened.
        taskModal.style.display = 'flex';
    }

    /**
     * Shows the task modal in 'edit' mode, populating it with existing task data.
     * @param {HTMLElement} card - The task card element containing the task data.
     */
    function showEditTaskModal(card) {
        taskForm.reset();
        // Populate the form with data stored in the card's data-* attributes.
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

    /**
     * Shows the file upload modal for a specific task.
     * @param {number} taskId - The ID of the task to upload a file for.
     */
    function showUploadModal(taskId) {
        document.getElementById('uploadTaskId').value = taskId;
        fileUploadModal.style.display = 'flex';
    }

    function hideUploadModal() {
        fileUploadModal.style.display = 'none';
    }

    // --- Event Listeners ---

    if (newTaskBtn) newTaskBtn.addEventListener('click', showTaskModal);
    if (cancelTaskBtn) cancelTaskBtn.addEventListener('click', hideTaskModal);
    if (cancelUploadBtn) cancelUploadBtn.addEventListener('click', hideUploadModal);

    // Uses event delegation to handle clicks on buttons within the task list.
    taskListContainer.addEventListener('click', (e) => {
        const target = e.target;
        const card = target.closest('.task-card');

        // Find which button was clicked within the card.
        const editBtn = target.closest('.edit-task-btn');
        const deleteBtn = target.closest('.delete-task-btn');
        const uploadBtn = target.closest('.btn-upload-file');

        if (editBtn) {
            showEditTaskModal(card);
        }

        if (deleteBtn) {
            if (confirm('Are you sure you want to delete this task?')) {
                const taskId = card.dataset.taskId;
                // Redirects to the delete script.
                window.location.href = `Config/delete_task.php?task_id=${taskId}&project_id=${projectId}`;
            }
        }

        if (uploadBtn) {
            const taskId = card.dataset.taskId;
            showUploadModal(taskId);
        }
    });
});