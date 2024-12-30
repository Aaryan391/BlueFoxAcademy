@extends('adminlayout.adminapp')
@section('title', 'Profile')
@section('content')
<link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
    <div class="row">
        <div class="col-lg-12 m-b30">
            <div class="widget-box">
                <div class="wc-title">
                    <h4>Update Profile</h4>
                </div>
                <div class="widget-inner">
                    <form id="updateProfileForm" class="edit-profile" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Profile Picture Section -->
                            <div class="col-12 text-center mb-4">
                                <div class="profile-picture-container">
                                    <img id="profilePicturePreview" 
                                         src="{{ asset('storage/default-profile.png') }}" 
                                         alt="Profile Picture" 
                                         class="img-fluid rounded-circle mb-3" 
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                    />
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input" 
                                               id="profile_picture" 
                                               name="profile_picture" 
                                               accept="image/*"
                                        >
                                        <label class="custom-file-label" for="profile_picture">
                                            Choose Profile Picture
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Details -->
                            <div class="col-12">
                                <h3 class="mb-3">1. Personal Information</h3>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Enter Full Name" 
                                       required
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Enter Email" 
                                       required 
                                       readonly
                                >
                            </div>

                            <!-- Contact Details -->
                            <div class="form-group col-md-6">
                                <label for="phone">Phone Number</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="Enter Phone Number"
                                >
                            </div>

                            <!-- Address Section -->
                            <div class="col-12 mt-3">
                                <h3>2. Address Details</h3>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address">Street Address</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="address" 
                                       name="address" 
                                       placeholder="Enter Street Address"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="city">City</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="city" 
                                       name="city" 
                                       placeholder="Enter City"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="state">State</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="state" 
                                       name="state" 
                                       placeholder="Enter State"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="postcode">Postal Code</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="postcode" 
                                       name="postcode" 
                                       placeholder="Enter Postal Code"
                                >
                            </div>

                            <!-- Teacher-Specific Details -->
                            <div class="col-12 mt-3">
                                <h3>3. Professional Information</h3>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="occupation">Occupation</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="occupation" 
                                       name="occupation" 
                                       placeholder="Enter Occupation"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_name">Company/Institution</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="company_name" 
                                       name="company_name" 
                                       placeholder="Enter Company/Institution Name"
                                >
                            </div>
                            <div class="form-group col-12">
                                <label for="teacher_expertise">Professional Expertise</label>
                                <textarea class="form-control" 
                                          id="teacher_expertise" 
                                          name="teacher_expertise" 
                                          rows="3" 
                                          placeholder="Describe your professional expertise"
                                ></textarea>
                            </div>

                            <!-- Social Links -->
                            <div class="col-12 mt-3">
                                <h3>4. Social Media Profiles</h3>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="linkedin">LinkedIn Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="linkedin" 
                                       name="linkedin" 
                                       placeholder="LinkedIn Profile URL"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="facebook">Facebook Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="facebook" 
                                       name="facebook" 
                                       placeholder="Facebook Profile URL"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="twitter">Twitter Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="twitter" 
                                       name="twitter" 
                                       placeholder="Twitter Profile URL"
                                >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="instagram">Instagram Profile</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="instagram" 
                                       name="instagram" 
                                       placeholder="Instagram Profile URL"
                                >
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fa fa-save"></i> Update Profile
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Notification Area -->
                    <div id="message" class="alert mt-3" style="display: none;"></div>
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

    const form = document.getElementById('updateProfileForm');
    const profilePictureInput = document.getElementById('profile_picture');
    const profilePicturePreview = document.getElementById('profilePicturePreview');

    // Profile Picture Preview
    profilePictureInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePicturePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Fetch user data on page load
    fetchUserData();

    // Form submission handler
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        await updateUserProfile(formData);
    });

    // Fetch user data function
    async function fetchUserData() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const response = await axios.get('/api/user', {
                headers: { Authorization: `Bearer ${token}` }
            });

            const user = response.data;
            
            // Populate form fields
            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('phone').value = user.contact_details.phone || '';
            document.getElementById('address').value = user.contact_details.address || '';
            document.getElementById('city').value = user.contact_details.city || '';
            document.getElementById('state').value = user.contact_details.state || '';
            document.getElementById('postcode').value = user.contact_details.postcode || '';

            // Teacher details
            if (user.teacher_details) {
                document.getElementById('occupation').value = user.teacher_details.occupation || '';
                document.getElementById('company_name').value = user.teacher_details.company_name || '';
                document.getElementById('teacher_expertise').value = user.teacher_details.expertise || '';
                document.getElementById('linkedin').value = user.teacher_details.social_links.linkedin || '';
                document.getElementById('facebook').value = user.teacher_details.social_links.facebook || '';
                document.getElementById('twitter').value = user.teacher_details.social_links.twitter || '';
                document.getElementById('instagram').value = user.teacher_details.social_links.instagram || '';
            }

            // Update profile picture
            if (user.profile_info && user.profile_info.profile_picture) {
                profilePicturePreview.src = `{{ asset('storage/') }}/${user.profile_info.profile_picture}`;
            }
        } catch (error) {
            showMessage('Error fetching user data', 'danger');
            console.error(error);
        }
    }

    // Update user profile function
    async function updateUserProfile(data) {
        try {
            const token = localStorage.getItem('auth_token');
            await axios.post('/api/user/update', data, {
                headers: { 
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data'
                }
            });

            showMessage('Profile updated successfully!', 'success');
            fetchUserData(); // Refresh data after update
        } catch (error) {
            const errorMsg = error.response?.data?.message || 'Profile update failed';
            showMessage(errorMsg, 'danger');
            
            if (error.response?.status === 401) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            }
        }
    }

    // Show message function
    function showMessage(message, type) {
        const msgElement = document.getElementById('message');
        msgElement.textContent = message;
        msgElement.className = `alert alert-${type}`;
        msgElement.style.display = 'block';
        
        // Auto-hide message after 5 seconds
        setTimeout(() => {
            msgElement.style.display = 'none';
        }, 5000);
    }
} catch (error) {
        console.error('Initialization error:', error);
        window.location.href = error.response?.status === 403 ? '/error' : '/login';
    }
});
</script>
@endsection