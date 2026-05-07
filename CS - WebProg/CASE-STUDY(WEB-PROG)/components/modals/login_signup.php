    <!--------------------------- Login Modal ----------------------------------------->
    <div id="loginModal" class="modal-overlay">
        <div class="modal-box fade-in">
            <h2 class="text-center">Login</h2>

            <span id="closeLoginModal" class="modal-close">&times;</span>

            <form action="actions/login.php" method="POST" id="loginForm">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <p id="loginMessage" class="text-center"></p>
                <button class="btn btn-primary" style="width:100%;">Login</button>
                <p class="text-center" style="margin-top: var(--space-md);">
                    No account yet?
                    <a href="#" id="openSignupFromLogin">Create one</a>
                </p>
            </form>
        </div>
    </div>

    <!--------------------------- Signup Modal ------------------------------------------>
    <div id="signupModal" class="modal-overlay">
    <div class="modal-box fade-in">
        <h2 class="text-center">Create Account</h2>

        <span id="closeSignupModal" class="modal-close">&times;</span>

        <form action="actions/register.php" method="POST" id="signupForm">
        
        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input 
            type="text" 
            id="display_name"
            name="display_name" 
            placeholder="e.g., Gian Vincent Tan"
            required
            autocomplete="name"
            >
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input 
            type="text" 
            id="username"
            name="username" 
            placeholder="Choose a username"
            required
            autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input 
            type="password" 
            id="password"
            name="password" 
            placeholder="Create a password"
            required
            minlength="8"
            autocomplete="new-password"
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Retype Password</label>
            <input 
            type="password" 
            id="confirm_password"
            name="confirm_password" 
            placeholder="Retype your password"
            required
            minlength="8"
            autocomplete="new-password"
            >
        </div>

        <div class="form-group">
            <input type="checkbox" id="accept_terms" name="accept_terms" required>
            <label for="accept_terms">
            I agree to the 
            <a href="terms.php" target="_blank" rel="noopener">Terms of Service</a> 
            and 
            <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.
            </label>
        </div>
        <p id="signupMessage" class="text-center"></p>
        <button class="btn btn-primary">Sign Up</button>

        <p class="text-center" style="margin-top: var(--space-md);">
            Already have an account?
            <a href="#" id="openLoginFromSignup">Login here</a>
        </p>
        </form>
    </div>
    </div>