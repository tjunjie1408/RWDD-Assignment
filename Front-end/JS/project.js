// This script manages the user-facing projects page, allowing users to view and search for projects.
document.addEventListener('DOMContentLoaded', () => {
    // --- Element References ---
    const projectListContainer = document.getElementById('projectList');
    const searchInput = document.getElementById('searchInput');
    let allProjects = []; // Caches the list of all projects for client-side searching.

    // --- Data Fetching and Rendering ---

    /**
     * Fetches the list of projects from the server and initiates the rendering process.
     */
    async function fetchAndRenderProjects() {
        try {
            // The backend script (`fetch_projects.php`) determines which projects the user can see.
            const response = await fetch('Config/fetch_projects.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            // The backend returns a JSON array of project objects.
            // Note: The user-facing project page seems to have been updated to fetch all projects
            // and let the backend decide on membership status, which is a good approach.
            // This check for `data.success` might be from an older version.
            if (data) { // Assuming the response is the array directly.
                allProjects = data;
                renderProjects(allProjects);
            } else {
                projectListContainer.innerHTML = `<p>Error loading projects.</p>`;
            }
        } catch (error) {
            console.error('Error fetching projects:', error);
            projectListContainer.innerHTML = '<p>An error occurred while fetching projects.</p>';
        }
    }

    /**
     * Renders a list of project cards into the DOM.
     * @param {Array} projects - The array of project objects to display.
     */
    function renderProjects(projects) {
        projectListContainer.innerHTML = '';
        if (projects.length === 0) {
            projectListContainer.innerHTML = '<p>No projects available at the moment.</p>';
            return;
        }

        projects.forEach(project => {
            const projectCard = document.createElement('div');
            projectCard.classList.add('task-card');
            // Store project data in data-* attributes for easy access later.
            projectCard.dataset.projectId = project.Project_ID;
            projectCard.dataset.title = project.Title;
            projectCard.dataset.description = project.Description;
            projectCard.dataset.startDate = project.Project_Start_Date;
            projectCard.dataset.endDate = project.Project_End_Date;

            const progress = project.Progress_Percent || 0;
            
            let actionButton;
            let memberBadge = '';
            // The backend provides an 'is_member' flag to customize the UI for members vs. non-members.
            if (project.is_member) {
                actionButton = `<a href="tasks.php?project_id=${project.Project_ID}" class="primary small-btn">View Tasks</a>`;
                memberBadge = '<span class="member-badge">You are a member</span>';
            } else {
                // Non-members get a button to view details in a modal.
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

        // Adds event listeners to the 'View Details' buttons for non-members.
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
            // Filters the cached project list based on the search term.
            const filteredProjects = allProjects.filter(p => 
                p.Title.toLowerCase().includes(searchTerm) || 
                (p.Description && p.Description.toLowerCase().includes(searchTerm))
            );
            renderProjects(filteredProjects);
        });
    }

    // --- Modal Handling for Project Details ---
    const projectDetailsModal = document.getElementById('projectDetailsModal');
    const closeProjectDetailsBtn = document.getElementById('closeProjectDetailsBtn');

    function showProjectDetailsModal(card) {
        // Populates the modal with data from the clicked card's data attributes.
        document.getElementById('projectDetailsTitle').textContent = card.dataset.title;
        document.getElementById('projectDetailsDescription').textContent = card.dataset.description;
        document.getElementById('projectDetailsDates').textContent = `${card.dataset.startDate} to ${card.dataset.endDate}`;
        projectDetailsModal.style.display = 'flex';
    }

    function hideProjectDetailsModal() {
        projectDetailsModal.style.display = 'none';
    }

    if(closeProjectDetailsBtn) {
        closeProjectDetailsBtn.addEventListener('click', hideProjectDetailsModal);
    }


    // --- Initial Load ---
    fetchAndRenderProjects();
});