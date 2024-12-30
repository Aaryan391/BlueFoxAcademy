@extends('userlayout.userapp')
@section('title', 'Blue Fox Academy')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
@section('content')
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 80vh;
            position: relative;
            overflow: hidden;
            margin: -30px -30px 0 -30px; /* Compensate for main-content padding */
            padding: 30px;
			margin-top: 1rem;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            height: 100px;
            background: var(--light-background);
            transform: skewY(-3deg);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 60px auto 0;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .hero-description {
            font-size: clamp(1.1rem, 2vw, 1.25rem);
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }
        
        .hero-image {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }
        
        .hero-image:hover {
            transform: perspective(1000px) rotateY(0deg);
        }
        
        .cta-button {
            background: var(--light-background);
            color: var(--primary-color);
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 2px solid transparent;
            text-decoration: none;
            display: inline-block;
        }
        
        .cta-button:hover {
            background: transparent;
            color: white;
            border-color: white;
            transform: translateY(-2px);
        }
        
        .features-section {
            padding: 80px 0;
            background: white;
            position: relative;
            z-index: 2;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
        }
        
        .cta-section {
            background: var(--secondary-color);
            padding: 5rem 0;
            margin: 0 -30px;
            position: relative;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--accent-color), var(--hover-color));
        }
        
        .cta-content {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
            color: white;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta-description {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
		.custom-footer {
        background: var(--primary-color);
        margin: 4rem -30px -30px -30px;
        padding: 4rem 0 2rem;
        position: relative;
        z-index: 10;
    }

    .footer-heading {
        color: var(--hover-color);
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.8rem;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: var(--accent-color);
        border-radius: 2px;
    }

    .footer-content {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        line-height: 1.8;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        display: block;
        padding: 0.3rem 0;
    }

    .footer-links a:hover {
        color: var(--hover-color);
        transform: translateX(5px);
    }

    .footer-links i {
        margin-right: 10px;
        color: var(--accent-color);
        width: 20px;
        text-align: center;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .social-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-3px);
    }

    .footer-bottom {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        color: rgba(255, 255, 255, 0.6);
    }
    </style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Transform Your Future with Blue Fox Academy</h1>
                    <p class="hero-description">Unlock your potential with industry-leading courses designed to launch your career in technology and innovation.</p>
                    <a href="/usercourselist" class="cta-button">Browse Courses</a>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <img src="/api/placeholder/600/400" alt="Students learning" class="img-fluid hero-image" />
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-laptop-code feature-icon"></i>
                        <h3>Industry-Relevant Curriculum</h3>
                        <p>Our courses are designed in collaboration with industry experts to ensure you learn the most in-demand skills.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-users feature-icon"></i>
                        <h3>Expert Instructors</h3>
                        <p>Learn from experienced professionals who bring real-world expertise to the classroom.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-certificate feature-icon"></i>
                        <h3>Recognized Certification</h3>
                        <p>Earn industry-recognized certifications that boost your career prospects.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Begin Your Journey?</h2>
                <p class="cta-description">Join thousands of successful graduates who transformed their careers with Blue Fox Academy.</p>
                <a href="/usercourselist" class="cta-button">Explore Courses</a>
            </div>
        </div>
    </section>
	
<footer class="custom-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h4 class="footer-heading">About Blue Fox Academy</h4>
                <div class="footer-content">
                    <p>Leading the way in technology education, Blue Fox Academy provides cutting-edge courses designed to transform careers and empower future tech leaders.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/usercourselist"><i class="fas fa-graduation-cap"></i>Available Courses</a></li>
                    <li><a href="/requestrole"><i class="fas fa-user-plus"></i>Request Role</a></li>
                    <li><a href="#"><i class="fas fa-book"></i>Learning Resources</a></li>
                    <li><a href="#"><i class="fas fa-headset"></i>Student Support</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h4 class="footer-heading">Connect With Us</h4>
                <div class="footer-content">
                    <p>Stay updated with our latest courses and educational insights through our social media channels.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Blue Fox Academy. All rights reserved.</p>
        </div>
    </div>
</footer>

    <script>
        // Intersection Observer for animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        // Animate feature cards on scroll
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            observer.observe(card);
        });
    </script>
@endsection