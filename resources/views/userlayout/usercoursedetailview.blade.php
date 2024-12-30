@extends('userlayout.userapp')
@section('title', 'Course Details')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    .course-header {
        background-color: var(--primary-color);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
    }

    .course-container {
        background-color: var(--light-background);
        min-height: 100vh;
    }

    .detail-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
    }

    .course-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .info-box {
        background-color: rgba(83, 92, 145, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        background-color: var(--accent-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .description-box {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-title {
        color: var(--secondary-color);
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--accent-color);
    }
</style>
<div id="messageContainer" class="alert alert-warning d-none" role="alert">
            <!-- Message will be injected here -->
        </div>
<div class="course-container">
    <div id="courseDetails">
        <!-- Course details will be populated here -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseDetailsContainer = document.getElementById('courseDetails');
    const courseId = window.location.pathname.split('/').pop();

    // Utility Functions
    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const formattedHour = hour % 12 || 12;
        return `${formattedHour}:${minutes} ${ampm}`;
    }

    function formatSchedule(scheduleStr) {
        try {
            const schedule = JSON.parse(scheduleStr);
            if (!Array.isArray(schedule) || schedule.length === 0) {
                return '<p class="mb-0">No schedule available</p>';
            }

            return schedule.map(slot => `
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">${slot.day}</h5>
                        <p class="mb-0">${formatTime(slot.start_time)} - ${formatTime(slot.end_time)}</p>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error parsing schedule:', error);
            return '<p class="mb-0">Schedule format error</p>';
        }
    }

    // Form Generation Functions
    function createAdmissionForm(course) {
    // Parse the schedule JSON string into an array
    const scheduleOptions = JSON.parse(course.schedule || '[]');
    
    return `
        <div class="modal fade" id="admissionModal" tabindex="-1" aria-labelledby="admissionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="admissionModalLabel">Apply for Admission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="admissionForm">
                            <div class="mb-3">
                                <label class="form-label">Selected Course</label>
                                <input type="text" class="form-control" value="${course.title}" disabled>
                                <input type="hidden" name="course_id" value="${course.id}">
                            </div>
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobileNumber" name="mobile_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="courseType" class="form-label">Course Type</label>
                                <select class="form-control" id="courseType" name="type" required>
                                    <option value="">Select Course Type</option>
                                    ${course.type === 'remote' ? '<option value="remote">Remote Learning</option>' : ''}
                                    ${course.type === 'onsite' ? '<option value="onsite">Onsite Training</option>' : ''}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="scheduleSelect" class="form-label">Preferred Schedule</label>
                                <select class="form-control" id="scheduleSelect" name="schedule" required>
                                    <option value="">Select Schedule</option>
                                    ${scheduleOptions.map((slot, index) => `
                                        <option value='${JSON.stringify(slot)}'>
                                            ${slot.day} (${formatTime(slot.start_time)} - ${formatTime(slot.end_time)})
                                            ${slot.location ? ` - ${slot.location}` : ''}
                                        </option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="alert alert-success d-none" id="admissionSuccessMessage">
                                Your admission request has been submitted successfully!
                            </div>
                            <div class="alert alert-danger d-none" id="admissionErrorMessage"></div>
                            <button type="submit" class="btn btn-primary w-100">Submit Application</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    `;
}

    function createInquiryForm(course) {
        return `
            <div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="inquiryModalLabel">Send Inquiry</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="inquiryForm">
                                <div class="mb-3">
                                    <label class="form-label">Selected Course</label>
                                    <input type="text" class="form-control" value="${course.title}" disabled>
                                    <input type="hidden" name="course_id" value="${course.id}">
                                </div>
                                <div class="mb-3">
                                    <label for="inquiryFullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="inquiryFullName" name="full_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="inquiryEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="inquiryEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>
                                <div class="alert alert-success d-none" id="inquirySuccessMessage">
                                    Your inquiry has been submitted successfully!
                                </div>
                                <div class="alert alert-danger d-none" id="inquiryErrorMessage"></div>
                                <button type="submit" class="btn btn-primary w-100">Submit Inquiry</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function generateCourseDetailsHTML(course, category) {
        return `
            <div class="course-header">
                <div class="container">
                    <h2 class="mb-2">${course.title}</h2>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <img src="${course.course_image ? `/storage/${course.course_image}` : '/storage/courses/default.jpg'}" 
                             class="course-image" alt="${course.title}">
                        
                        <div class="description-box mb-4">
                            <h3 class="section-title">Course Description</h3>
                            <p>${course.description}</p>
                        </div>
                        
                        <div class="detail-card p-4">
                            <h3 class="section-title">Course Schedule</h3>
                            <div class="info-box">
                                ${formatSchedule(course.schedule || '[]')}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="detail-card p-4">
                            <h3 class="section-title">Course Details</h3>
                            <div class="info-box">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Duration</h5>
                                        <p class="mb-0">${course.duration} hours</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Skill Level</h5>
                                        <p class="mb-0">${course.skill_level}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Language</h5>
                                        <p class="mb-0">${course.language}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Category</h5>
                                        <p class="mb-0">${category}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-${course.type === 'remote' ? 'laptop' : 'building'}"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Course Type</h5>
                                        <p class="mb-0">${course.type.charAt(0).toUpperCase() + course.type.slice(1)}</p>
                                    </div>
                                </div>
                                <button type="button" 
                                    class="btn btn-primary w-100 mb-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#admissionModal">
                                    Get Admission
                                </button>
                                <button type="button" 
                                    class="btn btn-outline-primary w-100" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#inquiryModal">
                                    Send Inquiry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Form Handling Functions
    async function initializeAdmissionForm() {
    const form = document.getElementById('admissionForm');
    const successMessage = document.getElementById('admissionSuccessMessage');
    const errorMessage = document.getElementById('admissionErrorMessage');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Verify user authentication and role
            try {
                const userResponse = await axios.get('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (userResponse.data.role !== 'user') {
                    window.location.href = '/error';
                    return;
                }
            } catch (authError) {
                console.error('Authentication error:', authError);
                if (authError.response?.status === 401) {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Authentication failed');
            }

            // Prepare form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Submit admission request
            const response = await axios.post('/api/course-admission', data, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            // Handle success
            form.reset();
            successMessage.classList.remove('d-none');
            errorMessage.classList.add('d-none');

            // Auto-close modal after success
            setTimeout(() => {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('admissionModal'));
                if (modalInstance) {
                    modalInstance.hide();
                }
                successMessage.classList.add('d-none');
            }, 2000);

        } catch (error) {
            console.error('Submission error:', error);
            
            
            // Handle specific error cases
            let errorMsg = 'An error occurred. Please try again.';
            if (error.response) {
                switch (error.response.status) {
                    case 422:
                        errorMsg = 'Please check your input and try again.';
                        break;
                    case 403:
                        errorMsg = 'You are not authorized to perform this action.';
                        break;
                    case 429:
                        errorMsg = 'Too many requests. Please try again later.';
                        break;
                    default:
                        errorMsg = error.response.data?.message || errorMsg;
                }
            }

            // Display error message
            errorMessage.textContent = error.response.data.message || 'An error occurred. Please try again.';
            errorMessage.classList.remove('d-none');
            successMessage.classList.add('d-none');
        }
    });
}

    function initializeInquiryForm() {
        const form = document.getElementById('inquiryForm');
        const successMessage = document.getElementById('inquirySuccessMessage');
        const errorMessage = document.getElementById('inquiryErrorMessage');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            axios.post('/api/course-inquiry', data)
                .then(response => {
                    form.reset();
                    successMessage.classList.remove('d-none');
                    errorMessage.classList.add('d-none');
                    setTimeout(() => {
                        $('#inquiryModal').modal('hide');
                        successMessage.classList.add('d-none');
                    }, 2000);
                })
                .catch(error => {
                    errorMessage.textContent = error.response?.data?.message || 'An error occurred. Please try again.';
                    errorMessage.classList.remove('d-none');
                    successMessage.classList.add('d-none');
                });
        });
    }

    // Main Function to Fetch and Display Course Details
    function fetchCourseDetails() {
        const token = localStorage.getItem('auth_token');
        const headers = token ? { 'Authorization': `Bearer ${token}` } : {};

        axios.get(`/api/course/${courseId}`, { headers })
            .then(response => {
                const course = response.data.data;
                const category = course.category ? course.category.name : 'N/A';
                
                // Add forms to document
                document.body.insertAdjacentHTML('beforeend', createAdmissionForm(course));
                document.body.insertAdjacentHTML('beforeend', createInquiryForm(course));
                
                // Render course details
                courseDetailsContainer.innerHTML = generateCourseDetailsHTML(course, category);
                
                // Initialize form handlers
                initializeAdmissionForm();
                initializeInquiryForm();
            })
            .catch(error => {
                console.error('Error fetching course details:', error);
                courseDetailsContainer.innerHTML = '<div class="container"><div class="alert alert-danger">Error loading course details. Please try again later.</div></div>';
            });
    }

    // Initialize
    fetchCourseDetails();
});
</script>

@endsection