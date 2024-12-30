@extends('userlayout.userapp')
@section('title', 'Become a Teacher')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
.form-floating > label {
    padding-left: 1.5rem;
}
.form-control:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
}
.btn-primary {
    background-color: #4F46E5;
    border-color: #4F46E5;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background-color: #4338CA;
    border-color: #4338CA;
    transform: translateY(-1px);
}
.card {
    border-radius: 1rem;
}
.form-floating textarea.form-control {
    padding-top: 1.625rem;
}
.alert {
    border-radius: 0.75rem;
}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-md-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold display-6 text-primary mb-3">Teacher Application</h2>
                        <p class="text-muted">Join our teaching community and share your expertise</p>
                    </div>
                    
                    <form id="teacherRequestForm" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="occupation" name="occupation" required 
                                           placeholder="Your current occupation">
                                    <label for="occupation">Occupation</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           placeholder="Your current employer">
                                    <label for="company_name">Company Name (Optional)</label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-floating mb-4">
                                    <textarea class="form-control" id="teacher_expertise" name="teacher_expertise" 
                                              style="height: 150px" required 
                                              placeholder="Describe your teaching expertise"></textarea>
                                    <label for="teacher_expertise">Teaching Expertise</label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <h5 class="text-muted mb-4">Social Media Profiles</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                           placeholder="LinkedIn Profile">
                                    <label for="linkedin">
                                        <i class="bi bi-linkedin me-2"></i>LinkedIn Profile
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="facebook" name="facebook" 
                                           placeholder="Facebook Profile">
                                    <label for="facebook">
                                        <i class="bi bi-facebook me-2"></i>Facebook Profile
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="twitter" name="twitter" 
                                           placeholder="Twitter Profile">
                                    <label for="twitter">
                                        <i class="bi bi-twitter me-2"></i>Twitter Profile
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="url" class="form-control" id="instagram" name="instagram" 
                                           placeholder="Instagram Profile">
                                    <label for="instagram">
                                        <i class="bi bi-instagram me-2"></i>Instagram Profile
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert mt-4" id="message" role="alert" style="display: none;"></div>
                        
                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                Submit Teacher Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Required JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 bg-white d-flex justify-content-center align-items-center';
    loadingOverlay.style.zIndex = '9999';
    loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
    document.body.appendChild(loadingOverlay);

    try {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        const userResponse = await axios.get('/api/user', {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (userResponse.data.role !== 'user') {
            window.location.href = '/error';
            return;
        }
       
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            localStorage.removeItem('auth_token');
                            window.location.href = '/login';
                            break;
                        case 403:
                            window.location.href = '/login';
                            break;
                    }
                }
                return Promise.reject(error);
            }
        );

        loadingOverlay.remove();
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        const form = document.getElementById('teacherRequestForm');
        const messageElement = document.getElementById('message');
        const submitButton = form.querySelector('button[type="submit"]');
        const spinner = submitButton.querySelector('.spinner-border');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            messageElement.style.display = 'none';
            submitButton.disabled = true;
            spinner.classList.remove('d-none');

            const formData = {
                occupation: document.getElementById('occupation').value,
                company_name: document.getElementById('company_name').value || null,
                teacher_expertise: document.getElementById('teacher_expertise').value,
                linkedin: document.getElementById('linkedin').value || null,
                facebook: document.getElementById('facebook').value || null,
                twitter: document.getElementById('twitter').value || null,
                instagram: document.getElementById('instagram').value || null
            };

            try {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    window.location.href = '/login';
                    return;
                }

                const response = await axios.post('/api/teacher-requests', formData, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                messageElement.className = 'alert alert-success mt-4';
                messageElement.textContent = response.data.message;
                messageElement.style.display = 'block';
                form.reset();
            } catch (error) {
                let errorMessage = 'An error occurred. Please try again.';
                
                if (error.response && error.response.data.errors) {
                    errorMessage = Object.values(error.response.data.errors).flat().join(', ');
                } else if (error.response && error.response.data.message) {
                    errorMessage = error.response.data.message;
                }

                messageElement.className = 'alert alert-danger mt-4';
                messageElement.textContent = errorMessage;
                messageElement.style.display = 'block';
            } finally {
                submitButton.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection