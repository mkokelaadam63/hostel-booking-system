<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();
$admin = new Admin($conn);

$report = $admin->getReportSummary();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ripoti - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">🏨 Hostel Booking - Admin Panel</span>
            <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">← Rudi Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow p-4 mb-4">
            <h3 class="mb-4">📊 Ripoti ya Mfumo</h3>

            <div class="row text-center mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white p-3">
                        <h4><?php echo $report['total_bookings']; ?></h4>
                        <small>Jumla ya Bookings</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white p-3">
                        <h4>TZS <?php echo number_format($report['total_revenue'], 2); ?></h4>
                        <small>Jumla ya Mapato</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white p-3">
                        <h4><?php echo $report['total_customers']; ?></h4>
                        <small>Jumla ya Wateja</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white p-3">
                        <h4><?php echo array_sum(array_column($report['rooms_by_status'], 'count')); ?></h4>
                        <small>Jumla ya Vyumba</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Bookings kwa Status</h5>
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>Status</th><th>Idadi</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['by_status'] as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['booking_status']); ?></td>
                                    <td><?php echo $row['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Vyumba kwa Status</h5>
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr><th>Status</th><th>Idadi</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['rooms_by_status'] as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo $row['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>