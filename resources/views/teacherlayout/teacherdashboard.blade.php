@extends('teacherlayout.teacherapp')

@section('title', 'Teacher Dashboard')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    .dashboard-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }

    .performance-metric {
        background: linear-gradient(145deg, var(--light-background), #ffffff);
        padding: 20px;
        text-align: center;
        border-radius: 12px;
    }

    .performance-metric h5 {
        color: var(--text-color);
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .performance-metric h3 {
        color: var(--primary-color);
        font-weight: bold;
    }

    .card-header {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .course-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s ease;
    }

    .course-item:hover {
        background-color: var(--hover-color);
        color: #ffffff;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }

    @media (max-width: 768px) {
        .performance-metric {
            margin-bottom: 15px;
        }

        .course-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .course-details {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center my-4" style="color: var(--primary-color);">
                Teacher Dashboard
            </h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Performance Metrics -->
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-card performance-metric">
                <h5>Total Courses</h5>
                <h3 id="total-courses" class="text-primary">0</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-card performance-metric">
                <h5>Total Students</h5>
                <h3 id="total-students" class="text-primary">0</h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-card performance-metric">
                <h5>Avg Students/Course</h5>
                <h3 id="avg-students" class="text-info">0</h3>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h4 class="mb-0">Your Courses</h4>
                </div>
                <div class="card-body p-0" id="courses-container">
                    <!-- Courses will be dynamically loaded -->
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h4 class="mb-0">Recent Enrollments</h4>
                </div>
                <ul class="list-group list-group-flush" id="recent-enrollments">
                    <!-- Recent enrollments will be dynamically loaded -->
                </ul>
            </div>
        </div>
    </div>
</div>

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
            window.location.href = '/error';
            return;
        }

        // Add role verification before loading dashboard
        const userResponse = await axios.get('/api/user', {
            headers: { 'Authorization': `Bearer ${token}` }
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

        async function fetchDashboardData() {
            try {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    window.location.href = '/error';
                    return;
                }

                const response = await axios.get('/api/teacher/dashboard', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const {
                    total_courses,
                    total_students,
                    performance_metrics,
                    courses,
                    recent_enrollments
                } = response.data.data;

                // Update performance metrics
                document.getElementById('total-courses').textContent = total_courses;
                document.getElementById('total-students').textContent = total_students;
                document.getElementById('avg-students').textContent =
                    performance_metrics.average_students_per_course;

                // Render courses
                const coursesContainer = document.getElementById('courses-container');
                coursesContainer.innerHTML = courses.length > 0
                    ? courses.map(course => `
                        <div class="course-item">
                            <div class="course-details">
                                <h5 class="mb-1">${course.title}</h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge ${getStatusBadge(course.status)} badge-status me-2">
                                        ${course.status}
                                    </span>
                                    <small class="text-muted">
                                        ${course.active_enrollments} Students
                                    </small>
                                </div>
                            </div>
                        </div>
                    `).join('')
                    : '<p class="text-center text-muted p-3">No courses available</p>';

                // Render recent enrollments
                const enrollmentsContainer = document.getElementById('recent-enrollments');
                enrollmentsContainer.innerHTML = recent_enrollments.length > 0
                    ? recent_enrollments.map(enrollment => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${enrollment.user.name}</strong>
                                <small class="d-block text-muted">
                                    ${enrollment.course.title}
                                </small>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                ${new Date(enrollment.created_at).toLocaleDateString()}
                            </span>
                        </li>
                    `).join('')
                    : '<li class="list-group-item text-center text-muted">No recent enrollments</li>';

            } catch (error) {
                handleError(error);
            }
        }

        function getStatusBadge(status) {
            const badgeClasses = {
                'approved': 'bg-success',
                'pending': 'bg-warning text-dark',
                'rejected': 'bg-danger'
            };
            return badgeClasses[status.toLowerCase()] || 'bg-secondary';
        }

        function handleError(error) {
            console.error('Dashboard fetch error:', error);
            alert('Failed to load dashboard. Please try again.');
        }

        fetchDashboardData();
    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/error';
    }
});

</script>
@endsection
