<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blue Fox Academy Admin Dashboard')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/icon/bluefoxacademy.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #070F2B;
            --secondary-color: #1B1A55;
            --accent-color: #535C91;
            --light-background: #F5F5F5;
            --text-color: #333333;
            --hover-color: #9290C3;
        }

        * {
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-background);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Responsive Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            transform: translateX(-100%);
            transition: transform 0.4s ease-in-out;
            z-index: 1050;
            overflow-y: auto;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            background-color: var(--secondary-color);
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
        }

        .sidebar-menu li a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            border-left: 4px solid transparent;
        }

        .sidebar-menu li a:hover {
            background-color: var(--hover-color);
            border-left-color: var(--accent-color);
        }

        .sidebar-menu li a i {
            margin-right: 12px;
            width: 25px;
            text-align: center;
        }

        /* Topbar */
        .topbar {
            background-color: var(--primary-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .main-content {
            padding: 30px;
            margin-left: 0;
            transition: margin-left 0.4s ease;
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 250px;
            }

            .sidebar-overlay {
                display: none !important;
            }
        }

        @media (max-width: 991px) {
            .main-content {
                padding: 15px;
            }
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card h5 {
            color: var(--secondary-color);
            font-weight: bold;
            margin-bottom: 15px;
        }

        .dashboard-card .display-4 {
            color: var(--accent-color);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark topbar">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#">EduChamp</a>
            <div class="ms-auto">
                <ul class="navbar-nav flex-row">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="/adminprofile">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-logout>
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">EduChamp</div>
        <ul class="sidebar-menu">
    <li><a href="/admindashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="/approverequest"><i class="fas fa-check-circle"></i> Approve Requests</a></li>
    <li><a href="/category"><i class="fas fa-tags"></i> Categories</a></li>
    <li><a href="/Coursesapprovelist"><i class="fas fa-clipboard-check"></i> Courses Approve List</a></li>
    <li><a href="/adminmanageuser"><i class="fas fa-user-cog"></i> Manage Users</a></li>
    <li><a href="/adminmanageadmission"><i class="fas fa-user-cog"></i> Manage Admission</a></li>
    <li><a href="/querylist"><i class="fas fa-question-circle"></i> Query List</a></li>
    <li><a href="/adminprofile"><i class="fas fa-id-badge"></i> My Profile</a></li>
</ul>

    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');

        // Sidebar toggle functionality
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        }

        // Sidebar toggle button
        sidebarToggle.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking outside on mobile
        mainContent.addEventListener('click', function(event) {
            if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        });

        // Logout functionality
        const logoutLink = document.querySelector('a[href="#"][data-logout]');
        
        if (logoutLink) {
            logoutLink.addEventListener('click', function(event) {
                event.preventDefault();
                
                const authToken = localStorage.getItem('auth_token');
                
                if (!authToken) {
                    alert('No authentication token found. Please log in again.');
                    window.location.href = '/';
                    return;
                }

                logoutLink.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
                logoutLink.classList.add('disabled');

                axios.post('/api/logout', {}, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    sessionStorage.clear();

                    console.log(response.data.message);
                    window.location.href = '/';
                })
                .catch(error => {
                    logoutLink.innerHTML = '<i class="fas fa-sign-out-alt"></i> Logout';
                    logoutLink.classList.remove('disabled');

                    console.error('Logout failed:', error.response ? error.response.data : error.message);
                    
                    alert('Logout failed. Please try again.');

                    if (error.response && error.response.status === 401) {
                        localStorage.removeItem('auth_token');
                        window.location.href = '/';
                    }
                });
            });
        }
    });
    </script>
</body>
</html>