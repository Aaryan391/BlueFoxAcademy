@extends('userlayout.userapp') 
@section('title', 'Available Courses')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
<style>
    .custom-card-header {
        background-color: var(--primary-color);
        color: white;
        border-radius: 8px 8px 0 0;
        padding: 1.5rem;
    }

    .custom-container {
        background-color: var(--light-background);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .course-card {
        transition: transform 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .course-card:hover {
        transform: translateY(-5px);
    }

    .card-img-wrapper {
        position: relative;
        overflow: hidden;
    }

    .card-img-overlay-category {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(7, 15, 43, 0.9), transparent);
        padding: 1rem;
        color: white;
    }

    .custom-badge {
        background-color: var(--accent-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .course-price {
        color: var(--secondary-color);
        font-size: 1.25rem;
    }

    .custom-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .custom-btn:hover {
        background-color: var(--hover-color);
        color: white;
    }

    .info-badge {
        background-color: rgba(83, 92, 145, 0.1);
        color: var(--accent-color);
        padding: 0.5rem 1rem;
        border-radius: 15px;
    }
</style>

<div class="custom-container">
    <div class="container">
        <div class="card shadow-lg">
            <div class="custom-card-header">
                <h4 class="mb-0">Available Courses</h4>
            </div>
            <div class="card-body p-4">
                <div class="row g-4" id="coursesContainer">
                    <!-- Courses dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const coursesContainer = document.getElementById('coursesContainer');
    
    function fetchCourses() {
        axios.get('/api/courses')
        .then(response => {
            coursesContainer.innerHTML = '';
            response.data.data.forEach(course => {
                const card = `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card course-card h-100 shadow">
                            <div class="card-img-wrapper">
                                <img src="${course.course_image ? `/storage/${course.course_image}` : '/storage/courses/default.jpg'}" 
                                     class="card-img-top" alt="${course.title}" 
                                     style="height: 220px; object-fit: cover;">
                                <div class="card-img-overlay-category">
                                    <span class="custom-badge">
                                        ${course.category.name}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0 fw-bold">${course.title}</h5>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-clock me-1"></i>
                                        <span class="small">${course.duration} hours</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="info-badge">
                                        <i class="fas fa-graduation-cap me-2"></i>${course.skill_level}
                                    </span>
                                    <span class="info-badge">
                                        <i class="fas fa-globe me-2"></i>${course.language}
                                    </span>
                                    <span class="info-badge">
                                        <i class="fas fa-${course.type === 'remote' ? 'laptop' : 'building'} me-2"></i>
                                        ${course.type.charAt(0).toUpperCase() + course.type.slice(1)}
                                    </span>
                                </div>
                                <a href="/course/${course.id}" class="custom-btn w-100 text-center text-decoration-none">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                coursesContainer.insertAdjacentHTML('beforeend', card);
            });
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
        });
    }
    
    fetchCourses();
});
</script>
@endsection