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

$success = "";

if (isset($_GET['delete_id'])) {
    $admin->deleteRoom($_GET['delete_id']);
    $success = "Chumba kimefutwa kikamilifu.";
}

$rooms = $admin->getAllRooms();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Simamia Vyumba - Admin</title>
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
        <div class="card shadow p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Simamia Vyumba (Rooms)</h3>
                <a href="add_room.php" class="btn btn-success">+ Ongeza Chumba Kipya</a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (empty($rooms)): ?>
                <div class="alert alert-info">Bado hakuna chumba kilichoongezwa.</div>
            <?php else: ?>
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Namba</th>
                            <th>Aina</th>
                            <th>Bei/Usiku</th>
                            <th>Status</th>
                            <th>Maelezo</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                <td>TZS <?php echo number_format($room['price_per_night'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $room['status'] === 'available' ? 'success' : 
                                             ($room['status'] === 'booked' ? 'warning' : 'secondary'); 
                                    ?>">
                                        <?php echo htmlspecialchars($room['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($room['description']); ?></td>
                                <td>
                                    <a href="edit_room.php?id=<?php echo $room['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="manage_rooms.php?delete_id=<?php echo $room['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Una uhakika unataka kufuta chumba hiki?');">
                                       Futa
                                    </a>
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
