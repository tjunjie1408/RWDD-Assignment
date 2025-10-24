document.addEventListener('DOMContentLoaded', () => {
    const newProjectBtn = document.getElementById('newProjectBtn');
    const projectModal = document.getElementById('projectModal');
    const cancelProjectBtn = document.getElementById('cancelProjectBtn');
    const projectForm = document.getElementById('projectForm');
    const projectListContainer = document.getElementById('projectList');

    // --- Modal Handling ---
    if (newProjectBtn) {
        newProjectBtn.addEventListener('click', () => {
            projectModal.classList.add('show');
        });
    }

    if (cancelProjectBtn) {
        cancelProjectBtn.addEventListener('click', () => {
            projectModal.classList.remove('show');
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === projectModal) {
            projectModal.classList.remove('show');
        }
    });

    // --- Create Project ---
    if (projectForm) {
        projectForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(projectForm);

            try {
                const response = await fetch('Config/create_project.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('Project created successfully!');
                    projectModal.classList.remove('show');
                    projectForm.reset();
                    fetchAndRenderProjects(); // Refresh the project list
                } else {
                    alert('Error creating project: ' + result.error);
                }
            } catch (error) {
                console.error('Failed to submit project form:', error);
                alert('A network error occurred. Please try again.');
            }
        });
    }

    // --- Fetch and Render Projects ---
    async function fetchAndRenderProjects() {
        try {
            const response = await fetch('Config/fetch_projects.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                renderProjects(data.projects);
            } else {
                projectListContainer.innerHTML = `<p>Error loading projects: ${data.error}</p>`;
            }
        } catch (error) {
            console.error('Error fetching projects:', error);
            projectListContainer.innerHTML = '<p>An error occurred while fetching projects.</p>';
        }
    }

    function renderProjects(projects) {
        projectListContainer.innerHTML = ''; // Clear existing projects
        if (projects.length === 0) {
            projectListContainer.innerHTML = '<p>No projects found. Create one to get started!</p>';
            return;
        }

        projects.forEach(project => {
            const projectCard = document.createElement('div');
            projectCard.classList.add('task-card'); // Re-using task-card style for now
            projectCard.dataset.projectId = project.Project_ID;

            // Basic progress calculation (can be improved later)
            const progress = project.Project_Status === 'Completed' ? 100 : 50;

            projectCard.innerHTML = `
                <div class="task-header">
                    <h4>${project.Title}</h4>
                    <span class="task-category">${project.Project_Status}</span>
                </div>
                <p class="task-desc">${project.Description || "No description."}</p>
                <div class="task-footer">
                    <span class="task-date">📅 ${project.Project_Start_Time} to ${project.Project_End_Time}</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progress}%;"></div>
                    </div>
                    <span class="progress-text">${progress}%</span>
                </div>
            `;
            projectListContainer.appendChild(projectCard);
        });
    }

    // Initial load
    fetchAndRenderProjects();
});
