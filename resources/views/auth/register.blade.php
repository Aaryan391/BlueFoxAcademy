<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blue Fox Academy - Sign Up</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #070F2B;
            --secondary-color: #1B1A55;
            --accent-color: #535C91;
            --light-background: #F5F5F5;
            --text-color: #333333;
            --hover-color: #9290C3;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
        }
        
        .signup-container {
            width: 100%;
            max-width: 450px;
            margin: 1rem;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .signup-header {
            background: var(--primary-color);
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        
        .signup-header img {
            max-height: 50px;
        }
        
        .signup-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            clip-path: polygon(0 0, 100% 0, 50% 100%);
        }
        
        .signup-form {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-floating > label {
            color: var(--text-color);
        }
        
        .form-control {
            border: 2px solid #eee;
            padding: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(83, 92, 145, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .social-signup {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .social-btn {
            width: 100%;
            padding: 0.7rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .facebook-btn {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .google-btn {
            background-color: var(--accent-color);
            color: white;
        }
        
        .social-btn:hover {
            background-color: var(--hover-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .social-btn i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 0.8rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.9rem;
            display: none;
        }
        
        .form-text {
            font-size: 0.9rem;
            color: var(--text-color);
        }
        
        .signup-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .login-link {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link:hover {
            color: var(--hover-color);
        }
    </style>
</head>

<body>
    <div class="signup-container">
        <div class="signup-header">
            <img src="{{url('frontend/assets/images/logo-white-2.png')}}" alt="EduChamp Logo">
        </div>
        
        <div class="signup-form">
            <h2 class="signup-title text-center">Create Account</h2>
            <p class="text-center mb-4 form-text">Already registered? <a href="/login" class="login-link">Sign In</a></p>
            
            <form id="signup-form">
                @csrf
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                    <label for="name">Full Name</label>
                </div>
                
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                    <label for="email">Email Address</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mt-3">Create Account</button>
                
                <div class="social-signup">
                    <p class="text-center form-text mb-3">Or continue with</p>
                    
                    <a href="#" class="social-btn facebook-btn mb-2">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </a>
                    
                    <a href="#" class="social-btn google-btn">
                        <i class="fab fa-google"></i>
                        Google
                    </a>
                </div>
            </form>
            
            <div id="error-message" class="error-message"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        document.getElementById('signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            axios.post('/api/register', {
                name: name,
                email: email,
                password: password,
                password_confirmation: password,
            })
            .then(function(response) {
                window.location.href = "/login";
            })
            .catch(function(error) {
                const errorDiv = document.getElementById('error-message');
                errorDiv.style.display = 'block';
                errorDiv.innerText = error.response?.data?.message || 'Registration failed!';
            });
        });
    </script>
</body>
</html>