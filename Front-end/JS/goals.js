// This script manages the functionality of the goals list page, including creating, editing, and deleting goals.
document.addEventListener('DOMContentLoaded', () => {
    // --- Element References ---
    const newGoalBtn = document.getElementById('newGoalBtn');
    const goalModal = document.getElementById('goalModal');
    const goalModalTitle = document.getElementById('goalModalTitle');
    const cancelGoalBtn = document.getElementById('cancelGoalBtn');
    const goalForm = document.getElementById('goalForm');
    const goalIdInput = document.getElementById('goalId');

    // --- Modal Handling ---
    const openModal = () => goalModal.classList.add('show');
    const closeModal = () => goalModal.classList.remove('show');

    // Opens the modal in 'create' mode.
    newGoalBtn.addEventListener('click', () => {
        goalForm.reset(); // Clears any previous data.
        goalIdInput.value = ''; // Ensures no ID is set.
        goalModalTitle.textContent = 'New Goal';
        goalForm.action = 'Config/create_goal.php'; // Sets the form to submit to the create script.
        openModal();
    });

    // Closes the modal when the cancel button or the background overlay is clicked.
    cancelGoalBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target === goalModal) {
            closeModal();
        }
    });

    // --- Edit Goal ---
    // Attaches a click event listener to all 'edit' buttons.
    document.querySelectorAll('.edit-goal-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.task-card');
            const goalId = card.dataset.goalId;

            // Finds the full goal object from the 'allGoals' array (provided by an inline script in the PHP file).
            const goal = allGoals.find(g => g.Goal_ID == goalId);

            if (goal) {
                // Populates the form with the existing goal data.
                goalIdInput.value = goal.Goal_ID;
                document.getElementById('goalTitle').value = goal.Title;
                document.getElementById('goalDescription').value = goal.Description;
                // The datetime string from the database needs ' ' replaced with 'T' to be valid for a datetime-local input.
                document.getElementById('goalStartDate').value = goal.Goal_Start_Time.replace(' ', 'T');
                document.getElementById('goalEndDate').value = goal.Goal_End_Time.replace(' ', 'T');
                document.getElementById('goalStatus').value = goal.Status;
            }

            // Sets the modal to 'edit' mode.
            goalModalTitle.textContent = 'Edit Goal';
            goalForm.action = 'Config/update_goal.php'; // Sets the form to submit to the update script.
            openModal();
        });
    });

    // --- Delete Goal ---
    // Attaches a click event listener to all 'delete' buttons.
    document.querySelectorAll('.delete-goal-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const card = e.target.closest('.task-card');
            const goalId = card.dataset.goalId;

            // Asks for user confirmation.
            if (confirm('Are you sure you want to delete this goal?')) {
                const formData = new FormData();
                formData.append('goalId', goalId);

                try {
                    // Sends a request to the server to delete the goal.
                    const response = await fetch('Config/delete_goal.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
                        // Removes the goal's card from the page on successful deletion.
                        card.remove();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('An error occurred while deleting the goal.');
                }
            }
        });
    });
});