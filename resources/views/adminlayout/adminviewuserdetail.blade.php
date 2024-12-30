@extends('adminlayout.adminapp')
@section('title', 'User Profile')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
:root {
    --primary-color: #070F2B;
    --secondary-color: #1B1A55;
    --accent-color: #535C91;
    --light-background: #F5F5F5;
    --text-color: #333333;
    --hover-color: #9290C3;
}

.user-profile-container {
    background-color: var(--light-background);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 2rem;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--accent-color);
}

.status-badge {
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.course-filter .dropdown-item.active {
    background-color: var(--primary-color);
    color: white;
}

.course-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.course-card.approved {
    border-left-color: #28a745;
}

.course-card.pending {
    border-left-color: #ffc107;
}

.course-card.rejected {
    border-left-color: #dc3545;
}
</style>

<div class="container-fluid px-4 py-4 user-profile-container">
    <div class="profile-header d-flex flex-column flex-md-row align-items-center gap-4">
        <div>
        <img id="profileAvatar" src="/default-avatar.png" alt="Profile Picture" 
     class="profile-avatar" onerror="this.src='/default-avatar.png'">

        </div>
        <div class="flex-grow-1">
            <h2 id="profileName" class="mb-2"></h2>
            <p id="profileEmail" class="mb-3"></p>
            <div class="d-flex align-items-center gap-3">
                <span id="profileRole" class="badge status-badge"></span>
                <span id="profileCompany" class="text-white"></span>
            </div>
        </div>
        <div class="text-white text-end">
            <div><strong>Member Since:</strong> <span id="profileMemberSince"></span></div>
            <div><strong>Last Active:</strong> <span id="profileLastActive"></span></div>
            <div><strong>instagram:</strong> <span id="instagram"></span></div>
            <div><strong>twitter:</strong> <span id="twitter"></span></div>
            <div><strong>facebook:</strong> <span id="facebook"></span></div>
            <div><strong>linkedin:</strong> <span id="linkedin"></span></div>
        </div>
    </div>

    <div class="row my-4 g-3">
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Courses</h5>
                    <h3 id="totalCourses" class="text-primary"></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Active Courses</h5>
                    <h3 id="activeCourses" class="text-success"></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Students</h5>
                    <h3 id="totalStudents" class="text-info"></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Avg Course Price</h5>
                    <h3 id="avgCoursePrice" class="text-warning"></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Course Listings</h4>
            <div class="dropdown course-filter">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="courseStatusDropdown" data-bs-toggle="dropdown">
                    All Courses
                </button>
                <ul class="dropdown-menu" id="courseStatusMenu">
                    <!-- Dynamically populated -->
                </ul>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="coursesContainer" class="list-group list-group-flush">
                <!-- Courses dynamically inserted here -->
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


    const userId = window.location.pathname.split('/').pop();

    function fetchUserProfile() {
        axios.get(`/api/admin/user/${userId}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(response => {
            const { user, stats } = response.data.data;
            const profileAvatar = document.getElementById('profileAvatar');
        profileAvatar.src = user.profile_picture;
            
            document.getElementById('profileName').textContent = user.name;
            document.getElementById('profileEmail').textContent = user.email;
            document.getElementById('profileRole').textContent = user.role;
            document.getElementById('profileCompany').textContent = user.company_name || 'N/A';
            document.getElementById('profileMemberSince').textContent = stats.member_since;
            document.getElementById('instagram').textContent = user.instagram;
            document.getElementById('twitter').textContent = user.twitter;
            document.getElementById('facebook').textContent = user.facebook;
            document.getElementById('linkedin').textContent = user.linkedin;

            document.getElementById('profileLastActive').textContent = stats.last_active;
            
            document.getElementById('totalCourses').textContent = stats.total_courses;
            document.getElementById('activeCourses').textContent = stats.active_courses;
            document.getElementById('totalStudents').textContent = stats.total_students;
            document.getElementById('avgCoursePrice').textContent = `$${stats.avg_course_price}`;
        })
        .catch(error => {
            console.error('Profile fetch error:', error);
            alert('Failed to fetch profile');
        });
    }

function fetchUserCourses(status = 'all') {
    axios.get(`/api/admin/user/${userId}/courses/${status}`, {
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(response => {
        const { courses, statusCounts, statusFilter } = response.data.data;

        // Dynamic Status Dropdown Rendering
        renderStatusDropdown(statusCounts, statusFilter);

        // Course Listing Rendering
        renderCourseListing(courses, statusFilter);

        // Interactive Event Listeners
        attachCourseFilterListeners();
    })
    .catch(error => {
        handleCoursesFetchError(error);
    });
}

function renderStatusDropdown(statusCounts, activeStatus) {
    const statusMenu = document.getElementById('courseStatusMenu');
    const dropdownButton = document.getElementById('courseStatusDropdown');

    // Generate Dropdown Items
    statusMenu.innerHTML = Object.entries(statusCounts)
        .map(([status, count]) => `
            <li>
                <a class="dropdown-item ${status === activeStatus ? 'active' : ''}" 
                   href="#" data-status="${status}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${status.charAt(0).toUpperCase() + status.slice(1)} Courses</span>
                        <span class="badge bg-secondary rounded-pill">${count}</span>
                    </div>
                </a>
            </li>
        `).join('');

    // Update Dropdown Button Text
    dropdownButton.innerHTML = `
        <i class="fas fa-filter me-2"></i>
        ${activeStatus.charAt(0).toUpperCase() + activeStatus.slice(1)} Courses 
        <span class="badge bg-light text-dark ms-2">${statusCounts[activeStatus]}</span>
    `;
}

function renderCourseListing(courses, statusFilter) {
    const coursesContainer = document.getElementById('coursesContainer');

    if (courses.length === 0) {
        coursesContainer.innerHTML = `
            <div class="text-center p-5">
                <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No ${statusFilter} Courses Found</h4>
                <p class="text-secondary">You haven't created any ${statusFilter} courses yet.</p>
            </div>
        `;
        return;
    }

    coursesContainer.innerHTML = courses.map(course => `
        <div class="course-card border-start border-4 
            ${course.status === 'approved' ? 'border-success' : 
              course.status === 'pending' ? 'border-warning' : 'border-danger'}
            p-3 mb-2 shadow-sm">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-2">${course.title}</h5>
                    <div class="mb-2">
                        <span class="badge bg-secondary me-2">${course.category}</span>
                        <span class="badge bg-info">${course.level} Level</span>
                    </div>
                    <div class="course-details text-muted small">
                        <span><i class="far fa-clock me-1"></i>${course.duration}</span>
                        <span class="mx-2">•</span>
                        <span><i class="fas fa-users me-1"></i>${course.num_students} Students</span>
                        <span class="mx-2">•</span>
                        <span><i class="fas fa-tag me-1"></i>${course.price}</span>
                    </div>
                    <div class="text-muted mt-2">
                        <i class="far fa-calendar-alt me-1"></i>Created ${course.created_at}
                    </div>
                </div>
                <div>
                    <span class="badge 
                        ${course.status === 'approved' ? 'bg-success' : 
                          course.status === 'pending' ? 'bg-warning' : 'bg-danger'}">
                        ${course.status.charAt(0).toUpperCase() + course.status.slice(1)}
                    </span>
                </div>
            </div>
        </div>
    `).join('');
}

function attachCourseFilterListeners() {
    document.querySelectorAll('#courseStatusMenu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const status = this.getAttribute('data-status');
            
            // Remove active class from all items
            document.querySelectorAll('#courseStatusMenu .dropdown-item')
                .forEach(el => el.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Fetch courses with new status
            fetchUserCourses(status);
        });
    });
}

function handleCoursesFetchError(error) {
    const errorContainer = document.getElementById('coursesContainer');
    errorContainer.innerHTML = `
        <div class="alert alert-danger p-4 text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h4>Unable to Load Courses</h4>
            <p>${error.response?.data?.message || 'An unexpected error occurred'}</p>
            <button onclick="fetchUserCourses()" class="btn btn-outline-danger mt-3">
                <i class="fas fa-sync me-2"></i>Retry
            </button>
        </div>
    `;
    console.error('Courses Fetch Error:', error);
}
    fetchUserProfile();
    fetchUserCourses();
} catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection