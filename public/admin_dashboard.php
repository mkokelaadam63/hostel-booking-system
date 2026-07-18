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
$all_bookings = $admin->viewAllBookings();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">🏨 Hostel Booking - Admin Panel</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Toka (Logout)</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow p-4">
            <h3 class="mb-4">Karibu Admin, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h3>

            <div class="mb-4">
                <a href="add_room.php" class="btn btn-success me-2">+ Ongeza Chumba Kipya</a>
                <a href="manage_rooms.php" class="btn btn-primary me-2">Simamia Vyumba (Rooms)</a>
                <a href="reports.php" class="btn btn-info">📊 Ripoti</a>
            </div>

            <h4 class="mb-3">Bookings Zote</h4>

            <?php if (empty($all_bookings)): ?>
                <div class="alert alert-info">Bado hakuna booking yoyote.</div>
            <?php else: ?>
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Mteja</th>
                            <th>Chumba</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                                <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $booking['booking_status'] === 'confirmed' ? 'success' : 
                                             ($booking['booking_status'] === 'cancelled' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo htmlspecialchars($booking['booking_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>
