@extends('adminlayout.adminapp')
@section('title', 'Dashboard')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
    .hidden {
        display: none;
    }

    .form-container,
    .list-container {
        background: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
        margin-bottom: 1rem;
        color: var(--text-color);
        font-size: 1.5rem;
        font-weight: 600;
    }

    .message {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        text-align: center;
        background: var(--light-background);
        color: var(--text-color);
    }

    .button {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .button-primary {
        background: var(--secondary-color);
        color: white;
    }

    .button-primary:hover {
        background: var(--hover-color);
    }

    .button-secondary {
        background: var(--light-background);
        color: var(--text-color);
    }

    .button-secondary:hover {
        background: var(--accent-color);
        color: white;
    }

    .button-danger {
        background: var(--accent-color);
        color: white;
    }

    .button-danger:hover {
        background: var(--hover-color);
    }

    .button-success {
        background: var(--primary-color);
        color: white;
    }

    .button-success:hover {
        background: var(--hover-color);
    }

    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--light-background);
        padding: 1rem;
        border-radius: 0.5rem;
        transition: background-color 0.3s ease;
    }

    .category-item:hover {
        background: var(--hover-color);
        color: white;
    }

    .category-item span {
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-color);
    }
</style>

<!-- Message -->
<div id="message" class="hidden message"></div>

<!-- Category Form -->
<div class="form-container max-w-lg mx-auto">
    <h2>Manage Categories</h2>
    <form id="categoryCreateForm" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium">Category Name</label>
            <input type="text" id="name" placeholder="Enter category name" required
                class="block w-full mt-1 p-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
        </div>
        <input type="hidden" id="categoryId">

        <div class="flex space-x-4">
            <button type="submit" id="submitBtn" class="button button-primary">
                Add Category
            </button>
            <button type="button" id="cancelEditBtn" class="button button-secondary hidden">
                Cancel
            </button>
        </div>
    </form>
</div>

<!-- Category List -->
<div class="list-container max-w-lg mx-auto mt-6">
    <h2>Category List</h2>
    <div id="categoryItems" class="space-y-3"></div>
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
                window.location.href = '/login';
                return;
            }

            // Add role verification before loading dashboard
            const userResponse = await axios.get('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
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
            fetchCategories();

            const form = document.getElementById('categoryCreateForm');
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                const formData = {
                    name: document.getElementById('name').value,
                };

                const categoryId = document.getElementById('categoryId').value;
                if (categoryId) {
                    await updateCategory(categoryId, formData);
                } else {
                    await createCategory(formData);
                }
            });


            async function fetchCategories() {
                try {
                    const token = localStorage.getItem('auth_token');
                    if (!token) {
                        window.location.href = '/login';
                        return;
                    }

                    const response = await axios.get('/api/categories', {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });

                    const categoryList = document.getElementById('categoryItems');
                    categoryList.innerHTML = '';

                    response.data.forEach(category => {
                        const categoryItem = `
                <div class="category-item">
                    <span>${category.name}</span>
                    <div class="flex space-x-2">
                        <button onclick="editCategory(${category.id}, '${category.name}')" 
                            class="button button-success">
                            Edit
                        </button>
                        <button onclick="deleteCategory(${category.id})" 
                            class="button button-danger">
                            Delete
                        </button>
                    </div>
                </div>
            `;
                        categoryList.innerHTML += categoryItem;
                    });
                } catch (error) {
                    handleError(error, 'Error fetching categories');
                }
            }
            async function createCategory(data) {
                try {
                    const token = localStorage.getItem('auth_token');
                    await axios.post('/api/categories', data, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                    });

                    showMessage('Category created successfully!', 'success');
                    document.getElementById('categoryCreateForm').reset();
                    fetchCategories();
                } catch (error) {
                    handleError(error, 'Error creating category');
                }
            }

            async function updateCategory(id, data) {
                try {
                    const token = localStorage.getItem('auth_token');
                    await axios.put(`/api/categories/${id}`, data, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                    });

                    showMessage('Category updated successfully!', 'success');
                    document.getElementById('categoryCreateForm').reset();
                    document.getElementById('categoryId').value = '';
                    document.getElementById('submitBtn').textContent = 'Add Category';
                    document.getElementById('cancelEditBtn').classList.add('hidden');
                    fetchCategories();
                } catch (error) {
                    handleError(error, 'Error updating category');
                }
            }

             window.deleteCategory= async function deleteCategory(id) {
                if (!confirm('Are you sure you want to delete this category?')) return;

                try {
                    const token = localStorage.getItem('auth_token');
                    await axios.delete(`/api/categories/${id}`, {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });

                    showMessage('Category deleted successfully!', 'success');
                    fetchCategories();
                } catch (error) {
                    handleError(error, 'Error deleting category');
                }
            }

            window.editCategory = function(id, name) {
                document.getElementById('name').value = name;
                document.getElementById('categoryId').value = id;
                document.getElementById('submitBtn').textContent = 'Update Category';
                document.getElementById('cancelEditBtn').classList.remove('hidden');
            };

            function handleError(error, defaultMessage) {
                const messageElement = document.getElementById('message');
                messageElement.className = "p-4 rounded-md text-center";

                if (error.response) {
                    if (error.response.data.errors) {
                        const errorMessages = Object.values(error.response.data.errors).flat().join(', ');
                        messageElement.textContent = `Validation Error: ${errorMessages}`;
                    } else {
                        messageElement.textContent = error.response.data.message || defaultMessage;
                    }

                    if (error.response.status === 401) {
                        localStorage.removeItem('auth_token');
                        window.location.href = '/login';
                    }
                    messageElement.classList.add('bg-red-100', 'text-red-800');
                } else if (error.request) {
                    messageElement.textContent = 'No response received from server.';
                    messageElement.classList.add('bg-red-100', 'text-red-800');
                } else {
                    messageElement.textContent = defaultMessage;
                    messageElement.classList.add('bg-red-100', 'text-red-800');
                }

                console.error('Error:', error);
            }

            function showMessage(message, type) {
                const messageElement = document.getElementById('message');
                messageElement.textContent = message;
                messageElement.className = `p-4 rounded-md text-center ${
        type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
    }`;

                setTimeout(() => {
                    messageElement.className = 'hidden';
                }, 5000);
            }
        } catch (error) {
            console.error('Initialization error:', error);
            window.location.href = error.response?.status === 403 ? '/error' : '/login';
        }
    });
</script>
@endsection