@extends('adminlayout.adminapp')
@section('title', 'Manage User')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<div class="row mb-3">
    <div class="col-md-4">
        <select id="roleFilter" class="form-select">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="teacher">Teacher</option>
            <option value="user">User</option>
        </select>
    </div>
</div>

<table class="table table-bordered table-hover" id="usersTable">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="usersTableBody">
        <!-- Users will be dynamically populated here -->
    </tbody>
</table>

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
        const usersTableBody = document.getElementById('usersTableBody');
        const roleFilter = document.getElementById('roleFilter');

        function fetchUsers(role = '') {
            const params = role ? { role } : {};
            axios.get('/api/adminqueryuser', {
                headers: { 'Authorization': `Bearer ${token}` },
                params: params
            })
            .then(response => {
                usersTableBody.innerHTML = '';
                response.data.data.forEach(user => {
                    const row = `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>
                                <a href="/admin/user/${user.id}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-danger btn-sm delete-user" data-id="${user.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    usersTableBody.insertAdjacentHTML('beforeend', row);
                });
                attachEventListeners();
            })
            .catch(error => {
                console.error('Error fetching users:', error);
            });
        }

        function attachEventListeners() {
            // Delete User
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    if(confirm('Are you sure you want to delete this user?')) {
                        axios.delete(`/api/admin/user/${userId}`)
                            .then(() => {
                                fetchUsers(roleFilter.value);
                            });
                    }
                });
            });
        }

        // Initial load and filter
        fetchUsers();
        roleFilter.addEventListener('change', () => fetchUsers(roleFilter.value));
    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection