<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #070F2B;
            --secondary-color: #1B1A55;
            --accent-color: #535C91;
            --light-background: #F0F4F8;
            --text-color: #333333;
            --hover-color: #9290C3;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-attachment: fixed;
        }

        .login-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(83, 92, 145, 0.1);
            transform: rotate(-45deg);
            z-index: -1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 15px;
        }

        .login-header p {
            color: var(--text-color);
            opacity: 0.8;
        }

        .form-control {
            border-color: var(--accent-color);
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(83, 92, 145, 0.25);
            background-color: white;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            border-radius: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }

        #error-message {
            text-align: center;
            margin-top: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="login-container">
                    <div class="login-header">
                        <h2>Login to your <span>Account</span></h2>
                        <p>Don't have an account? <a href="/register" class="text-decoration-none">Create one here</a></p>
                    </div>
                    
                    <form id="signin-form">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                        <div id="error-message" class="text-danger"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios for API requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if user is already authenticated
        const token = localStorage.getItem('auth_token');
        if (token) {
            // Verify token validity and redirect if necessary
            verifyAuthenticationStatus();
        }
    });

    async function verifyAuthenticationStatus() {
        try {
            const response = await axios.get('/api/user', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            
            // If verified, redirect based on role
            const user = response.data;
            redirectBasedOnRole(user.role);
        } catch (error) {
            // If token is invalid, clear it
            localStorage.removeItem('auth_token');
        }
    }

    function redirectBasedOnRole(role) {
        const redirectPaths = {
            'admin': '/admindashboard',
            'teacher': '/teacherdashboard',
            'user': '/'
        };
        
        window.location.href = redirectPaths[role] || '/';
    }

    document.getElementById('signin-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const errorMessageElement = document.getElementById('error-message');
        const submitButton = this.querySelector('button[type="submit"]');
        
        try {
            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
            errorMessageElement.innerText = '';
            
            const response = await axios.post('/api/login', {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            });

            const { data } = response.data;
            
            // Store authentication token
            localStorage.setItem('auth_token', data.token);
            
            // Set default authorization header for future requests
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            
            // Redirect based on role
            window.location.href = data.redirect;
            
        } catch (error) {
            // Handle different types of errors
            let errorMessage = 'An unexpected error occurred. Please try again.';
            
            if (error.response) {
                switch (error.response.status) {
                    case 422: // Validation error
                        errorMessage = error.response.data.message || 'Invalid credentials.';
                        break;
                    case 429: // Too many attempts
                        errorMessage = 'Too many login attempts. Please try again later.';
                        break;
                    case 401: // Unauthorized
                        errorMessage = 'Invalid email or password.';
                        break;
                }
            }
            
            errorMessageElement.innerText = errorMessage;
        } finally {
            // Reset submit button state
            submitButton.disabled = false;
            submitButton.innerHTML = 'Login';
        }
    });
</script>
</body>
</html>