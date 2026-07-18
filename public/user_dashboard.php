<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Customer.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$customer = new Customer($conn);
$my_bookings = $customer->viewMyBookings($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">🏨 Hostel Booking - Customer</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Toka (Logout)</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow p-4 mb-4">
            <h3>Karibu, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h3>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Bookings Zako</h4>
                <a href="book_room.php" class="btn btn-success">+ Fanya Booking Mpya</a>
            </div>

            <?php if (empty($my_bookings)): ?>
                <div class="alert alert-info">Bado hujafanya booking yoyote.</div>
            <?php else: ?>
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Chumba</th>
                            <th>Aina</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Bei</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                                <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                                <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                                <td>TZS <?php echo number_format($booking['total_price'], 2); ?></td>
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