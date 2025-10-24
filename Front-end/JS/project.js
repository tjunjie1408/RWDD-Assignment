document.addEventListener("DOMContentLoaded", () => {
    const path = window.location.pathname;
    const onAdminPage = path.includes("admin_project.php");

    if (onAdminPage) {
        initializeAdminProjectView();
    } else {
        initializeUserProjectView();
    }
});


/**
 * =================================================================
 * ADMIN PROJECT MANAGEMENT VIEW (admin_project.php)
 * =================================================================
 */
function initializeAdminProjectView() {
    const newProjectBtn = document.getElementById("newProjectBtn");
    const modal = document.getElementById("modal");
    const cancelBtn = document.getElementById("cancelBtn");
    const saveProjectBtn = document.getElementById("saveProjectBtn");
    const projectListContainer = document.getElementById("projectList");

    // Open "Create Project" modal
    newProjectBtn.addEventListener("click", () => {
        document.getElementById("modalTitle").textContent = "Create New Project";
        document.getElementById("projectId").value = ""; // Clear ID for creation
        document.getElementById("projectTitle").value = "";
        document.getElementById("projectDescription").value = "";
        document.getElementById("projectStartDate").value = "";
        document.getElementById("projectEndDate").value = "";
        document.getElementById("projectStatus").value = "Not Started";
        modal.classList.add("show");
    });

    // Close modal
    cancelBtn.addEventListener("click", () => {
        modal.classList.remove("show");
    });

    // Handle Save/Update Project
    saveProjectBtn.addEventListener("click", () => {
        // This is where you would add an API call to save/update the project
        const projectData = {
            id: document.getElementById("projectId").value,
            title: document.getElementById("projectTitle").value,
            description: document.getElementById("projectDescription").value,
            startDate: document.getElementById("projectStartDate").value,
            endDate: document.getElementById("projectEndDate").value,
            status: document.getElementById("projectStatus").value,
        };

        console.log("Saving project:", projectData);
        alert("Project functionality (save/update) is not yet connected to the backend.");
        modal.classList.remove("show");
        // After successful save, you would call fetchAndRenderProjects()
    });

    // Fetch and render projects
    function fetchAndRenderProjects() {
        // Placeholder: In a real app, you'd fetch this from a PHP endpoint
        const projects = [{
                id: 1,
                title: "Website Redesign",
                description: "Complete overhaul of the company website.",
                status: "In Progress"
            },
            {
                id: 2,
                title: "Q4 Marketing Campaign",
                description: "Launch campaign for the new product line.",
                status: "Completed"
            },
            {
                id: 3,
                title: "Mobile App Development",
                description: "Develop the new iOS and Android application.",
                status: "Not Started"
            },
        ];

        renderProjects(projects);
    }

    function renderProjects(projects) {
        projectListContainer.innerHTML = "";
        projects.forEach(project => {
            const projectCard = document.createElement("div");
            projectCard.className = "task-card"; // Re-using task-card style for consistency
            projectCard.innerHTML = `
                <div class="task-header">
                    <h4>${project.title}</h4>
                    <span class="task-category">${project.status}</span>
                </div>
                <p class="task-desc">${project.description}</p>
                <div class="task-footer admin-actions">
                    <button class="edit-btn" data-id="${project.id}">Edit</button>
                    <button class="delete-btn" data-id="${project.id}">Delete</button>
                </div>
            `;
            projectListContainer.appendChild(projectCard);
        });
    }

    // Event delegation for edit/delete buttons
    projectListContainer.addEventListener("click", (e) => {
        const projectId = e.target.dataset.id;
        if (e.target.classList.contains("edit-btn")) {
            // In a real app, fetch project details by ID first
            console.log("Editing project", projectId);
            alert("Edit functionality is a placeholder.");
            // Pre-fill and open the modal for editing
        }
        if (e.target.classList.contains("delete-btn")) {
            if (confirm(`Are you sure you want to delete project ${projectId}?`)) {
                console.log("Deleting project", projectId);
                alert("Delete functionality is a placeholder.");
                // API call to delete, then re-render
            }
        }
    });

    // Initial load
    fetchAndRenderProjects();
}


/**
 * =================================================================
 * USER TASK MANAGEMENT VIEW (project.php)
 * =================================================================
 */
function initializeUserProjectView() {
    const newTaskBtn = document.getElementById("newTaskBtn");
    const taskModal = document.getElementById("modal");
    const cancelBtn = document.getElementById("cancelBtn");
    const createBtn = document.getElementById("createBtn");

    if (newTaskBtn) {
        newTaskBtn.addEventListener("click", () => taskModal.classList.add("show"));
    }
    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => taskModal.classList.remove("show"));
    }

    // The rest of the user-specific task logic from the original file
    // This part remains largely the same as it handles task creation,
    // rendering, filtering, and details for the user.

    // Example: Re-implementing a simplified task creation for demonstration
    if (createBtn) {
        createBtn.addEventListener("click", () => {
            const title = document.getElementById("taskTitle").value;
            if (!title) {
                alert("Task title is required.");
                return;
            }
            console.log("Creating task:", title);
            alert("Task creation is a placeholder.");
            taskModal.classList.remove("show");
        });
    }

    // Placeholder for rendering tasks
    function renderUserTasks() {
        const container = document.getElementById("taskGroups");
        if (container) {
            container.innerHTML = `<p style="padding: 20px; text-align: center;">Task display area. Task loading is not yet implemented.</p>`;
        }
        // Update Kanban counts
        const inProgressCount = document.getElementById("inProgressCount");
        const completedCount = document.getElementById("completedCount");
        const overdueCount = document.getElementById("overdueCount");
        if (inProgressCount) inProgressCount.textContent = '0';
        if (completedCount) completedCount.textContent = '0';
        if (overdueCount) overdueCount.textContent = '0';
    }

    // Handle file submission form
    const submissionForm = document.getElementById("submissionForm");
    if (submissionForm) {
        submissionForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const file = document.getElementById("submissionFile").files[0];
            if (!file) {
                alert("Please upload a file.");
                return;
            }
            console.log("Submitting file:", file.name);
            alert("File submission is a placeholder.");
            submissionForm.reset();
        });
    }

    // Initial load for user view
    renderUserTasks();
}