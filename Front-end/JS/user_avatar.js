// This script runs when the page loads to fetch and display the logged-in user's avatar in the header.
document.addEventListener("DOMContentLoaded", function() {
    // Fetches the user's profile data from the server.
    fetch("Config/get_profile.php")
        // Checks if the request was successful, then parses the JSON response.
        .then(response => response.ok ? response.json() : Promise.reject('Failed to load avatar'))
        // If the data includes an avatar URL, it updates the 'src' of the avatar image element.
        .then(data => {
            if (data.avatar) {
                // This assumes an element with the ID 'userAvatar' exists in the header.
                document.getElementById("userAvatar").src = data.avatar;
            }
        })
        // Logs any errors that occur during the fetch process.
        .catch(error => console.error("Could not fetch user avatar:", error));
});