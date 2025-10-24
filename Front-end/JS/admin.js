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

    // --- Create Project Form Submission ---
    if (projectForm) {
        projectForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(projectForm);
            try {
                const response = await fetch('Config/create_project.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert('Project created successfully!');
                    projectModal.classList.remove('show');
                    projectForm.reset();
                    fetchAndRenderProjects();
                } else {
                    alert(`Error: ${result.error}`);
                }
            } catch (error) {
                alert('A network error occurred.');
            }
        });
    }

    // --- Edit Project Form Submission ---
    if (editProjectForm) {
        editProjectForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(editProjectForm);
            try {
                const response = await fetch('Config/update_project.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    alert('Project updated successfully!');
                    editProjectModal.classList.remove('show');
                    fetchAndRenderProjects();
                } else {
                    alert(`Error: ${result.error}`);
                }
            } catch (error) {
                alert('A network error occurred.');
            }
        });
    }

    // --- Fetch and Render Projects ---
    async function fetchAndRenderProjects() {
        try {
            const response = await fetch('Config/fetch_projects.php');
            const data = await response.json();
            if (data.success) {
                renderProjects(data.projects);
            } else {
                projectListContainer.innerHTML = `<p>Error: ${data.error}</p>`;
            }
        } catch (error) {
            projectListContainer.innerHTML = '<p>An error occurred while fetching projects.</p>';
        }
    }

    function renderProjects(projects) {
        projectListContainer.innerHTML = '';
        if (projects.length === 0) {
            projectListContainer.innerHTML = '<p>No projects found. Create one to get started!</p>';
            return;
        }
        projects.forEach(project => {
            const card = document.createElement('div');
            card.classList.add('task-card');
            card.dataset.projectId = project.Project_ID;
            const progress = project.Project_Status === 'Completed' ? 100 : (project.Progress_Percent || 0);

            card.innerHTML = `
                <div class="task-header">
                    <h4>${project.Title}</h4>
                    <div>
                        <button class="primary small-btn edit-project-btn">Edit</button>
                        <button class="danger small-btn delete-project-btn">Delete</button>
                    </div>
                </div>
                <p class="task-desc">${project.Description || "No description."}</p>
                <div class="task-footer">
                    <span class="task-date">📅 ${project.Project_Start_Time} to ${project.Project_End_Time}</span>
                    <div class="progress-bar"><div class="progress-fill" style="width: ${progress}%;"></div></div>
                    <span class="progress-text">${progress}%</span>
                </div>
            `;
            projectListContainer.appendChild(card);
        });
        addProjectActionListeners();
    }

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
    fetchAndRenderProjects();
});
