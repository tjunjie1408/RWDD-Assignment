// This script initializes charts on the analysis page using the Chart.js library.
// It runs after the full HTML document has been loaded.
document.addEventListener('DOMContentLoaded', () => {
    // --- Project Completion Chart (Pie Chart) ---
    
    // Gets the canvas element for the project chart.
    const projectCtx = document.getElementById('projectChart').getContext('2d');
    
    // Destroys any existing chart instance on the canvas to prevent conflicts.
    if (window.projectChart instanceof Chart) {
        window.projectChart.destroy();
    }
    
    // Creates a new pie chart to visualize the distribution of project statuses.
    // The data (projectLabels, projectData) is passed from the PHP file via inline script tags.
    window.projectChart = new Chart(projectCtx, {
        type: 'pie',
        data: {
            // Formats the labels for better readability (e.g., 'in_progress' becomes 'In Progress').
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

    // --- Task Distribution Chart (Doughnut Chart) ---

    // Gets the canvas element for the task chart.
    const taskCtx = document.getElementById('taskChart').getContext('2d');

    // Destroys any existing chart instance.
    if (window.taskChart instanceof Chart) {
        window.taskChart.destroy();
    }

    // Creates a new doughnut chart for task statuses.
    // The data (taskLabels, taskData) is also passed from the PHP file.
    window.taskChart = new Chart(taskCtx, {
        type: 'doughnut',
        data: {
            // Formats labels (e.g., 'open' becomes 'Open').
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