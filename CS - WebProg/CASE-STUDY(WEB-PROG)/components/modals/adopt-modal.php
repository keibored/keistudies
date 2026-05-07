<!--------------------------- Adopt Modal ----------------------------------------->
<div id="adoptModal" class="modal-overlay">
    <div class="modal-box fade-in">

        <h2 class="text-center">Adoption Application</h2>

        <span id="closeAdoptModal" class="modal-close">&times;</span>

        <form id="adoptForm" action="actions/adopt.php" method="POST">

            <!-- Hidden Pet ID -->
            <input type="hidden" name="petID" id="adoptPetID">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullName" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contactNumber" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <input type="checkbox" id="adoptTerms" required>
                <label for="adoptTerms">
                    I agree to the adoption process and confirm all details are correct.
                </label>
            </div>

            <div class="form-group">
                <input type="checkbox" id="payConfirm" name="payConfirm" required>
                <label for="payConfirm">Payment</label>
            </div>

            <p id="adoptMessage" class="text-center"></p>

            <button class="btn btn-primary" style="width:100%;">Submit Application</button>
        </form>
    </div>
</div>
