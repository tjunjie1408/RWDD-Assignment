document.addEventListener('DOMContentLoaded', () => {
    // --- Project Completion Chart ---
    const projectCtx = document.getElementById('projectChart').getContext('2d');
    if (window.projectChart instanceof Chart) {
        window.projectChart.destroy();
    }
    window.projectChart = new Chart(projectCtx, {
        type: 'pie',
        data: {
            labels: projectLabels.map(label => label.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                label: 'Project Status',
                data: projectData,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)', // Completed
                    'rgba(54, 162, 235, 0.7)', // In Progress
                    'rgba(201, 203, 207, 0.7)', // Not Started
                    'rgba(255, 205, 86, 0.7)'  // On Hold
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(201, 203, 207, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribution of Project Statuses'
                }
            }
        }
    });

    // --- Task Distribution Chart ---
    const taskCtx = document.getElementById('taskChart').getContext('2d');
    if (window.taskChart instanceof Chart) {
        window.taskChart.destroy();
    }
    window.taskChart = new Chart(taskCtx, {
        type: 'doughnut',
        data: {
            labels: taskLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
            datasets: [{
                label: 'Task Status',
                data: taskData,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)', // Done
                    'rgba(255, 99, 132, 0.7)'  // Open
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribution of Task Statuses'
                }
            }
        }
    });
});