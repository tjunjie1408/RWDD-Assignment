// This script initializes the FullCalendar instance on the goal calendar page.
document.addEventListener('DOMContentLoaded', function() {
    // Gets the calendar container element from the DOM.
    var calendarEl = document.getElementById('calendar');
    
    // Creates a new FullCalendar instance.
    var calendar = new FullCalendar.Calendar(calendarEl, {
        // Sets the initial view to a month grid.
        initialView: 'dayGridMonth',
        // Configures the header toolbar with navigation and view-switching buttons.
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        // Specifies the URL to fetch event data from. FullCalendar will make an AJAX request to this endpoint.
        events: 'Config/get_goals.php',
        // Formats the time display for events in the week and day views.
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false // Uses 24-hour format.
        }
    });
    
    // Renders the calendar on the page.
    calendar.render();
});