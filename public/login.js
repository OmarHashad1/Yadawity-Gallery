      document.addEventListener('DOMContentLoaded', function() {
        console.log('Login page loaded');
        
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginBtn = document.getElementById('loginBtn');
        const form = document.getElementById('loginForm');
        
        // Form validation functions
        function validateEmail(email) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          return emailRegex.test(email);
        }
        
        function validatePassword(password) {
          return password.length >= 6;
        }
        
        function updateFormGroup(input, isValid) {
          const formGroup = input.closest('.form-group');
          formGroup.classList.remove('has-error', 'has-success');
          
          if (input.value.trim() !== '') {
            if (isValid) {
              formGroup.classList.add('has-success');
            } else {
              formGroup.classList.add('has-error');
            }
          }
        }
        
        function checkFormValidity() {
          const email = emailInput.value.trim();
          const password = passwordInput.value.trim();
          
          const isEmailValid = email !== '' && validateEmail(email);
          const isPasswordValid = password !== '' && validatePassword(password);
          
          updateFormGroup(emailInput, isEmailValid);
          updateFormGroup(passwordInput, isPasswordValid);
          
          loginBtn.disabled = !(isEmailValid && isPasswordValid);
        }
        
        // Real-time validation event listeners
        emailInput.addEventListener('input', checkFormValidity);
        emailInput.addEventListener('blur', checkFormValidity);
        passwordInput.addEventListener('input', checkFormValidity);
        passwordInput.addEventListener('blur', checkFormValidity);
        
        // Initial validation check
        setTimeout(checkFormValidity, 100);
        
        // Form submission handler
        form.addEventListener('submit', function(e) {
          e.preventDefault(); // Prevent default form submission
          
          const email = emailInput.value.trim();
          const password = passwordInput.value.trim();
          
          // Validate form before submitting
          if (!validateEmail(email) || !validatePassword(password)) {
            Swal.fire({
              icon: 'error',
              title: 'Invalid Input',
              text: 'Please enter a valid email and password (minimum 6 characters).',
              confirmButtonColor: '#667eea'
            });
            return;
          }
          
          // Show loading indicator
          Swal.fire({
            title: 'Signing you in...',
            text: 'Please wait while we authenticate your credentials',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          // Prepare form data
          const formData = new FormData();
          formData.append('email', email);
          formData.append('password', password);
          
          // Send AJAX request to authentication endpoint
          fetch('./API/login.php?action=authenticate', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            // Check if response is ok before trying to parse JSON
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
              throw new Error('Response is not JSON');
            }
            
            return response.json();
          })
          .then(data => {
            if (data.success) {
              // Success - show welcome message and redirect
              Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: data.message,
                confirmButtonText: 'Continue',
                confirmButtonColor: '#667eea',
                timer: 2000,
                timerProgressBar: true
              }).then((result) => {
                // Redirect based on user type
                if (data.data && data.data.redirect_url) {
                  window.location.href = data.data.redirect_url;
                } else {
                  window.location.href = 'index.php'; // Default redirect
                }
              });
            } else {
              // Error - show error message
              Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: data.message || 'Invalid email or password. Please try again.',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#667eea'
              });
            }
          })
          .catch(error => {
            console.error('Login error:', error);
            
            // More specific error messages based on error type
            let errorMessage = 'Unable to connect to the server. Please try again.';
            
            if (error.message.includes('HTTP error')) {
              errorMessage = 'Server error occurred. Please try again later.';
            } else if (error.message.includes('JSON')) {
              errorMessage = 'Invalid server response. Please contact support.';
            } else if (error.message.includes('Failed to fetch')) {
              errorMessage = 'Network connection failed. Please check your internet connection.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: errorMessage,
              confirmButtonText: 'Retry',
              confirmButtonColor: '#667eea'
            });
          });
        });
        
        // Forgot password handler
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
          e.preventDefault();
          Swal.fire({
            icon: 'info',
            title: 'Password Recovery',
            text: 'Password recovery feature coming soon! Please contact support for assistance.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#667eea'
          });
        });
      });