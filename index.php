<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .night-mode body, body.night-mode {
            background: #181c24 !important;
            color: #e0e0e0 !important;
        }
        .night-mode .card, body.night-mode .card {
            background: #232a34 !important;
            color: #e0e0e0 !important;
            border: none;
        }
        .night-mode .card-header, body.night-mode .card-header {
            background: #232a34 !important;
            color: #e0e0e0 !important;
            border-bottom: 1px solid #232a34;
        }
        .night-mode .bg-primary, body.night-mode .bg-primary {
            background: #1a237e !important;
        }
        .night-mode .bg-success, body.night-mode .bg-success {
            background: #14532d !important;
        }
        .night-mode .bg-warning, body.night-mode .bg-warning {
            background: #b45309 !important;
        }
        .night-mode .bg-info, body.night-mode .bg-info {
            background: #164e63 !important;
        }
        .night-mode #sidebar, body.night-mode #sidebar {
            background: #23232b !important;
        }
        .night-mode .navbar, body.night-mode .navbar {
            background: #23232b !important;
            color: #e0e0e0 !important;
        }
        .night-toggle-btn {
            background: rgba(0,0,0,0.2);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: 12px;
            transition: background 0.2s;
        }
        .night-toggle-btn:hover {
            background: rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white">
            <div class="sidebar-header">
                <h3>Gym Management</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li>
                    <a href="members.php"><i class="fas fa-users"></i> Members</a>
                </li>
                <li>
                    <a href="trainers.php"><i class="fas fa-user-tie"></i> Trainers</a>
                </li>
                <li>
                    <a href="equipment.php"><i class="fas fa-dumbbell"></i> Equipment</a>
                </li>
                <li>
                    <a href="plans.php"><i class="fas fa-credit-card"></i> Membership Plans</a>
                </li>
                <li>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="navbar-text me-2">
                            Welcome, <?php echo $_SESSION['username']; ?>
                        </span>
                    </div>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Members</h5>
                                <h2 class="card-text" id="totalMembers">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Trainers</h5>
                                <h2 class="card-text" id="totalTrainers">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Equipment Items</h5>
                                <h2 class="card-text" id="totalEquipment">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Plans</h5>
                                <h2 class="card-text" id="totalPlans">0</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Members</h5>
                            </div>
                            <div class="card-body">
                                <div id="recentMembers"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Upcoming Renewals</h5>
                            </div>
                            <div class="card-body">
                                <div id="upcomingRenewals"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 