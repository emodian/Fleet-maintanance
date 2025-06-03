<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pope's Tr | Fleet Maintenance Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --collapsed-sidebar: 80px;
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #94a3b8;
            --sidebar-bg: rgba(30, 41, 59, 0.95);
            --content-bg: rgba(0, 0, 0, 0);
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--light);
            color: var(--dark);
            overflow-x: hidden;
            background-image: url('background.jpg'); /* Use the same image as in login.html */
            background-size: cover; /* Ensure the image covers the entire screen */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Prevent the image from repeating */
            position: relative;
        }

        /* Remove the overlay completely */
        body::before {
            content: ''; /* Keep this if you want to retain the pseudo-element */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: none; /* Remove any overlay */
            z-index: -1;
        }

        /* Remove or adjust the .bg-overlay if it exists */
        .bg-overlay {
            display: none; /* Hide the overlay completely */
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: white;
            position: fixed;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar.collapsed {
            width: var(--collapsed-sidebar);
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: white;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 1.75rem;
            color: var(--primary);
        }

        .logo-text {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--gray);
            font-size: 1.25rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            color: white;
            transform: rotate(180deg);
        }

        .sidebar.collapsed .toggle-btn {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .toggle-btn:hover {
            transform: rotate(0deg);
        }

        .user-profile {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            transition: all 0.3s;
        }

        .sidebar.collapsed .user-avatar {
            width: 40px;
            height: 40px;
        }

        .user-info {
            text-align: center;
            transition: all 0.3s;
        }

        .sidebar.collapsed .user-info {
            opacity: 0;
            height: 0;
            overflow: hidden;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .user-role {
            font-size: 0.875rem;
            color: var(--gray);
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            display: inline-block;
        }

        .nav-menu {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .nav-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray);
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .sidebar.collapsed .nav-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--gray);
            text-decoration: none;
            transition: all 0.2s;
            margin: 0.25rem 0;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: white;
            background-color: rgba(37, 99, 235, 0.2);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--primary);
        }

        .nav-icon {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
            margin-right: 1rem;
            transition: all 0.3s;
        }

        .sidebar.collapsed .nav-icon {
            margin-right: 0;
            font-size: 1.5rem;
        }

        .nav-text {
            transition: all 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .nav-badge {
            margin-left: auto;
            background-color: var(--danger);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem;
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .logout-btn:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }

        .logout-icon {
            margin-right: 0.75rem;
            transition: all 0.3s;
        }

        .sidebar.collapsed .logout-icon {
            margin-right: 0;
            font-size: 1.25rem;
        }

        .logout-text {
            transition: all 0.3s;
        }

        .sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--content-bg);
           
        }

        .main-content.collapsed {
            margin-left: var(--collapsed-sidebar);
        }

        /* Top Navigation */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--card-bg);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var (--dark);
        }

        .page-title p {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .search-bar {
            position: relative;
            margin-right: 1rem; /* Add spacing between search bar and other elements */
        }

        .search-bar input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            width: 300px; /* Increased width for better usability */
            transition: all 0.3s;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .notification-btn, .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray);
            cursor: pointer;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            width: 20px; /* Slightly larger badge */
            height: 20px;
            border-radius: 50%;
            font-size: 0.875rem; /* Adjusted font size */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn {
            display: none;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 2rem;
        }

        .stats-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem; /* Increased spacing between cards */
            overflow-x: auto;
            padding: 1.5rem 0; /* Adjusted padding for better spacing */
        }

        .stat-card {
            flex: 0 0 auto;
            width: 300px; /* Increased width for larger boxes */
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 2rem; /* Increased padding for larger content */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.primary {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        .stat-icon.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }

        .stat-icon.warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .stat-icon.danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-title {
            font-size: 1rem;
            color: var(--gray);
        }

        .stat-change {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .stat-change.positive {
            color: var(--secondary);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        .card-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .card-btn:hover {
            color: var (--primary);
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .search-bar input {
                width: 200px;
            }
        }

        @media (max-width: 576px) {
            .search-bar {
                display: none;
            }

            .dashboard-content {
                padding: 1rem;
            }
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed; /* Fix the footer at the bottom */
            bottom: 0;
            left: 0;
            width: 100%; /* Ensure the footer spans the entire width */
        }

        footer a {
            color: #3498db;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Background Image Overlay -->
    <div class="bg-overlay"></div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-truck-moving logo-icon"></i>
                <span class="logo-text">Transport Division </span>
            </a>
            <button class="toggle-btn" id="toggleSidebar">
                <i class="fas fa-angle-left"></i>
            </button>
        </div>

        <nav class="nav-menu">
            <div class="nav-title">Main</div>
            <ul>
                
                <li class="nav-item">
                    <a href="news_and_alerts.php" class="nav-link">
                        <i class="fas fa-newspaper nav-icon"></i>
                        <span class="nav-text">News and Alerts</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="vehicles.php" class="nav-link">
                        <i class="fas fa-truck-moving nav-icon"></i>
                        <span class="nav-text">Vehicles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="maintenance.php" class="nav-link">
                        <i class="fas fa-tools nav-icon"></i>
                        <span class="nav-text">Maintenance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="fuel.php" class="nav-link">
                        <i class="fas fa-gas-pump nav-icon"></i>
                        <span class="nav-text">Fuel Management</span>
                    </a>
                </li>
            </ul>

            <div class="nav-title">Operations</div>
            <ul>
                <li class="nav-item">
                    <a href="drivers.php" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <span class="nav-text">Drivers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="trip_logs.php" class="nav-link">
                        <i class="fas fa-route nav-icon"></i>
                        <span class="nav-text">Trip Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="inventory.php" class="nav-link">
                        <i class="fas fa-boxes nav-icon"></i>
                        <span class="nav-text">Inventory</span>
                    </a>
                </li>
            </ul>

            <div class="nav-title">Role-Specific</div>
            <ul>
                
                <li class="nav-item">
                    <a href="reports.php" class="nav-link">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                </li>
               
                <li class="nav-item">
                    <a href="financials.php" class="nav-link">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <span class="nav-text">Financials</span>
                    </a>
                </li>
               
                <li class="nav-item">
                    <a href="maintenance_tasks_done.php" class="nav-link">
                        <i class="fas fa-wrench nav-icon"></i>
                        <span class="nav-text">Repairs and Services</span>
                    </a>
                </li>
            
            </ul>
          

            <div class="nav-title">Support</div>
            <ul>
                <li class="nav-item">
                    <a href="help.php" class="nav-link">
                        <i class="fas fa-question-circle nav-icon"></i>
                        <span class="nav-text">Help and Support</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-btn" onclick="window.location.href='login.php?message=Thank you for using FleetPro!'">
                    <i class="fas fa-sign-out-alt logout-icon"></i>
                    <span class="logout-text">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="page-title">
                <h1>Dashboard</h1>
                <p>Welcome, to Pope's Tr</p>
            </div>

            <div class="nav-actions">
                <div class="search-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search...">
                </div>
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">42</div>
                            <div class="stat-title">Total Vehicles</div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">8</div>
                            <div class="stat-title">Pending Maintenance</div>
                        </div>
                        <div class="stat-icon danger">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-up"></i> 3 new requests
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">24</div>
                            <div class="stat-title">Active Trips</div>
                        </div>
                        <div class="stat-icon success">
                            <i class="fas fa-route"></i>
                        </div>
                    </div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 5 new trips today
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">Tsh 4,530,450</div>
                            <div class="stat-title">Monthly Fuel Cost</div>
                        </div>
                        <div class="stat-icon warning">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                    </div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-up"></i> 8% increase
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="main-grid">
                <!-- Recent Maintenance -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Maintenance</h2>
                        <div class="card-actions">
                            <button class="card-btn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="card-btn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Maintenance table would go here -->
                </div>

                <!-- Alerts -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Alerts</h2>
                        <div class="card-actions">
                            <button class="card-btn">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Alerts list would go here -->
                </div>
            </div>
        </div>
    </main>

    <footer>
        © 2025 Fleet Maintenance Management System<br>
        Pope's Tr Headquarters | P.O.Box 1600 Dar es Salaam | Tanzania. | Phone: +255781636843 | Email: info@popestr.com | Website: <a href="http://www.popestr.com" target="_blank">www.popestr.com</a>
    </footer>

    <script>
        // Toggle sidebar
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });

        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Auto-collapse sidebar on smaller screens
        function handleResize() {
            if (window.innerWidth < 1200) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
            }
        }

        // Initial check
        handleResize();
        
        // Add resize listener
        window.addEventListener('resize', handleResize);

        // Example data for alerts
        const alerts = [
            { id: 1, message: "Vehicle maintenance overdue" },
            { id: 2, message: "Fuel levels low" },
            { id: 3, message: "Driver license expiring soon" },
        ];

        // Update the notification badge with the number of alerts
        const notificationBadge = document.querySelector(".notification-badge");
        notificationBadge.textContent = alerts.length;

        // Implement search functionality
        const searchInput = document.querySelector(".search-bar input");
        searchInput.addEventListener("input", (event) => {
            const query = event.target.value.toLowerCase();
            const filteredAlerts = alerts.filter((alert) =>
                alert.message.toLowerCase().includes(query)
            );
            console.log("Filtered Alerts:", filteredAlerts); // Replace with actual display logic
        });
    </script>
</body>
</html>