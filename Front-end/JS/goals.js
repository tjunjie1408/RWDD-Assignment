document.addEventListener('DOMContentLoaded', () => {
    const newGoalBtn = document.getElementById('newGoalBtn');
    const goalModal = document.getElementById('goalModal');
    const goalModalTitle = document.getElementById('goalModalTitle');
    const cancelGoalBtn = document.getElementById('cancelGoalBtn');
    const goalForm = document.getElementById('goalForm');
    const goalIdInput = document.getElementById('goalId');

    // --- Modal Handling ---
    const openModal = () => goalModal.classList.add('show');
    const closeModal = () => goalModal.classList.remove('show');

    newGoalBtn.addEventListener('click', () => {
        goalForm.reset();
        goalIdInput.value = '';
        goalModalTitle.textContent = 'New Goal';
        goalForm.action = 'Config/create_goal.php';
        openModal();
    });

    cancelGoalBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target === goalModal) {
            closeModal();
        }
    });

    // --- Edit Goal ---
    document.querySelectorAll('.edit-goal-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.task-card');
            const goalId = card.dataset.goalId;

            // Fetch the full goal details to ensure we have the correct datetime format
            const goal = allGoals.find(g => g.Goal_ID == goalId);

            if (goal) {
                goalIdInput.value = goal.Goal_ID;
                document.getElementById('goalTitle').value = goal.Title;
                document.getElementById('goalDescription').value = goal.Description;
                // Format the DATETIME string for the datetime-local input
                document.getElementById('goalStartDate').value = goal.Goal_Start_Time.replace(' ', 'T');
                document.getElementById('goalEndDate').value = goal.Goal_End_Time.replace(' ', 'T');
                document.getElementById('goalStatus').value = goal.Status;
            }

            goalModalTitle.textContent = 'Edit Goal';
            goalForm.action = 'Config/update_goal.php';
            openModal();
        });
    });

    // --- Delete Goal ---
    document.querySelectorAll('.delete-goal-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const card = e.target.closest('.task-card');
            const goalId = card.dataset.goalId;

            if (confirm('Are you sure you want to delete this goal?')) {
                const formData = new FormData();
                formData.append('goalId', goalId);

                try {
                    const response = await fetch('Config/delete_goal.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
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