@extends('adminlayout.adminapp')
@section('title', 'Manage Courses')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    .filter-buttons button.active {
        background-color: var(--primary-color);
        color: white;
    }
    .card-courses-list {
        border: 1px solid var(--accent-color);
        border-radius: 10px;
        margin-bottom: 1.5rem;
        background: var(--light-background);
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .card-courses-media img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .card-courses-full-dec {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1rem;
    }

    .card-courses-title h4 {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .card-courses-list-bx ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
    }

    .card-courses-list-bx ul li {
        flex: 1 1 45%;
        text-align: center;
        border-right: none;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.5rem 0.8rem;
    }

    .card-courses-footer {
        margin-top: auto;
    }
</style>
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header text-white" style="background-color: var(--primary-color);">
                <h4>Courses List</h4>
            </div>
            <div class="card-body">
                <div id="alert-container" class="mb-4">
                    <!-- Dynamic alert messages will be inserted here -->
                </div>
                <div id="course-status-filter-buttons" class="filter-buttons mb-3 d-flex gap-2">
                    <!-- Filter buttons will be dynamically updated -->
                </div>
                <div id="courses-container" class="row">
                    <!-- Courses will be loaded here dynamically -->
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
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (userResponse.data.role !== 'admin') {
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

        const coursesContainer = document.getElementById('courses-container');
        const alertContainer = document.getElementById('alert-container');
        let currentStatusFilter = null;

        // Create a single, persistent filter container
        const filterContainer = document.getElementById('course-status-filter-buttons');

        // Update the status filter buttons
        function updateStatusFilterButtons() {
            filterContainer.innerHTML = `
                <button class="btn btn-outline-primary ${currentStatusFilter === null ? 'active' : ''}" 
                        onclick="setStatusFilter(null)">All Courses</button>
                <button class="btn btn-outline-success ${currentStatusFilter === 'approved' ? 'active' : ''}" 
                        onclick="setStatusFilter('approved')">Approved</button>
                <button class="btn btn-outline-warning ${currentStatusFilter === 'pending' ? 'active' : ''}" 
                        onclick="setStatusFilter('pending')">Pending</button>
                <button class="btn btn-outline-danger ${currentStatusFilter === 'rejected' ? 'active' : ''}" 
                        onclick="setStatusFilter('rejected')">Rejected</button>
            `;
        }

        // Fetch Courses
        const fetchCourses = async () => {
            try {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    window.location.href = '/login';
                    return;
                }

                updateStatusFilterButtons();
                const params = currentStatusFilter ? `?status=${currentStatusFilter}` : '';
                const response = await axios.get(`/api/admin/courses${params}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.data.success) {
                    showAlert('Error fetching courses', 'danger');
                    return;
                }

                const courses = response.data.data;
                coursesContainer.innerHTML = "";
                if (courses.length === 0) {
                    coursesContainer.innerHTML = `
                        <div class="alert alert-info text-center">
                            No courses found for the selected status.
                        </div>
                    `;
                    return;
                }

                courses.forEach(course => {
    const courseCard = `
        <div class="col-md-6 col-lg-4">
            <div class="card-courses-list">
                <div class="card-courses-media">
                    <img src="${course.course_image ? `/storage/${course.course_image}` : '/storage/courses/default.jpg'}" alt="${course.title}" />
                </div>
                <div class="card-courses-full-dec">
                    <div class="card-courses-title">
                        <h4>${course.title}</h4>
                    </div>
                    <div class="card-courses-list-bx">
                        <ul class="d-flex flex-wrap">
                            <li>
                            <div class="d-flex align-items-center justify-content-center">
    <img id="profileAvatar" 
         src="${course.user.profile_picture ? `/storage/${course.user.profile_picture}` : '/default-avatar.png'}" 
         alt="Profile Picture" 
         class="img-fluid rounded-circle mb-2 shadow border border-3 border-light" 
         style="width: 60px; height: 60px; object-fit: cover;" 
         onerror="this.src='/default-avatar.png'">
</div>


                                <h6>${course.user.name}</h6>
                                <small>Teacher</small>
                            </li>
                            <li>
                                <h6>Category</h6>
                                <p>${course.category.name}</p>
                            </li>
                            <li>
                                <span class="badge ${getStatusBadgeClass(course.status)}">${course.status}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-courses-footer d-flex justify-content-end gap-2">
                        <button onclick="updateStatus(${course.id}, 'approved')" 
                                class="btn btn-success btn-sm" 
                                ${course.status === 'approved' ? 'disabled' : ''}>
                            Approve
                        </button>
                        <button onclick="updateStatus(${course.id}, 'rejected')" 
                                class="btn btn-danger btn-sm" 
                                ${course.status === 'rejected' ? 'disabled' : ''}>
                            Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    coursesContainer.innerHTML += courseCard;
});

            } catch (error) {
                handleError(error);
            }
        };

        window.setStatusFilter = (status) => {
            currentStatusFilter = status;
            fetchCourses();
        };

    // Helper function to get the class for status badge
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'approved':
                return 'bg-success text-white';
            case 'rejected':
                return 'bg-danger text-white';
            case 'pending':
                return 'bg-warning text-dark';
            default:
                return 'bg-secondary text-white';
        }
    }

    // Update course status
    window.updateStatus = async (id, status) => {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await axios.put(
                `/api/admin/courses/${id}/status`,
                { status },
                { headers: { 'Authorization': `Bearer ${token}` } }
            );

            if (response.data.message) {
                showAlert(response.data.message, 'success');
            }

            // Refresh the courses list
            fetchCourses();
        } catch (error) {
            handleError(error);
        }
    };

    // Function to handle errors
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
            } else {
                errorMessage = error.response.data.message || 'Server error';
            }
        } else if (error.request) {
            errorMessage = 'No response from server. Please check your connection.';
        }

        showAlert(errorMessage, 'danger');
    }

    // Function to display alerts
    function showAlert(message, type = 'info') {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }
    // Initial fetch of courses
    fetchCourses();
} catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection