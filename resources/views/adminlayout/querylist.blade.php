@extends('adminlayout.adminapp')
@section('title', 'Course Details')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    body {
        background-color: var(--light-background);
        color: var(--text-color);
    }

    .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
        transition: transform 0.2s;
        background: white;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-filter {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background-color: var(--hover-color);
        color: white;
    }

    .btn-filter.active {
        background-color: var(--accent-color);
    }

    .status-badge {
        padding: 0.5em 1em;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: var(--hover-color);
        color: white;
    }

    .status-contacted {
        background-color: var(--accent-color);
        color: white;
    }

    .status-closed {
        background-color: var(--secondary-color);
        color: white;
    }

    .form-select {
        border-color: var(--accent-color);
    }

    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(7, 15, 43, 0.25);
    }
</style>
<div class="container py-5">
    <div class="row mb-4" id="inquiriesContent"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
        loadingOverlay.innerHTML = '<div class="spinner-border" style="color: var(--primary-color)" role="status"></div>';
        document.body.appendChild(loadingOverlay);

        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Add role verification
            const userResponse = await axios.get('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (userResponse.data.role !== 'admin') {
                window.location.href = '/error';
                return;
            }

            // Set up axios interceptors
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
                                window.location.href = '/error';
                                break;
                        }
                    }
                    return Promise.reject(error);
                }
            );

            loadingOverlay.remove();
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            const inquiriesContent = document.getElementById('inquiriesContent');

            // Initialize content structure
            inquiriesContent.innerHTML = `
            <div class="col-12 mb-4">
                <h2 class="mb-3">Course Inquiries</h2>
                <div class="btn-group" role="group" aria-label="Filter inquiries">
                    <button type="button" class="btn btn-filter active" data-filter="all">All</button>
                    <button type="button" class="btn btn-filter" data-filter="pending">Pending</button>
                    <button type="button" class="btn btn-filter" data-filter="contacted">Contacted</button>
                    <button type="button" class="btn btn-filter" data-filter="closed">Closed</button>
                </div>
            </div>
            <div class="col-12">
                <div class="row g-4" id="inquiriesContainer"></div>
            </div>
        `;

            const inquiriesContainer = document.getElementById('inquiriesContainer');

            async function fetchInquiries(status = 'all') {
                try {
                    const url = status === 'all' ? '/api/course-inquiries' : `/api/course-inquiries?status=${status}`;
                    const response = await axios.get(url);
                    renderInquiries(response.data.inquiries);
                } catch (error) {
                    console.error('Error fetching inquiries:', error);
        const errorMessage = error.response?.data?.message || 'Failed to load inquiries. Please try again later.';
        inquiriesContainer.innerHTML = `
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                ${errorMessage}
            </div>
        </div>
        `;
                }
            }

            function renderInquiries(inquiries) {
                if (inquiries.length === 0) {
                    inquiriesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-secondary text-center" role="alert">
                            No inquiries found.
                        </div>
                    </div>
                `;
                    return;
                }

                inquiriesContainer.innerHTML = inquiries.map(inquiry => `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">${inquiry.full_name}</h5>
                                <span class="status-badge status-${inquiry.status}">${inquiry.status}</span>
                            </div>
                            <p class="card-text">
                                <strong>Course:</strong> ${inquiry.course.title}<br>
                                <strong>Email:</strong> ${inquiry.email}<br>
                                <strong>Date:</strong> ${new Date(inquiry.created_at).toLocaleDateString()}
                            </p>
                            <p class="card-text">${inquiry.message}</p>
                            <div class="mt-3">
                                <select class="form-select" 
                                        onchange="updateInquiryStatus(${inquiry.id}, this.value)"
                                        ${inquiry.status === 'closed' ? 'disabled' : ''}>
                                    <option value="pending" ${inquiry.status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="contacted" ${inquiry.status === 'contacted' ? 'selected' : ''}>Contacted</option>
                                    <option value="closed" ${inquiry.status === 'closed' ? 'selected' : ''}>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            window.updateInquiryStatus = async function(inquiryId, status) {
                try {
                    await axios.patch(`/api/course-inquiries/${inquiryId}/status`, {
                        status
                    });
                    const activeFilter = document.querySelector('.btn-filter.active').dataset.filter;
                    await fetchInquiries(activeFilter);
                } catch (error) {
                    console.error('Error updating status:', error);
                    alert('Failed to update inquiry status: ' + error.response?.data?.message || 'Unknown error');
                }
            };

            // Set up filter buttons
            document.querySelectorAll('.btn-filter').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    fetchInquiries(this.dataset.filter);
                });
            });

            // Initial load
            fetchInquiries('all');

        } catch (error) {
            console.error('Initialization error:', error);
    const errorMessage = error.response?.data?.message || 'An unexpected error occurred. Please try again.';
    alert(`Error: ${errorMessage}`);
    window.location.href = error.response?.status === 403 ? '/error' : '/login';
        }
    });
</script>
@endsection