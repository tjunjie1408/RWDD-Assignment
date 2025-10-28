document.addEventListener('DOMContentLoaded', () => {
    console.log("admin.js loaded and executing."); // For debugging

    // --- Modal Elements ---
    const projectModal = document.getElementById('projectModal');
    const editProjectModal = document.getElementById('editProjectModal');

    // --- Button Elements ---
    const newProjectBtn = document.getElementById('newProjectBtn');
    const cancelProjectBtn = document.getElementById('cancelProjectBtn');
    const cancelEditProjectBtn = document.getElementById('cancelEditProjectBtn');
    
    // --- Container for Event Delegation ---
    const projectListContainer = document.getElementById('projectList');

    // --- Modal Display Functions ---
    const showModal = (modal) => {
        if (modal) modal.style.display = 'flex';
    };
    const hideModal = (modal) => {
        if (modal) modal.style.display = 'none';
    };

    // --- "New Project" Modal Button Handlers ---
    if (newProjectBtn) {
        newProjectBtn.addEventListener('click', () => showModal(projectModal));
    }
    if (cancelProjectBtn) {
        cancelProjectBtn.addEventListener('click', () => hideModal(projectModal));
    }

    // --- "Edit Project" Modal Button Handler ---
    if (cancelEditProjectBtn) {
        cancelEditProjectBtn.addEventListener('click', () => hideModal(editProjectModal));
    }

    // --- Generic Modal Close (clicking outside) ---
    window.addEventListener('click', (event) => {
        if (event.target === projectModal) hideModal(projectModal);
        if (event.target === editProjectModal) hideModal(editProjectModal);
    });

    // --- Event Delegation for All Project Card Clicks ---
    if (projectListContainer) {
        projectListContainer.addEventListener('click', async (e) => {
            const target = e.target;
            const card = target.closest('.task-card');
            if (!card) return;

            const projectId = card.dataset.projectId;

            // Case 1: Click on EDIT button
            if (target.closest('.edit-project-btn')) {
                e.preventDefault();
                document.getElementById('editProjectId').value = card.dataset.projectId;
                document.getElementById('editProjectTitle').value = card.dataset.title;
                document.getElementById('editProjectDescription').value = card.dataset.description;
                document.getElementById('editProjectStartDate').value = card.dataset.startDate;
                document.getElementById('editProjectEndDate').value = card.dataset.endDate;
                document.getElementById('editProjectStatus').value = card.dataset.status;
                showModal(editProjectModal);
                return;
            }

            // Case 2: Click on DELETE button
            if (target.closest('.delete-project-btn')) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this project and all its tasks?')) {
                    const formData = new FormData();
                    formData.append('projectId', projectId);
                    try {
                        const response = await fetch('Config/delete_project.php', { method: 'POST', body: formData });
                        const result = await response.json();
                        if (result.success) {
                            alert('Project deleted successfully.');
                            card.remove();
                        } else {
                            alert(`Error: ${result.error || 'Could not delete project.'}`);
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert('A network error occurred.');
                    }
                }
                return;
            }

            // Case 3: Click on a link (like "View Tasks")
            if (target.closest('a')) {
                // Let the link do its default action (navigate)
                return;
            }

            // Case 4: Click on the card itself (but not on a button or link)
            if (projectId) {
                window.location.href = `tasks.php?project_id=${projectId}`;
            }
        });
    }

    // --- Search Functionality ---
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.task-card').forEach(card => {
                const title = (card.dataset.title || '').toLowerCase();
                const description = (card.dataset.description || '').toLowerCase();
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block'; // Or whatever the default is
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

