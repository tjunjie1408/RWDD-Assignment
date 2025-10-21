document.addEventListener("DOMContentLoaded", function() {
    // Fetch the user's avatar for the header
    fetch("PHP/get_profile.php")
        .then(response => response.ok ? response.json() : Promise.reject('Failed to load avatar'))
        .then(data => {
            if (data.avatar) {
                document.getElementById("userAvatar").src = data.avatar;
            }
        })
        .catch(error => console.error("Could not fetch user avatar:", error));
});