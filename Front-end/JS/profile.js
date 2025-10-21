document.addEventListener("DOMContentLoaded", function() {
    fetchUserProfile();
});

function fetchUserProfile() {
    fetch("PHP/get_profile.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Not logged in or user not found");
            }
            return response.json();
        })
        .then(data => {
            // Populate view mode
            document.getElementById("v-username").textContent = data.username;
            document.getElementById("v-email").textContent = data.email;
            document.getElementById("v-password").textContent = "********";
            document.getElementById("v-company").textContent = data.company;
            document.getElementById("v-position").textContent = data.position;
            document.getElementById("viewAvatar").src = data.avatar;
            document.getElementById("userAvatar").src = data.avatar; // Update header avatar

            // Populate edit mode
            document.getElementById("e-username").value = data.username;
            document.getElementById("e-email").value = data.email;
            document.getElementById("e-company").value = data.company;
            document.getElementById("e-position").value = data.position;
            document.getElementById("editAvatarPreview").src = data.avatar;
        })
        .catch(error => {
            console.error("Error fetching or populating profile:", error);
            // Redirect to login if not authenticated
            window.location.href = "signup.php";
        });
}

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

    fetch("PHP/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(profileData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchUserProfile(); // Refresh profile data
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
