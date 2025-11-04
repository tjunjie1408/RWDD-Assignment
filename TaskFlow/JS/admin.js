// This script runs when the DOM is fully loaded and ready.
document.addEventListener('DOMContentLoaded', () => {
    // --- Modal and Form Elements ---
    // References to the modals and forms for creating and editing projects.
    const projectModal = document.getElementById('projectModal');
    const newProjectBtn = document.getElementById('newProjectBtn');
    const cancelProjectBtn = document.getElementById('cancelProjectBtn');
    
    const editProjectModal = document.getElementById('editProjectModal');
    const cancelEditProjectBtn = document.getElementById('cancelEditProjectBtn');

    // --- Modal Display Handling ---

    // Shows the 'Create Project' modal when the 'New Project' button is clicked.
    if (newProjectBtn) {
        newProjectBtn.addEventListener('click', () => projectModal.classList.add('show'));
    }
    // Hides the 'Create Project' modal when the 'Cancel' button is clicked.
    if (cancelProjectBtn) {
        cancelProjectBtn.addEventListener('click', () => projectModal.classList.remove('show'));
    }

    // Hides the 'Edit Project' modal when its 'Cancel' button is clicked.
    if (cancelEditProjectBtn) {
        cancelEditProjectBtn.addEventListener('click', () => editProjectModal.classList.remove('show'));
    }

    // Adds a global click listener to close modals when the user clicks on the background overlay.
    window.addEventListener('click', (event) => {
        if (event.target === projectModal) projectModal.classList.remove('show');
        if (event.target === editProjectModal) editProjectModal.classList.remove('show');
    });

    // --- Project Action Event Listeners ---
    // This function adds event listeners to all delete and edit buttons on the project cards.
    function addProjectActionListeners() {
        // Adds a click listener to each 'delete' button.
        document.querySelectorAll('.delete-project-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                // Prevents the click from bubbling up to parent elements (like the project card link).
                e.stopPropagation(); 
                const card = e.target.closest('.task-card');
                const projectId = card.dataset.projectId;

                // Asks for user confirmation before proceeding with deletion.
                if (confirm('Are you sure you want to delete this project and all its tasks?')) {
                    const formData = new FormData();
                    formData.append('projectId', projectId);
                    try {
                        // Sends a request to the server to delete the project.
                        const response = await fetch('Config/delete_project.php', { method: 'POST', body: formData });
                        const result = await response.json();
                        if (result.success) {
                            alert('Project deleted.');
                            // Removes the project card from the UI.
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

        // Adds a click listener to each 'edit' button.
        document.querySelectorAll('.edit-project-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const card = e.target.closest('.task-card');
                
                // Populates the 'Edit Project' form with data from the project card's data attributes.
                document.getElementById('editProjectId').value = card.dataset.projectId;
                document.getElementById('editProjectTitle').value = card.dataset.title;
                document.getElementById('editProjectDescription').value = card.dataset.description;
                document.getElementById('editProjectStartDate').value = card.dataset.startDate;
                document.getElementById('editProjectEndDate').value = card.dataset.endDate;
                document.getElementById('editProjectStatus').value = card.dataset.status;
                
                // Shows the 'Edit Project' modal.
                editProjectModal.classList.add('show');
            });
        });
    }

    // Initial call to set up the event listeners when the page loads.
    addProjectActionListeners();
});