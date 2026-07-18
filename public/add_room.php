<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Admin.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number']);
    $room_type = $_POST['room_type'];
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($room_number) || empty($price)) {
        $error = "Tafadhali jaza namba ya chumba na bei.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Bei lazima iwe namba sahihi zaidi ya sifuri.";
    } else {
        $database = new Database();
        $conn = $database->connect();
        $admin = new Admin($conn);

        if ($admin->addRoom($room_number, $room_type, $price, $description)) {
            $success = "Chumba kimeongezwa kikamilifu!";
        } else {
            $error = "Kuna tatizo, jaribu tena.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ongeza Chumba - Admin</title>
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

    <div class="container d-flex justify-content-center">
        <div class="card shadow p-4" style="width: 450px;">
            <h3 class="mb-4">Ongeza Chumba Kipya</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="add_room.php">
                <div class="mb-3">
                    <label class="form-label">Namba ya Chumba:</label>
                    <input type="text" name="room_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Aina ya Chumba:</label>
                    <select name="room_type" class="form-select">
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bei kwa Usiku (TZS):</label>
                    <input type="number" name="price" step="0.01" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maelezo:</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Ongeza Chumba</button>
            </form>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>

