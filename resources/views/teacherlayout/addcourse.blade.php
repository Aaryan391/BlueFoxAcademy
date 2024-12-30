@extends('teacherlayout.teacherapp')
@section('title', 'Add Course')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card course-creation-card">
                <div class="card-header">
                    <h4 class="card-title">Create New Course</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Course</li>
                        </ol>
                    </nav>
                </div>
                <div class="card-body">
                    <div id="alert-container" class="mb-4">
                        <!-- Dynamic alert messages will be inserted here -->
                    </div>

                    <form id="courseCreateForm" enctype="multipart/form-data" novalidate>
                        <div class="row g-4">
                            <!-- Basic Information (Unchanged) -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Basic Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" required maxlength="255" placeholder="Enter course title">
                                            <div class="invalid-feedback">Please enter a course title</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id" class="form-label">Course Category <span class="text-danger">*</span></label>
                                            <select class="form-control" id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a category</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Details (Unchanged) -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Course Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="skill_level" class="form-label">Skill Level <span class="text-danger">*</span></label>
                                            <select class="form-control" id="skill_level" name="skill_level" required>
                                                <option value="">Select Skill Level</option>
                                                <option value="Beginner">Beginner</option>
                                                <option value="Intermediate">Intermediate</option>
                                                <option value="Advanced">Advanced</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="form-label">Language <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="language" name="language" required placeholder="Course language">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration" class="form-label">Duration (Hours) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="duration" name="duration" required min="1" placeholder="Total course hours">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="has_assessments" class="form-label">Assessments <span class="text-danger">*</span></label>
                                            <select class="form-control" id="has_assessments" name="has_assessments" required>
                                                <option value="">Select Assessment Availability</option>
                                                <option value="0">No Assessments</option>
                                                <option value="1">With Assessments</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule Section (Simplified) -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Course Schedule (Optional)</h5>
                                <div class="form-group">
                                    <textarea class="form-control" id="schedule" name="schedule" rows="4"
                                        placeholder="Enter course schedule as JSON. Example:
[
    {
        'day': 'Monday',
        'start_time': '09:00',
        'end_time': '11:00',
        'location': 'Online Classroom'
    }
] Should be double quote"></textarea>
                                    <small class="text-muted">
                                        Optional JSON array. Each object must have: day, start_time, end_time, location
                                    </small>
                                </div>
                            </div>
                            <!-- Course Type -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Course Type <span class="text-danger">*</span></h5>
                                <div class="course-type-container">
                                    <div class="course-type-option">
                                        <input type="radio" id="type_remote" name="type" value="remote" required>
                                        <label for="type_remote">
                                            <i class="fas fa-laptop fa-2x mb-2"></i>
                                            <h6>Remote Learning</h6>
                                            <small class="text-muted">Learn from anywhere</small>
                                        </label>
                                    </div>
                                    <div class="course-type-option">
                                        <input type="radio" id="type_onsite" name="type" value="onsite" required>
                                        <label for="type_onsite">
                                            <i class="fas fa-building fa-2x mb-2"></i>
                                            <h6>Onsite Training</h6>
                                            <small class="text-muted">In-person learning</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Image -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Course Image</h5>
                                <div class="file-upload-container">
                                    <input type="file" class="form-control" id="course_image" name="course_image" accept="image/*">
                                    <small class="text-muted d-block mt-2">Allowed formats: JPEG, PNG, JPG (Max 4MB)</small>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-4">
                                <h5 class="text-secondary mb-3">Course Description</h5>
                                <div class="form-group">
                                    <textarea class="form-control" id="description" name="description" required rows="4" placeholder="Provide a detailed course description"></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fa fa-save mr-2"></i>Create Course
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    `;
        loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
        document.body.appendChild(loadingOverlay);
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Add role verification before loading dashboard
            const userResponse = await axios.get('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (userResponse.data.role !== 'teacher') {
                window.location.href = '/error';
                return;
            }


            // Set up axios interceptors for handling unauthorized/forbidden responses
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response) {
                        switch (error.response.status) {
                            case 401: // Unauthorized
                                localStorage.removeItem('auth_token');
                                window.location.href = '/login';
                                break;
                            case 403: // Forbidden
                                window.location.href = '/error';
                                break;
                        }
                    }
                    return Promise.reject(error);
                }
            );
            loadingOverlay.remove();
            // Set default authorization header
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            const form = document.getElementById('courseCreateForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alert-container');

            // Fetch categories on page load
            fetchCategories();

            function validateSchedule() {
                const scheduleInput = document.getElementById('schedule').value.trim();
                if (!scheduleInput) return []; // Allow empty schedule

                try {
                    const scheduleArray = JSON.parse(scheduleInput);

                    // Validate schedule array
                    if (!Array.isArray(scheduleArray)) {
                        showAlert('Schedule must be a JSON array', 'danger');
                        return null;
                    }

                    // Optional: Add more detailed validation if needed
                    return scheduleArray;
                } catch (error) {
                    showAlert('Invalid schedule JSON format', 'danger');
                    return null;
                }
            }

            // Form submission handler
            window.addEventListener('submit', async function(event) {
                event.preventDefault();
                event.stopPropagation();

                // Client-side validation
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                // Validate schedule
                const validatedSchedule = validateSchedule();
                if (validatedSchedule === null) {
                    return; // Stop submission if schedule is invalid
                }

                // Prepare form data
                const formData = new FormData(form);

                // Only append schedule if it's not empty
                if (validatedSchedule.length > 0) {
                    formData.append('schedule', JSON.stringify(validatedSchedule));
                }

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Creating...';

                try {
                    // Send course creation request
                    const response = await axios.post('/api/courses', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                        }
                    });

                    // Show success message
                    showAlert('Course created successfully!', 'success');

                    // Reset form
                    form.reset();
                    form.classList.remove('was-validated');

                    // Optional: Redirect or perform additional actions
                    // window.location.href = '/courses';
                } catch (error) {
                    // Handle errors
                    handleError(error);
                } finally {
                    // Restore submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-save mr-2"></i>Create Course';
                }
            });

            // Fetch course categories
            async function fetchCategories() {
                try {
                    const token = localStorage.getItem('auth_token');
                    if (!token) {
                        window.location.href = '/login';
                        return;
                    }

                    const response = await axios.get('/api/courses/categories', {
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    const categorySelect = document.getElementById('category_id');
                    categorySelect.innerHTML = '<option value="">Select Category</option>';

                    response.data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    });
                } catch (error) {
                    handleCategoryFetchError(error);
                }
            }

            // Error handling functions
            function handleCategoryFetchError(error) {
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            showAlert('Session expired. Please log in again.', 'danger');
                            localStorage.removeItem('auth_token');
                            window.location.href = '/login';
                            break;
                        case 403:
                            showAlert('You do not have permission to view categories.', 'warning');
                            break;
                        default:
                            showAlert('Failed to load categories. Please try again.', 'danger');
                    }
                } else {
                    showAlert('Network error. Please check your connection.', 'danger');
                }
            }

            function handleError(error) {
                let errorMessage = 'An unexpected error occurred';

                if (error.response) {
                    if (error.response.status === 422) {
                        const errors = error.response.data.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    } else if (error.response.status === 401) {
                        errorMessage = 'Session expired. Please login again.';
                        localStorage.removeItem('auth_token');
                        window.location.href = '/login';
                    } else if (error.response.status === 403) {
                        errorMessage = 'Unauthorized. Only teachers can add courses.';
                    } else {
                        errorMessage = error.response.data.message || 'Server error';
                    }
                } else if (error.request) {
                    errorMessage = 'No response from server. Please check your connection.';
                }

                showAlert(errorMessage, 'danger');
            }

            function showAlert(message, type = 'info') {
                alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
            }
        } catch (error) {
            console.error('Initialization error:', error);
            window.location.href = error.response?.status === 403 ? '/error' : '/login';
        }
    });
</script>
@endsection