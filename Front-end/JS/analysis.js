document.addEventListener("DOMContentLoaded", () => {
  // Retrieve data from localStorage
  const tasks = JSON.parse(localStorage.getItem("tasks")) || [];
  const goals = JSON.parse(localStorage.getItem("calendarEvents")) || [];

  // ====== Calculate Task & Project Stats ======
  const totalTasks = tasks.length;
  const completedTasks = tasks.filter(t => t.progress >= 100).length;
  const overdueTasks = tasks.filter(t => t.progress < 100 && new Date(t.dueDate) < new Date()).length;
  const inProgressTasks = totalTasks - completedTasks - overdueTasks;
  const averageProgress = totalTasks
    ? Math.round(tasks.reduce((sum, t) => sum + t.progress, 0) / totalTasks)
    : 0;

  // ====== Calculate Goal Stats ======
  const goalGroups = {};
  goals.forEach(g => {
    if (!g.goalId) return;
    if (!goalGroups[g.goalId]) goalGroups[g.goalId] = [];
    goalGroups[g.goalId].push(g);
  });

  const totalGoals = Object.keys(goalGroups).length;
  let completedGoals = 0;
  Object.values(goalGroups).forEach(list => {
    if (list.every(g => g.isCompleted)) completedGoals++;
  });

  // ====== Insert Charts ======
  const chartContainers = document.querySelectorAll(".analysis-chart");

  // 1️⃣ Project Completion Rate
  const completionCanvas = document.createElement("canvas");
  chartContainers[0].appendChild(completionCanvas);

  new Chart(completionCanvas, {
    type: "doughnut",
    data: {
      labels: ["Completed", "In Progress", "Overdue"],
      datasets: [
        {
          data: [completedTasks, inProgressTasks, overdueTasks],
          backgroundColor: ["#22c55e", "#fbbf24", "#ef4444"],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        title: { display: true, text: `Average Progress: ${averageProgress}%`, font: { size: 14 } },
        legend: { position: "bottom" },
      },
      cutout: "70%",
    },
  });

  // 2️⃣ Task Distribution (Goals vs Tasks)
  const distributionCanvas = document.createElement("canvas");
  chartContainers[1].appendChild(distributionCanvas);

  new Chart(distributionCanvas, {
    type: "bar",
    data: {
      labels: ["Total Tasks", "Completed Tasks", "Overdue Tasks", "Total Goals", "Completed Goals"],
      datasets: [
        {
          label: "Count",
          data: [totalTasks, completedTasks, overdueTasks, totalGoals, completedGoals],
          backgroundColor: ["#3b82f6", "#22c55e", "#ef4444", "#8b5cf6", "#14b8a6"],
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } },
      },
      plugins: {
        legend: { display: false },
        title: { display: true, text: "Task & Goal Distribution", font: { size: 14 } },
      },
    },
  });

  // ====== Optional Console Log for Debugging ======
  console.log("✅ Analysis Data Loaded");
  console.log({
    totalTasks,
    completedTasks,
    overdueTasks,
    inProgressTasks,
    averageProgress,
    totalGoals,
    completedGoals,
  });
});
