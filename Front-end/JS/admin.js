document.addEventListener('DOMContentLoaded', () => {
    // --- General Elements ---
    const projectListContainer = document.getElementById('projectList');

    // --- Create Modal Elements ---
    const newProjectBtn = document.getElementById('newProjectBtn');
    const projectModal = document.getElementById('projectModal');
    const cancelProjectBtn = document.getElementById('cancelProjectBtn');
    const projectForm = document.getElementById('projectForm');

    // --- Edit Modal Elements ---
    const editProjectModal = document.getElementById('editProjectModal');
    const cancelEditProjectBtn = document.getElementById('cancelEditProjectBtn');
    const editProjectForm = document.getElementById('editProjectForm');

    // --- Create Modal Handling ---
    if (newProjectBtn) {
        newProjectBtn.addEventListener('click', () => projectModal.classList.add('show'));
    }
    if (cancelProjectBtn) {
        cancelProjectBtn.addEventListener('click', () => projectModal.classList.remove('show'));
    }

    // --- Edit Modal Handling ---
    if (cancelEditProjectBtn) {
        cancelEditProjectBtn.addEventListener('click', () => editProjectModal.classList.remove('show'));
    }

    // --- Generic Modal Close ---
    window.addEventListener('click', (event) => {
        if (event.target === projectModal) projectModal.classList.remove('show');
        if (event.target === editProjectModal) editProjectModal.classList.remove('show');
    });





    function addProjectActionListeners() {
        document.querySelectorAll('.delete-project-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const card = e.target.closest('.task-card');
                const projectId = card.dataset.projectId;
                if (confirm('Are you sure you want to delete this project and all its tasks?')) {
                    const formData = new FormData();
                    formData.append('projectId', projectId);
                    try {
                        const response = await fetch('Config/delete_project.php', { method: 'POST', body: formData });
                        const result = await response.json();
                        if (result.success) {
                            alert('Project deleted.');
                            card.remove();
                        } else {
                            alert(`Error: ${result.error}`);
                        }
                    } catch (error) {
                        alert('A network error occurred.');
                    }
                }
            });
        });

        document.querySelectorAll('.edit-project-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const card = e.target.closest('.task-card');
                
                document.getElementById('editProjectId').value = card.dataset.projectId;
                document.getElementById('editProjectTitle').value = card.dataset.title;
                document.getElementById('editProjectDescription').value = card.dataset.description;
                document.getElementById('editProjectStartDate').value = card.dataset.startDate;
                document.getElementById('editProjectEndDate').value = card.dataset.endDate;
                document.getElementById('editProjectStatus').value = card.dataset.status;
                
                editProjectModal.classList.add('show');
            });
        });
    }

    // Initial load
    addProjectActionListeners();
});
