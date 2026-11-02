/* =====================================================
   EMPOWERTECH TRAINING CENTER - ENHANCED JAVASCRIPT
   ===================================================== */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    /* =====================================================
       MOBILE MENU TOGGLE FUNCTIONALITY
       ===================================================== */
    
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');
    
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event from bubbling
            nav.classList.toggle('active');
            
            // Update ARIA attribute for accessibility
            const isExpanded = nav.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
            
            // Update button text
            menuToggle.innerHTML = isExpanded ? '✕ CLOSE' : '☰ MENU';
        });
        
        // Close menu when clicking on a nav link
        const navLinks = nav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.innerHTML = '☰ MENU';
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!nav.contains(e.target) && !menuToggle.contains(e.target)) {
                if (nav.classList.contains('active')) {
                    nav.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.innerHTML = '☰ MENU';
                }
            }
        });
    }
    
    /* =====================================================
       TESTIMONIAL CAROUSEL ENHANCEMENT
       ===================================================== */
    
    const testimonials = document.querySelectorAll('.testimonial');
    
    if (testimonials.length > 0) {
        let currentIndex = 0;
        let autoPlayInterval;
        
        // Create and add navigation dots
        const carousel = document.querySelector('.testimonial-carousel');
        if (carousel) {
            const dotsContainer = document.createElement('div');
            dotsContainer.className = 'carousel-dots';
            dotsContainer.setAttribute('role', 'tablist');
            dotsContainer.setAttribute('aria-label', 'Testimonial navigation');
            
            testimonials.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.className = 'carousel-dot';
                dot.setAttribute('role', 'tab');
                dot.setAttribute('aria-label', `View testimonial ${index + 1}`);
                dot.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
                
                if (index === 0) {
                    dot.classList.add('active');
                }
                
                dot.addEventListener('click', () => goToSlide(index));
                dotsContainer.appendChild(dot);
            });
            
            carousel.appendChild(dotsContainer);
        }
        
        /**
         * Navigate to specific slide
         */
        const goToSlide = (index) => {
            // Remove active class from current testimonial and dot
            testimonials[currentIndex].classList.remove('active');
            const dots = document.querySelectorAll('.carousel-dot');
            if (dots[currentIndex]) {
                dots[currentIndex].classList.remove('active');
                dots[currentIndex].setAttribute('aria-selected', 'false');
            }
            
            // Update index
            currentIndex = index;
            
            // Add active class to new testimonial and dot
            testimonials[currentIndex].classList.add('active');
            if (dots[currentIndex]) {
                dots[currentIndex].classList.add('active');
                dots[currentIndex].setAttribute('aria-selected', 'true');
            }
        };
        
        /**
         * Auto-advance carousel every 5 seconds
         */
        const startAutoPlay = () => {
            autoPlayInterval = setInterval(() => {
                const nextIndex = (currentIndex + 1) % testimonials.length;
                goToSlide(nextIndex);
            }, 5000);
        };
        
        const stopAutoPlay = () => {
            clearInterval(autoPlayInterval);
        };
        
        // Start auto-play
        startAutoPlay();
        
        // Pause auto-play when user hovers over carousel
        if (carousel) {
            carousel.addEventListener('mouseenter', stopAutoPlay);
            carousel.addEventListener('mouseleave', startAutoPlay);
        }
    }
    
    /* =====================================================
       FORM VALIDATION (Only runs on pages with forms)
       ===================================================== */
    
    const form = document.querySelector('form');
    
    if (form) {
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const programSelect = document.getElementById('program');
        const messageTextarea = document.getElementById('message');
        
        // Only proceed if form elements exist
        if (nameInput && emailInput) {
            
            // Create error message elements for each field
            const createErrorElement = (inputElement) => {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.setAttribute('role', 'alert');
                errorDiv.setAttribute('aria-live', 'polite');
                inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
                return errorDiv;
            };
            
            // Error message containers
            const nameError = createErrorElement(nameInput);
            const emailError = createErrorElement(emailInput);
            
            /* =====================================================
               VALIDATION FUNCTIONS
               ===================================================== */
            
            /**
             * Validates name field
             * Requirements: At least 2 characters, letters and spaces only
             */
            const validateName = () => {
                const name = nameInput.value.trim();
                
                if (name === '') {
                    showError(nameInput, nameError, 'Please enter your full name.');
                    return false;
                }
                
                if (name.length < 2) {
                    showError(nameInput, nameError, 'Name must be at least 2 characters long.');
                    return false;
                }
                
                // Check for valid characters (letters, spaces, hyphens, apostrophes)
                const nameRegex = /^[a-zA-Z\s'-]+$/;
                if (!nameRegex.test(name)) {
                    showError(nameInput, nameError, 'Name can only contain letters, spaces, hyphens, and apostrophes.');
                    return false;
                }
                
                clearError(nameInput, nameError);
                return true;
            };
            
            /**
             * Validates email field
             * Requirements: Valid email format
             */
            const validateEmail = () => {
                const email = emailInput.value.trim();
                
                if (email === '') {
                    showError(emailInput, emailError, 'Please enter your email address.');
                    return false;
                }
                
                // Comprehensive email validation regex
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showError(emailInput, emailError, 'Please enter a valid email address.');
                    return false;
                }
                
                clearError(emailInput, emailError);
                return true;
            };
            
            /**
             * Shows error message and styling
             */
            const showError = (input, errorElement, message) => {
                input.classList.add('input-error');
                input.setAttribute('aria-invalid', 'true');
                errorElement.textContent = message;
                errorElement.classList.add('show');
            };
            
            /**
             * Clears error message and styling
             */
            const clearError = (input, errorElement) => {
                input.classList.remove('input-error');
                input.setAttribute('aria-invalid', 'false');
                errorElement.textContent = '';
                errorElement.classList.remove('show');
            };
            
            /* =====================================================
               REAL-TIME VALIDATION
               ===================================================== */
            
            // Validate name on blur (when user leaves the field)
            nameInput.addEventListener('blur', validateName);
            
            // Clear name error as user types (after initial validation)
            nameInput.addEventListener('input', () => {
                if (nameError.classList.contains('show')) {
                    validateName();
                }
            });
            
            // Validate email on blur
            emailInput.addEventListener('blur', validateEmail);
            
            // Clear email error as user types (after initial validation)
            emailInput.addEventListener('input', () => {
                if (emailError.classList.contains('show')) {
                    validateEmail();
                }
            });
            
            /* =====================================================
               FORM SUBMISSION HANDLING
               ===================================================== */
            
            form.addEventListener('submit', function(e) {
                // Prevent default form submission
                e.preventDefault();
                
                // Validate all fields
                const isNameValid = validateName();
                const isEmailValid = validateEmail();
                
                // If all validations pass
                if (isNameValid && isEmailValid) {
                    // Get form values
                    const name = nameInput.value.trim();
                    const email = emailInput.value.trim();
                    const program = programSelect ? programSelect.options[programSelect.selectedIndex].text : 'General Inquiry';
                    const message = messageTextarea ? messageTextarea.value.trim() : '';
                    
                    // Disable submit button to prevent double submission
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Submitting...';
                    }
                    
                    // Simulate form submission (in production, this would send to server)
                    setTimeout(() => {
                        // Show success message
                        showSuccessMessage(name, program);
                        
                        // Reset form
                        form.reset();
                        
                        // Re-enable submit button
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = 'Submit Registration';
                        }
                        
                        // Clear any remaining error states
                        clearError(nameInput, nameError);
                        clearError(emailInput, emailError);
                    }, 1000);
                } else {
                    // Focus on first invalid field
                    if (!isNameValid) {
                        nameInput.focus();
                    } else if (!isEmailValid) {
                        emailInput.focus();
                    }
                }
            });
            
            /**
             * Displays a success message to the user
             */
            const showSuccessMessage = (name, program) => {
                // Create success message container
                const successDiv = document.createElement('div');
                successDiv.className = 'success-message';
                successDiv.setAttribute('role', 'alert');
                successDiv.setAttribute('aria-live', 'polite');
                
                successDiv.innerHTML = `
                    <h3>✓ Registration Successful!</h3>
                    <p>Thank you, <strong>${name}</strong>! Your interest in the <strong>${program}</strong> program has been recorded.</p>
                    <p><small>This is a demo form. In production, you would receive a confirmation email shortly.</small></p>
                `;
                
                // Insert before the form
                form.parentNode.insertBefore(successDiv, form);
                
                // Scroll to success message
                successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Remove success message after 8 seconds
                setTimeout(() => {
                    successDiv.style.transition = 'opacity 0.5s ease';
                    successDiv.style.opacity = '0';
                    setTimeout(() => successDiv.remove(), 500);
                }, 10000);
            };
        }
    }
    
    /* =====================================================
       SMOOTH SCROLL FOR ANCHOR LINKS
       ===================================================== */
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#main-content') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    /* =====================================================
       SCROLL-TO-TOP BUTTON
       ===================================================== */
    
    // Create scroll to top button
    const scrollButton = document.createElement('button');
    scrollButton.className = 'scroll-to-top';
    scrollButton.innerHTML = '↑';
    scrollButton.setAttribute('aria-label', 'Scroll to top');
    scrollButton.setAttribute('title', 'Back to top');
    document.body.appendChild(scrollButton);
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollButton.classList.add('visible');
        } else {
            scrollButton.classList.remove('visible');
        }
    });
    
    // Scroll to top when clicked
    scrollButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    /* =====================================================
       ANIMATED STATS COUNTER (for homepage stats)
       ===================================================== */
    
    const statElements = document.querySelectorAll('.stat-number');
    
    if (statElements.length > 0) {
        const animateValue = (element, start, end, duration, suffix = '') => {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentValue = Math.floor(progress * (end - start) + start);
                element.textContent = currentValue + suffix;
                if (progress < 