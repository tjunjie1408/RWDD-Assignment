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
            projectCard.dataset.title = project.Title;
            projectCard.dataset.description = project.Description;
            projectCard.dataset.startDate = project.Project_Start_Date;
            projectCard.dataset.endDate = project.Project_End_Date;

            const progress = project.Progress_Percent || 0;
            
            let actionButton;
            let memberBadge = '';
            if (project.is_member) {
                actionButton = `<a href="tasks.php?project_id=${project.Project_ID}" class="primary small-btn">View Tasks</a>`;
                memberBadge = '<span class="member-badge">You are a member</span>';
            } else {
                actionButton = `<button class="secondary small-btn view-details-btn">View Details</button>`;
            }

            projectCard.innerHTML = `
                <div class="task-header">
                    <h4>${project.Title}</h4>
                    <div class="header-actions">
                        ${memberBadge}
                        ${actionButton}
                    </div>
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
            projectListContainer.appendChild(projectCard);
        });

        // Add event listeners for the new "View Details" buttons
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const card = e.target.closest('.task-card');
                showProjectDetailsModal(card);
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

    // --- Modal Handling for Project Details ---
    const projectDetailsModal = document.getElementById('projectDetailsModal');
    const closeProjectDetailsBtn = document.getElementById('closeProjectDetailsBtn');

    function showProjectDetailsModal(card) {
        document.getElementById('projectDetailsTitle').textContent = card.dataset.title;
        document.getElementById('projectDetailsDescription').textContent = card.dataset.description;
        document.getElementById('projectDetailsDates').textContent = `${card.dataset.startDate} to ${card.dataset.endDate}`;
        projectDetailsModal.style.display = 'flex';
    }

    function hideProjectDetailsModal() {
        projectDetailsModal.style.display = 'none';
    }

    closeProjectDetailsBtn.addEventListener('click', hideProjectDetailsModal);

    // --- Initial Load ---
    fetchAndRenderProjects();
});
