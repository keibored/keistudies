console.log('js is running');

document.addEventListener('DOMContentLoaded', function() {

    // Initialize feather icons
    if (typeof feather !== "undefined") {
        feather.replace();
    }

    // Modals
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModal');

    // Buttons / triggers
    const openLogin = document.getElementById('openLoginModal');
    const closeLogin = document.getElementById('closeLoginModal');
    const openSignupFromLogin = document.getElementById('openSignupFromLogin');

    const closeSignup = document.getElementById('closeSignupModal');
    const openLoginFromSignup = document.getElementById('openLoginFromSignup');

    // Open Login Modal

    if (openLogin) {
        openLogin.addEventListener('click', () => {
            signupModal.style.display = 'none';
            loginModal.style.display = 'flex';
            document.body.classList.add("modal-open");
        });
    }

    if (closeLogin) {
        closeLogin.addEventListener('click', () => {
            loginModal.style.display = 'none';
            document.body.classList.remove("modal-open");
        });
    }

    if (openSignupFromLogin) {
        openSignupFromLogin.addEventListener('click', () => {
            loginModal.style.display = 'none';
            signupModal.style.display = 'flex';
        });
    }

    if (openLoginFromSignup) {
        openLoginFromSignup.addEventListener('click', () => {
            signupModal.style.display = 'none';
            loginModal.style.display = 'flex';
        });
    }

    if (closeSignup) {
        closeSignup.addEventListener('click', () => {
            signupModal.style.display = 'none';
            document.body.classList.remove("modal-open");
        });
    }

    // -------------------- SIGNUP --------------------
    const signupForm = document.getElementById("signupForm");
    const pwd = document.getElementById("password");
    const confirmPwd = document.getElementById("confirm_password");

    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            e.preventDefault();

            if (pwd && confirmPwd && pwd.value !== confirmPwd.value) {
                alert("Passwords do not match. Please retype.");
                confirmPwd.focus();
                return; 
            }

            const formData = new FormData(this);

            fetch("actions/register.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const statusmsg = document.getElementById("signupMessage");
                statusmsg.innerText = data.message;
                statusmsg.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    setTimeout(() => {
                        document.getElementById("signupModal").style.display = "none";
                        document.getElementById("loginModal").style.display = "flex";
                    }, 1000);
                }
            });
        });
    }

    // -------------------- LOGIN --------------------
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("actions/login.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const statusmsg = document.getElementById("loginMessage");
                statusmsg.innerText = data.message;
                statusmsg.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        });
    }

    // -------------------- ADOPT MODAL --------------------
    const adoptModal = document.getElementById("adoptModal");
    const closeAdopt = document.getElementById("closeAdoptModal");
    const adoptButtons = document.querySelectorAll(".openAdoptModal");
    const adoptPetID = document.getElementById("adoptPetID");

    // Only run if adopt buttons exist on this page
    if (adoptButtons && adoptButtons.length > 0) {
        adoptButtons.forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();

                const petID = btn.getAttribute("data-petid");
                adoptPetID.value = petID;

                adoptModal.style.display = "flex";
                document.body.classList.add("modal-open");
            });
        });
    }

    // Close modal if the close button exists
    if (closeAdopt) {
        closeAdopt.addEventListener("click", () => {
            adoptModal.style.display = "none";
            document.body.classList.remove("modal-open");
        });
    }

    // -------------------- EDIT PROFILE MODAL --------------------
    const editProfileModal = document.getElementById("editProfileModal");
    const openEditProfile = document.getElementById("openEditProfile");
    const closeEditProfile = document.getElementById("closeEditProfile");

    if (openEditProfile) {
        openEditProfile.addEventListener("click", () => {
            editProfileModal.style.display = "flex";
            document.body.classList.add("modal-open");
        });
    }

    if (closeEditProfile) {
        closeEditProfile.addEventListener("click", () => {
            editProfileModal.style.display = "none";
            document.body.classList.remove("modal-open");
        });
    }

    const editProfileForm = document.getElementById("editProfileForm");

    if (editProfileForm) {
        editProfileForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("actions/update-profile.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById("editProfileMessage");

                msg.innerText = data.message;
                msg.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    setTimeout(() => {
                        editProfileModal.style.display = "none";
                        window.location.reload();
                    }, 1000);
                }
            });
        });
    }

    // -------------------- SUBMIT ADOPTION --------------------
    const adoptForm = document.getElementById("adoptForm");

    if (adoptForm) {
        adoptForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("actions/adopt.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const msg = document.getElementById("adoptMessage");
                msg.innerText = data.message;
                msg.style.color = data.status === "success" ? "green" : "red";

                if (data.status === "success") {
                    setTimeout(() => {
                        adoptModal.style.display = "none";
                        document.body.classList.remove("modal-open");
                        window.location.href = "pet-list.php";
                    }, 1200);
                }
            });
        });
    }

    // -------------------- CANCEL ADOPTION (AJAX) --------------------
    const cancelButtons = document.querySelectorAll(".cancel-adoption-btn");

    if (cancelButtons.length > 0) {
        cancelButtons.forEach(btn => {
            btn.addEventListener("click", function () {

                if (!confirm("Are you sure you want to cancel this adoption request?")) {
                    return;
                }

                const adoptID = btn.getAttribute("data-adoptid");

                fetch("actions/cancel-adoption.php", {
                    method: "POST",
                    body: new URLSearchParams({ adoptID })
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status === "success") {
                        // Remove the entire card instantly
                        const card = btn.closest(".pet-card-horizontal");
                        if (card) card.remove();

                        alert("Adoption request has been canceled.");
                    } else {
                        alert(data.message);
                    }

                });
            });
        });
    }

    // -------------------- ADMIN: APPROVE / REJECT ADOPTION --------------------

    function adminAction(action, adoptID) {
        fetch("actions/admin-update-adoption.php", {
            method: "POST",
            body: new URLSearchParams({ action, adoptID })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);

            if (data.status === "success") {

                const card = document.querySelector(`[data-adoptid='${adoptID}']`).closest('.pet-card-horizontal');

                // If admin REJECTS, remove the card completely
                if (action === "reject") {
                    if (card) card.remove();
                    return;
                }

                // If admin APPROVES, show approved label
                if (action === "approve") {
                    if (card) {
                        card.querySelector('.pet-card-horizontal-actions').innerHTML =
                            `<span class="status-label">Approved</span>`;
                    }
                }
            }
        });
    }

    const approveButtons = document.querySelectorAll(".approve-btn");
    const rejectButtons = document.querySelectorAll(".reject-btn");

    if (approveButtons.length > 0) {
        approveButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                adminAction("approve", btn.getAttribute("data-adoptid"));
            });
        });
    }

    if (rejectButtons.length > 0) {
        rejectButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                adminAction("reject", btn.getAttribute("data-adoptid"));
            });
        });
    }

        /*---------------------MOBILE NAV BAR------------------------------*/
    const navToggle = document.getElementById("navToggle");
    const navLinks = document.getElementById("navLinks");

    navToggle?.addEventListener("click", () => {
    navLinks.classList.toggle("open");

    const isOpen = navLinks.classList.contains("open");
    navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    navLinks?.querySelectorAll("a").forEach(a => {
    a.addEventListener("click", () => {
        navLinks.classList.remove("open");
        navToggle.setAttribute("aria-expanded", "false");
        });
    });

    /*------------------ADMIN APPROVE BUTTON---------------------*/
    const approveForms = document.querySelectorAll(".approve-form");

    approveForms.forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop immediate submit

            const btn = form.querySelector(".approve-btn");
            btn.disabled = true;

            // Show feedback message
            let msg = document.createElement("div");
            msg.className = "approve-feedback";
            msg.innerText = "Pet approved! Updating...";
            form.appendChild(msg);

            // Delay before submitting
            setTimeout(() => {
                form.submit(); // Now continue to approve-pet.php
            }, 1500);
        });
    });

    /*-----------------ADMIN REJECT BUTTON-----------------------*/
    const rejectForms = document.querySelectorAll(".reject-form");

    rejectForms.forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop immediate submit

            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;

            // Create feedback message
            let msg = document.createElement("div");
            msg.className = "reject-feedback";
            msg.innerText = "Pet rejected. Updating...";
            form.appendChild(msg);

            // Delay before submitting
            setTimeout(() => {
                form.submit(); // continue to reject-pet.php
            }, 1500);
        });
    });


    /*---------------USER CANCEL PET PENDING/REJECTED BUTTON--------------------*/
    const cancelForms = document.querySelectorAll(".cancel-form");

    cancelForms.forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop immediate submit

            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;

            // Create feedback message
            let msg = document.createElement("div");
            msg.className = "cancel-feedback";
            msg.innerText = "Pet canceled. Updating...";
            form.appendChild(msg);

            // Delay before submitting
            setTimeout(() => {
                form.submit(); // continue to delete-pet.php
            }, 1500);
        });
    });

    /*--------------PET-LIST FILTERS--------------------*/
    const applyBtn = document.querySelector(".apply-filters");
    if (applyBtn) {
        applyBtn.addEventListener("click", () => {
            const types = [...document.querySelectorAll("input[name='type']:checked")].map(i => i.value);
            const ages = [...document.querySelectorAll("input[name='age']:checked")].map(i => i.value);
            const sizes = [...document.querySelectorAll("input[name='size']:checked")].map(i => i.value);

            let params = new URLSearchParams();

            if (types.length) params.append("type", types.join(","));
            if (ages.length) params.append("age", ages.join(","));
            if (sizes.length) params.append("size", sizes.join(","));

            
            const sortSelect = document.getElementById("sort");
            if (sortSelect) params.append("sort", sortSelect.value);

            // Always reset to page 1 when filtering
            params.append("page", 1);

            window.location.href = "pet-list.php?" + params.toString();
        });
    }

    const resetBtn = document.querySelector(".reset-filters");
    if (resetBtn) {
        resetBtn.addEventListener("click", () => {
            window.location.href = "pet-list.php";
        });
    }

    /*----------------PET SORT-------------------*/
    const sortSelect = document.getElementById("sort");

    if (sortSelect) {
        sortSelect.addEventListener("change", () => {
                let params = new URLSearchParams();

                // Collect currently checked filters
                const types = [...document.querySelectorAll("input[name='type']:checked")].map(i => i.value);
                const ages = [...document.querySelectorAll("input[name='age']:checked")].map(i => i.value);
                const sizes = [...document.querySelectorAll("input[name='size']:checked")].map(i => i.value);

                if (types.length) params.append("type", types.join(","));
                if (ages.length) params.append("age", ages.join(","));
                if (sizes.length) params.append("size", sizes.join(","));

                // Apply the new sort value
                params.append("sort", sortSelect.value);

                // Reset to first page
                params.append("page", 1);

                window.location.href = "pet-list.php?" + params.toString();
        });
    }

    /*-------------SCROLL ANIMATIONS-----------------*/
    const animateOnScroll = (elements) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        });

        elements.forEach(el => observer.observe(el));
    };

    const animatedElements = document.querySelectorAll('[data-animate]');
    animateOnScroll(animatedElements);

    // HEADER ANIMATION
    const header = document.querySelector('.header');
    if (header) {
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const currentScroll = window.scrollY;

            if (currentScroll > lastScroll && currentScroll > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            if (navLinks && navLinks.classList.contains("open")) {
                if (currentScroll > lastScroll) {  // user is scrolling DOWN
                    navLinks.classList.remove("open");
                    navToggle.setAttribute("aria-expanded", "false");
                }
            }

            lastScroll = currentScroll; 

            if (currentScroll > 50) header.classList.add('scrolled');
            else header.classList.remove('scrolled');
            const navToggle = document.getElementById("navToggle");

            if (currentScroll > lastScroll && currentScroll > 100) {
                navToggle.style.opacity = "0";
                navToggle.style.pointerEvents = "none";
            } else {
                navToggle.style.opacity = "1";
                navToggle.style.pointerEvents = "auto";
            }

        });
    }

    // BUTTON ANIMATION
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.transform = 'translateY(-3px) scale(1.05)';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translateY(0) scale(1)';
        });
    });

    // PETCARD - HOVER ANIMATION
    document.querySelectorAll('.pet-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const angleX = (y - rect.height / 2) / 20;
            const angleY = (rect.width / 2 - x) / 20;

            card.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) translateY(-10px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
        });
    });

    // NAVIGATION LINK CLICK ANIMATION - initialize
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });

    initAnimations();

    //----------------FILE UPLOAD-----------------//
    const petImageInput = document.getElementById("pet-image");
    const fileNameDisplay = document.getElementById("file-name");

    if (petImageInput && fileNameDisplay) {
        petImageInput.addEventListener("change", function () {
            const fileName = this.files.length ? this.files[0].name : "No file chosen";
            fileNameDisplay.textContent = fileName;
        });
    }

});

// LOAD ANIMATION
function initAnimations() {
    // Stagger pet cards
    document.querySelectorAll('.pet-card').forEach((card, index) => {
        card.style.animationDelay = `${0.2 + (index * 0.1)}s`;
    });

    // Stagger sections
    document.querySelectorAll('section').forEach((section, index) => {
        section.style.animationDelay = `${0.3 + (index * 0.15)}s`;
    });
}

// PAGE TRANSITION EFFECT
window.addEventListener('load', function() {
    document.body.classList.add('fade-in');
});
