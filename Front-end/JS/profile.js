

function switchToEdit() {
    document.getElementById("viewMode").classList.add("hidden");
    document.getElementById("editMode").classList.remove("hidden");
}

function cancelEdit() {
    document.getElementById("editMode").classList.add("hidden");
    document.getElementById("viewMode").classList.remove("hidden");
}

function saveChanges() {
    const profileData = {
        username: document.getElementById("e-username").value,
        email: document.getElementById("e-email").value,
        company: document.getElementById("e-company").value,
        position: document.getElementById("e-position").value,
    };

    fetch("Config/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(profileData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh page to show updated data
            cancelEdit(); // Switch back to view mode
        } else {
            alert("Error updating profile: " .concat(data.error));
        }
    })
    .catch(error => {
        console.error("Error saving changes:", error);
        alert("A network error occurred while saving the profile.");
    });
}
