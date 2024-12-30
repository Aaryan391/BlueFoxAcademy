@extends('adminlayout.adminapp')

@section('title', 'Course Management')

@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-lg {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-text {
    font-weight: bold;
    color: #666;
}

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.nav-pills .nav-link.active {
    background-color: #4a6cf7;
}

.nav-pills .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-pills .nav-link:hover {
    color: #4a6cf7;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.status-badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 500;
    border-radius: 0.25rem;
}

.detail-section {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.detail-section:last-child {
    border-bottom: none;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <!-- Tabs Navigation -->
            <ul class="nav nav-pills nav-fill gap-2 p-1 bg-white rounded-pill shadow-sm mb-4" id="managementTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill" id="admissions-tab" data-bs-toggle="tab" data-bs-target="#admissions" type="button" role="tab">
                        <i class="fas fa-user-plus me-2"></i>Admissions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill" id="enrollments-tab" data-bs-toggle="tab" data-bs-target="#enrollments" type="button" role="tab">
                        <i class="fas fa-graduation-cap me-2"></i>Enrollments
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="managementTabsContent">
                <!-- Admissions Tab -->
                <div class="tab-pane fade show active" id="admissions" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Course Admissions</h5>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="/admindashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Admissions</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="admissions-alert" class="alert-container mb-4"></div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="admissionsTableBody"></tbody>
                                </table>
                            </div>
                            <div id="admissionsPagination" class="d-flex justify-content-center mt-4"></div>
                        </div>
                    </div>
                </div>

                <!-- Enrollments Tab -->
                <div class="tab-pane fade" id="enrollments" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Course Enrollments</h5>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="/admindashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Enrollments</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="enrollments-alert" class="alert-container mb-4"></div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Status</th>
                                            <th>Enrollment Date</th>
                                            <th>Completion Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="enrollmentsTableBody"></tbody>
                                </table>
                            </div>
                            <div id="enrollmentsPagination" class="d-flex justify-content-center mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Admission Details Modal -->
<div class="modal fade" id="admissionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Admission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="admissionDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

    function showAlert(containerId, message, type = 'info') {
        const container = document.getElementById(containerId);
        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    function getStatusBadgeClass(status) {
        const classes = {
            pending: 'bg-warning',
            approved: 'bg-success',
            rejected: 'bg-danger',
            active: 'bg-primary',
            completed: 'bg-info'
        };
        return `status-badge ${classes[status] || 'bg-secondary'}`;
    }

    async function fetchAdmissions(page = 1) {
        try {
            const response = await axios.get(`/api/admin/admissions?page=${page}`);
            const admissions = response.data.admissions.data;

            document.getElementById('admissionsTableBody').innerHTML = admissions.map(admission => `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-light rounded-circle me-2">
                                <span class="avatar-text">${admission.full_name.charAt(0)}</span>
                            </div>
                            <div>
                                <div class="fw-bold">${admission.full_name}</div>
                                <small class="text-muted">${admission.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>${admission.course ? admission.course.title : 'N/A'}</td>
                    <td>
                        <select class="form-select form-select-sm admission-status w-auto" 
                                data-admission-id="${admission.id}" 
                                ${admission.status === 'approved' ? 'disabled' : ''}>
                            <option value="pending" ${admission.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="approved" ${admission.status === 'approved' ? 'selected' : ''}>Approved</option>
                            <option value="rejected" ${admission.status === 'rejected' ? 'selected' : ''}>Rejected</option>
                        </select>
                    </td>
                    <td>${new Date(admission.created_at).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    })}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm" onclick="viewAdmissionDetails(${admission.id})">
                            <i class="fas fa-eye me-1"></i>View Details
                        </button>
                    </td>
                </tr>
            `).join('');

            setupPagination('admissionsPagination', response.data.admissions);
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.message || 'An unknown error occurred';
            showAlert('admissions-alert', `Failed to fetch admissions: ${errorMessage}`, 'danger');
        }
    }
    async function fetchEnrollments(page = 1) {
        try {
            const response = await axios.get(`/api/admin/enrollments?page=${page}`);
            const enrollments = response.data.enrollments.data;

            document.getElementById('enrollmentsTableBody').innerHTML = enrollments.map(enrollment => `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-light rounded-circle me-2">
                                <span class="avatar-text">${enrollment.user.name.charAt(0)}</span>
                            </div>
                            <div>
                                <div class="fw-bold">${enrollment.user.name}</div>
                                <small class="text-muted">${enrollment.user.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium">${enrollment.course.title}</div>
                        <small class="text-muted">ID: ${enrollment.course.code || enrollment.course.id}</small>
                    </td>
                    <td>
                        <span class="badge ${getStatusBadgeClass(enrollment.status)}">
                ${enrollment.status.charAt(0).toUpperCase() + enrollment.status.slice(1)}
            </span>
                    </td>
                    <td>
                        <div class="text-nowrap">
                            ${new Date(enrollment.enrollment_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            })}
                        </div>
                    </td>
                    <td>
                        ${enrollment.completion_date ? 
                            new Date(enrollment.completion_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) : 
                            '<span class="text-muted">--</span>'
                        }
                    </td>
                </tr>
            `).join('');

            setupPagination('enrollmentsPagination', response.data.enrollments);
        } catch (error) {
            showAlert('enrollments-alert', 'Failed to fetch enrollments: ' + error.message, 'danger');
        }
    }

    function setupPagination(containerId, paginationData) {
        const container = document.getElementById(containerId);
        const totalPages = Math.ceil(paginationData.total / paginationData.per_page);
        const currentPage = paginationData.current_page;

        let paginationHtml = '<ul class="pagination">';

        // Previous page button
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${containerId === 'admissionsPagination' ? 'fetchAdmissions' : 'fetchEnrollments'}(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 2 && i <= currentPage + 2)
            ) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="event.preventDefault(); ${containerId === 'admissionsPagination' ? 'fetchAdmissions' : 'fetchEnrollments'}(${i})">${i}</a>
                    </li>
                `;
            } else if (
                i === currentPage - 3 || 
                i === currentPage + 3
            ) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next page button
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${containerId === 'admissionsPagination' ? 'fetchAdmissions' : 'fetchEnrollments'}(${currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        paginationHtml += '</ul>';
        container.innerHTML = paginationHtml;
    }

    window.viewAdmissionDetails = async function(admissionId) {
        try {
            const response = await axios.get(`/api/admin/admissions/${admissionId}`);
            const admission = response.data.admission;
            const schedule = JSON.parse(admission.schedule || '{}');

            document.getElementById('admissionDetailsContent').innerHTML = `
                <div class="detail-section text-center bg-light">
                    <div class="avatar-lg bg-white rounded-circle mx-auto mb-3">
                        <span class="avatar-text display-6">${admission.full_name.charAt(0)}</span>
                    </div>
                    <h4 class="mb-1">${admission.full_name}</h4>
                    <span class="badge ${getStatusBadgeClass(admission.status)}">${admission.status}</span>
                </div>
                
                <div class="detail-section">
                    <h6 class="text-uppercase text-muted mb-3">Contact Information</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-muted small mb-1">Email Address</label>
                            <div class="fw-medium">${admission.email}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small mb-1">Mobile Number</label>
                            <div class="fw-medium">${admission.mobile_number}</div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h6 class="text-uppercase text-muted mb-3">Course Details</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-muted small mb-1">Course Title</label>
                            <div class="fw-medium">${admission.course.title}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Type</label>
                            <div class="fw-medium text-capitalize">${admission.type}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Application Date</label>
                            <div class="fw-medium">${new Date(admission.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</div>
                        </div>
                    </div>
                </div>
                
                ${schedule && Object.keys(schedule).length > 0 ? `
                    <div class="detail-section">
                        <h6 class="text-uppercase text-muted mb-3">Preferred Schedule</h6>
                        <div class="schedule-grid">
                            ${Object.entries(schedule).map(([day, time]) => `
                                <div>
                                    <label class="text-muted small mb-1">${day}</label>
                                    <div class="fw-medium">${time}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;

            new bootstrap.Modal(document.getElementById('admissionDetailsModal')).show();
        } catch (error) {
            showAlert('admissions-alert', 'Failed to fetch admission details', 'danger');
        }
    }

    // Event Listeners for Status Updates
    document.addEventListener('change', async function(e) {
        if (e.target.classList.contains('admission-status')) {
            const admissionId = e.target.dataset.admissionId;
            const newStatus = e.target.value;
            const originalStatus = e.target.querySelector('option:checked').value;

            try {
                await axios.post(`/api/admin/admissions/${admissionId}/status`, {
                    
                    status: newStatus
                });

                showAlert('admissions-alert', 'Status updated successfully', 'success');
                if (newStatus === 'approved') {
                    e.target.disabled = true;
                }
                fetchAdmissions(); // Refresh the table
            } catch (error) {
                console.error("Error response data:", error.response?.data);
                console.error("Error status:", error.response?.status);
                console.error("Error headers:", error.response?.headers);

                showAlert('admissions-alert', 'Failed to update status', 'danger');

                e.target.value = originalStatus;
            }
        }
    });

    // Tab change handler
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            if (e.target.id === 'admissions-tab') {
                fetchAdmissions();
            } else if (e.target.id === 'enrollments-tab') {
                fetchEnrollments();
            }
        });
    });

    // Initial load
    fetchAdmissions();
} catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection