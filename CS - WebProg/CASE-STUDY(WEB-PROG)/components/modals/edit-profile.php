<?php
// Ensure session values exist
$displayName = $_SESSION["DisplayName"] ?? "";
?>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal-overlay">
    <div class="modal-box fade-in" style="max-width: 450px;">

        <h2 class="text-center">Edit Profile</h2>
        <span id="closeEditProfile" class="modal-close">&times;</span>

        <form id="editProfileForm">

            <!-- Display Name -->
            <div class="form-group">
                <label for="edit_display_name">Display Name</label>
                <input 
                    type="text"
                    id="edit_display_name"
                    name="display_name"
                    value="<?php echo htmlspecialchars($displayName); ?>"
                    required
                >
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="edit_new_password">New Password (optional)</label>
                <input 
                    type="password"
                    id="edit_new_password"
                    name="new_password"
                    minlength="8"
                    placeholder="Leave blank to keep current password"
                >
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="edit_confirm_password">Confirm New Password</label>
                <input 
                    type="password"
                    id="edit_confirm_password"
                    name="confirm_password"
                    minlength="8"
                    placeholder="Retype new password"
                >
            </div>

            <p id="editProfileMessage" class="text-center"></p>

            <button class="btn btn-primary" style="width:100%;">Save Changes</button>
        </form>

    </div>
</div>
