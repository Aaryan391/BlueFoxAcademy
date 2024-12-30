@extends('adminlayout.adminapp')
@section('title', 'Teacher Requests Management')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
.card {
            border-color: var(--secondary-color);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .btn-approve {
            background-color: var(--accent-color);
            border-color: var(--secondary-color);
        }

        .btn-approve:hover {
            background-color: var(--secondary-color);
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .header-container {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
    </style>
    <div class="header-container text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Teacher Requests Management</h1>
        </div>
    </div>

    <div class="container">
        <div id="teacherRequestsContainer" class="row g-4">
            <!-- Teacher requests will be dynamically loaded here -->
        </div>
    </div>
    <!-- Axios for HTTP requests -->
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
        const requestsContainer = document.getElementById('teacherRequestsContainer');

        async function fetchTeacherRequests() {
            try {
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    window.location.href = '/login';
                    return;
                }

                const response = await axios.get('/api/teacher-requests', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                renderTeacherRequests(response.data.requests);
            } catch (error) {
                console.error('Error fetching teacher requests:', error);
                requestsContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            Failed to load teacher requests. Please try again later.
                        </div>
                    </div>
                `;
            }
        }

        function renderTeacherRequests(requests) {
            if (requests.length === 0) {
                requestsContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-secondary text-center" role="alert">
                            No pending teacher requests.
                        </div>
                    </div>
                `;
                return;
            }

            requestsContainer.innerHTML = requests.map(request => `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">${request.name}</h5>
                            <p class="card-text text-muted">
                                <strong>Email:</strong> ${request.email}<br>
                                <strong>Occupation:</strong> ${request.occupation || 'Not specified'}<br>
                                <strong>Company:</strong> ${request.company_name || 'Not specified'}
                            </p>
                            <p class="card-text fst-italic">
                                <strong>Expertise:</strong> ${request.teacher_expertise || 'No expertise provided'}
                            </p>
                            <div class="d-flex justify-content-between mt-3">
                                <button onclick="approveRequest(${request.id})" class="btn btn-approve text-white">
                                    Approve
                                </button>
                                <button onclick="deleteRequest(${request.id})" class="btn btn-reject">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        window.approveRequest = async function(requestId) {
            try {
                const token = localStorage.getItem('auth_token');
                await axios.post(`/api/teacher-requests/${requestId}/approve`, {}, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                await fetchTeacherRequests();
            } catch (error) {
                console.error('Error approving request:', error);
                alert('Failed to approve request: ' + error.response?.data?.message || 'Unknown error');
            }
        };

        window.deleteRequest = async function(requestId) {
            try {
                const token = localStorage.getItem('auth_token');
                await axios.post(`/api/teacher-requests/${requestId}/reject`,{}, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                await fetchTeacherRequests();
            } catch (error) {
                console.error('Error rejecting request:', error);
                alert('Failed to reject request: ' + error.response?.data?.message || 'Unknown error');
            }
        };

        fetchTeacherRequests();
    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
    </script>
@endsection