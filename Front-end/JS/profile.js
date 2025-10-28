

/**
 * Switches the profile page from view mode to edit mode.
 * It hides the static profile information and shows the editable form fields.
 */
function switchToEdit() {
    document.getElementById("viewMode").classList.add("hidden");
    document.getElementById("editMode").classList.remove("hidden");
}

/**
 * Cancels the edit operation and switches back to view mode.
 * It hides the form fields and shows the static profile information.
 */
function cancelEdit() {
    document.getElementById("editMode").classList.add("hidden");
    document.getElementById("viewMode").classList.remove("hidden");
}

/**
 * Saves the changes made to the user's profile.
 * It collects the data from the form, sends it to the server, and handles the response.
 */
function saveChanges() {
    // Collects the updated profile data from the input fields.
    const profileData = {
        username: document.getElementById("e-username").value,
        email: document.getElementById("e-email").value,
        company: document.getElementById("e-company").value,
        position: document.getElementById("e-position").value,
    };

    // Sends the updated data to the server using the Fetch API.
    fetch("Config/update_profile.php", {
        method: "POST",
        // Specifies that the request body is JSON.
        headers: { "Content-Type": "application/json" },
        // Converts the JavaScript object to a JSON string.
        body: JSON.stringify(profileData),
    })
    .then(response => response.json())
    .then(data => {
        // Handles the server's response.
        if (data.success) {
            // If the update was successful, reload the page to show the new data.
            location.reload(); 
        } else {
            // If there was an error, show an alert to the user.
            alert("Error updating profile: " + data.error);
        }
    })
    .catch(error => {
        // Handles network errors.
        console.error("Error saving changes:", error);
        alert("A network error occurred while saving the profile.");
    });
}
