@extends('teacherlayout.teacherapp')

@section('title', 'My Courses')

@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">My Courses</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Courses</li>
                        </ol>
                    </nav>
                </div>
                <div class="card-body">
                    <div id="alert-container" class="mb-4"></div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Skill Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="coursesTableBody">
                            <!-- Course data will be populated dynamically here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-container"></div>
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
        const alertContainer = document.getElementById('alert-container');
        const coursesTableBody = document.getElementById('coursesTableBody');
        const modalContainer = document.getElementById('modal-container');

        // Show alerts
        function showAlert(message, type = 'info') {
            alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        }

        // Fetch and display courses
        async function fetchCourses() {
            try {
                const response = await axios.get('/api/teacher/courses', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data && response.data.courses) {
                    const courses = response.data.courses;
                    coursesTableBody.innerHTML = courses.map(course => `
                    <tr>
                        <td>${course.title}</td>
                        <td>${course.category ? course.category.name : 'N/A'}</td>
                        <td>${course.skill_level}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editCourse(${course.id})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCourse(${course.id})">Delete</button>
                        </td>
                    </tr>
                `).join('');
                }
            } catch (error) {
                console.error('Error fetching courses:', error);
                showAlert('Failed to fetch courses.', 'danger');
            }
        }

        // Load categories
        async function loadCategories(selectedCategoryId) {
            try {
                const response = await axios.get('/api/courses/categories', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                const categories = response.data.categories;
                const categorySelect = document.getElementById('edit_category_id');
                categorySelect.innerHTML = '<option value="">Select Category</option>';

                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    if (category.id === selectedCategoryId) {
                        option.selected = true;
                    }
                    categorySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Edit course
        window.editCourse = async function(courseId) {
            try {
                const response = await axios.get(`/api/courses/${courseId}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                const course = response.data.course;

                modalContainer.innerHTML = `
                <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Course</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editCourseForm" enctype="multipart/form-data">
                                    <div class="form-group mb-3">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" value="${course.title}" maxlength="255" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" name="description" rows="4">${course.description || ''}</textarea>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="duration">Duration (hours)</label>
                                            <input type="number" class="form-control" name="duration" value="${course.duration || ''}" min="1">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="language">Language</label>
                                            <input type="text" class="form-control" name="language" value="${course.language || ''}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="category_id">Category</label>
                                            <select class="form-control" name="category_id" id="edit_category_id">
                                                <option value="">Select Category</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="skill_level">Skill Level</label>
                                            <select class="form-control" name="skill_level" required>
                                                <option value="Beginner" ${course.skill_level === 'Beginner' ? 'selected' : ''}>Beginner</option>
                                                <option value="Intermediate" ${course.skill_level === 'Intermediate' ? 'selected' : ''}>Intermediate</option>
                                                <option value="Advanced" ${course.skill_level === 'Advanced' ? 'selected' : ''}>Advanced</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="course_image">Course Image</label>
                                        <input type="file" class="form-control" name="course_image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                        <small class="text-muted">Max size: 4MB. Formats: JPEG, PNG, JPG, GIF</small>
                                    </div>
                                    <div class="form-group mb-3">
                                            <label>Schedule (JSON)</label>
                                            <textarea name="schedule" class="form-control">${course.schedule || '[]'}</textarea>
                                        </div>
                                    <div class="form-group mb-3">
                                        <label>Course Type</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="type" value="remote" ${course.type === 'remote' ? 'checked' : ''}>
                                                <label class="form-check-label">Remote</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="type" value="onsite" ${course.type === 'onsite' ? 'checked' : ''}>
                                                <label class="form-check-label">Onsite</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
    <!-- Hidden input to send 0 when unchecked -->
    <input type="hidden" name="has_assessments" value="0">
    <!-- Checkbox to send 1 when checked -->
    <input type="checkbox" class="form-check-input" name="has_assessments" value="1" id="has_assessments" ${course.has_assessments ? 'checked' : ''}>
    <label class="form-check-label" for="has_assessments">Has Assessments</label>
</div>


                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveCourseBtn">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                const modal = new bootstrap.Modal(document.getElementById('editCourseModal'));
                modal.show();

                await loadCategories(course.category_id);

                document.getElementById('saveCourseBtn').addEventListener('click', async function() {
                    const form = document.getElementById('editCourseForm');
                    const formData = new FormData(form);

                    const hasAssessments = document.getElementById('has_assessments').checked ? 1 : 0;
                    const courseData = {
                        has_assessments: hasAssessments
                    };


                    try {
                        const response = await axios.post(`/api/teacher/update/courses/${courseId}`, formData, {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        showAlert(response.data.message, 'success');
                        modal.hide();
                        fetchCourses();
                    } catch (error) {
                        console.error('Error updating course:', error);
                        showAlert(error.response?.data?.message || 'Failed to update course.', 'danger');
                    }
                });
            } catch (error) {
                console.error('Error fetching course details:', error);
                showAlert('Failed to fetch course details.', 'danger');
            }
        };

        // Delete course
        window.deleteCourse = async function(courseId) {
            if (confirm('Are you sure you want to delete this course?')) {
                try {
                    const response = await axios.delete(`/api/courses/${courseId}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'Content-Type': 'application/json'
                        }
                    });

                    showAlert(response.data.message, 'success');
                    fetchCourses();
                } catch (error) {
                    console.error('Error deleting course:', error);
                    showAlert('Failed to delete course.', 'danger');
                }
            }
        };

        // Initial load
        fetchCourses();
    } catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection