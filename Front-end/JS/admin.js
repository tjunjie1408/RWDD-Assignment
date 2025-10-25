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
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const card = e.target.closest('.task-card');
                const projectId = card.dataset.projectId;
                try {
                    const response = await fetch(`Config/fetch_project_details.php?id=${projectId}`);
                    const result = await response.json();
                    if (result.success) {
                        const p = result.project;
                        document.getElementById('editProjectId').value = p.Project_ID;
                        document.getElementById('editProjectTitle').value = p.Title;
                        document.getElementById('editProjectDescription').value = p.Description;
                        document.getElementById('editProjectStartDate').value = p.Project_Start_Time;
                        document.getElementById('editProjectEndDate').value = p.Project_End_Time;
                        document.getElementById('editProjectStatus').value = p.Project_Status;
                        editProjectModal.classList.add('show');
                    } else {
                        alert(`Error: ${result.error}`);
                    }
                } catch (error) {
                    alert('A network error occurred.');
                }
            });
        });
    }

    // Initial load
    addProjectActionListeners();
});
