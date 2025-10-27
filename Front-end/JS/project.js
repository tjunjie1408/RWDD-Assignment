document.addEventListener('DOMContentLoaded', () => {
    const projectListContainer = document.getElementById('projectList');
    const searchInput = document.getElementById('searchInput');
    let allProjects = [];

    // --- Fetch and Render Projects ---
    async function fetchAndRenderProjects() {
        try {
            const response = await fetch('Config/fetch_projects.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                allProjects = data.projects;
                renderProjects(allProjects);
            } else {
                projectListContainer.innerHTML = `<p>Error loading projects: ${data.error}</p>`;
            }
        } catch (error) {
            console.error('Error fetching projects:', error);
            projectListContainer.innerHTML = '<p>An error occurred while fetching projects.</p>';
        }
    }

    function renderProjects(projects) {
        projectListContainer.innerHTML = '';
        if (projects.length === 0) {
            projectListContainer.innerHTML = '<p>No projects available at the moment.</p>';
            return;
        }

        projects.forEach(project => {
            const projectCard = document.createElement('div');
            projectCard.classList.add('task-card');
            projectCard.dataset.projectId = project.Project_ID;

            const progress = project.Progress_Percent || 0;
            
            const actionButton = project.is_member
                ? `<a href="tasks.php?project_id=${project.Project_ID}" class="primary small-btn">View Tasks</a>`
                : `<button class.primary small-btn join-project-btn">Join Project</button>`;

            const projectLink = document.createElement('a');
            projectLink.href = `tasks.php?project_id=${project.Project_ID}`;
            projectLink.classList.add('project-card-link');

            projectCard.innerHTML = `
                <div class="task-header">
                    <h4>${project.Title}</h4>
                    ${actionButton}
                </div>
                <p class="task-desc">${project.Description || "No description."}</p>
                <div class="task-footer">
                    <span class="task-date">📅 ${project.Project_Start_Date} to ${project.Project_End_Date}</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progress}%;"></div>
                    </div>
                    <span class="progress-text">${progress}%</span>
                </div>
            `;

            projectLink.appendChild(projectCard);
            projectListContainer.appendChild(projectLink);
        });

        // addProjectActionListeners function removed as it is no longer needed.

        document.querySelectorAll('.view-project-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const card = e.target.closest('.task-card');
                const projectId = card.dataset.projectId;
                // Redirect to the tasks page for this project
                window.location.href = `tasks.php?project_id=${projectId}`;
            });
        });
    }

    // --- Search Functionality ---
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredProjects = allProjects.filter(p => 
                p.Title.toLowerCase().includes(searchTerm) || 
                p.Description.toLowerCase().includes(searchTerm)
            );
            renderProjects(filteredProjects);
        });
    }

    // --- Initial Load ---
    fetchAndRenderProjects();
});
