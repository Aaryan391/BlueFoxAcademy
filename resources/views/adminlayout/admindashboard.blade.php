@extends('adminlayout.adminapp')
@section('title', 'Admin Dashboard')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    .dashboard-container {
        padding: 2rem;
        background: var(--light-background);
    }

    .stats-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        border: none;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card .card-body {
        padding: 1.5rem;
    }

    .stats-card .card-title {
        color: var(--secondary-color);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .stats-card .card-text {
        color: var(--primary-color);
        font-size: 1.5rem;
        font-weight: 700;
    }

    .content-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        border: none;
    }

    .content-card .card-header {
        background: var(--primary-color);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1rem 1.5rem;
        border: none;
    }

    .content-card .card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .table {
        margin: 0;
    }

    .table thead th {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 1rem;
    }

    .table tbody td {
        padding: 1rem;
        color: var(--text-color);
        border-color: #eee;
    }

    .table tbody tr:hover {
        background-color: rgba(83, 92, 145, 0.1);
    }

    .btn-primary {
        background: var(--accent-color);
        border: none;
        padding: 0.5rem 1.5rem;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--hover-color);
    }

    input.form-control {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        border-radius: 8px;
    }

    input.form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(83, 92, 145, 0.25);
    }

    #courseCategoriesChart {
        padding: 1rem;
    }

    #reportResult {
        margin-top: 1.5rem;
        padding: 1rem;
        background: rgba(83, 92, 145, 0.1);
        border-radius: 8px;
    }
</style>

<div class="dashboard-container">
    <!-- Dashboard Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text" id="totalUsers">--</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text" id="totalCourses">--</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <div class="card-body">
                    <h5 class="card-title">Total Enrollments</h5>
                    <p class="card-text" id="totalEnrollments">--</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments Table -->
    <div class="content-card">
        <div class="card-header">
            <h5>Recent Enrollments</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="enrollmentsTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Course</th>
                            <th>Date Enrolled</th>
                        </tr>
                    </thead>
                    <tbody id="recentEnrollmentsBody">
                        <!-- Data will be dynamically populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Course Categories Chart Section -->
    <div class="content-card">
        <div class="card-header">
            <h5>Course Categories Distribution</h5>
        </div>
        <div class="card-body">
            <canvas id="courseCategoriesChart" height="300"></canvas>
        </div>
    </div>

    <!-- Enrollment Report Section -->
    <div class="content-card">
        <div class="card-header">
            <h5>Generate Enrollment Report</h5>
        </div>
        <div class="card-body">
            <form id="reportForm">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <input type="date" name="start_date" id="startDate" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-md-5 mb-3">
                        <input type="date" name="end_date" id="endDate" class="form-control" placeholder="End Date">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Generate</button>
                    </div>
                </div>
            </form>
            <div id="reportResult">
                <!-- Report results dynamically populated -->
            </div>
        </div>
    </div>
</div>

<!-- Rest of the JavaScript code remains unchanged -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        // Dashboard functionality
        async function fetchStatistics() {
            try {
                const response = await axios.get('/api/dashboard/statistics');
                const data = response.data;
                
                updateDashboardStatistics(data);
                renderCategoriesChart(data.course_categories);
                populateRecentEnrollments(data.recent_enrollments);
            } catch (error) {
                handleDashboardError(error);
            }
        }

        function updateDashboardStatistics(data) {
            document.getElementById('totalUsers').innerText = data.total_users;
            document.getElementById('totalCourses').innerText = data.total_courses;
            document.getElementById('totalEnrollments').innerText = data.total_enrollments;
        }

        function populateRecentEnrollments(enrollments) {
            const tbody = document.getElementById('recentEnrollmentsBody');
            tbody.innerHTML = '';
            enrollments.forEach(enrollment => {
                const row = `
                    <tr>
                        <td>${enrollment.user.name}</td>
                        <td>${enrollment.course.title}</td>
                        <td>${enrollment.enrollment_date}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function renderCategoriesChart(categories) {
            const ctx = document.getElementById('courseCategoriesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categories.map(c => c.name),
                    datasets: [{
                        label: 'Courses Count',
                        data: categories.map(c => c.courses_count),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        function handleDashboardError(error) {
            console.error('Dashboard error:', error);
            if (error.response?.status === 403) {
                window.location.href = '/error';
            }
        }

        // Report form handling
        document.getElementById('reportForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            
            try {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
                
                const response = await axios.post('/api/reports/enrollments', {
                    start_date: document.getElementById('startDate').value,
                    end_date: document.getElementById('endDate').value
                });
                
                const report = response.data;
                document.getElementById('reportResult').innerHTML = `
                    <p><strong>Total Enrollments:</strong> ${report.total_enrollments}</p>
                `;
            } catch (error) {
                document.getElementById('reportResult').innerHTML = `
                    <p class="text-danger">Error generating report. Please try again.</p>
                `;
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Generate';
            }
        });

        // Initial data fetch
        await fetchStatistics();

    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection
