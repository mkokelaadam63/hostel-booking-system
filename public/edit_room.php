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

$error = "";
$success = "";

if (!isset($_GET['id'])) {
    header("Location: manage_rooms.php");
    exit();
}

$room_id = $_GET['id'];
$room = $admin->getRoomById($room_id);

if (!$room) {
    header("Location: manage_rooms.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number']);
    $room_type = $_POST['room_type'];
    $price = trim($_POST['price']);
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    if (empty($room_number) || empty($price)) {
        $error = "Tafadhali jaza namba ya chumba na bei.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Bei lazima iwe namba sahihi zaidi ya sifuri.";
    } else {
        if ($admin->updateRoom($room_id, $room_number, $room_type, $price, $status, $description)) {
            $success = "Chumba kimesasishwa kikamilifu!";
            $room = $admin->getRoomById($room_id);
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
    <title>Hariri Chumba - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">🏨 Hostel Booking - Admin Panel</span>
            <a href="manage_rooms.php" class="btn btn-outline-light btn-sm">← Rudi Manage Rooms</a>
        </div>
    </nav>

    <div class="container d-flex justify-content-center">
        <div class="card shadow p-4" style="width: 450px;">
            <h3 class="mb-4">Hariri Chumba</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_room.php?id=<?php echo $room_id; ?>">
                <div class="mb-3">
                    <label class="form-label">Namba ya Chumba:</label>
                    <input type="text" name="room_number" class="form-control" 
                           value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Aina ya Chumba:</label>
                    <select name="room_type" class="form-select">
                        <option value="single" <?php echo $room['room_type'] === 'single' ? 'selected' : ''; ?>>Single</option>
                        <option value="double" <?php echo $room['room_type'] === 'double' ? 'selected' : ''; ?>>Double</option>
                        <option value="suite" <?php echo $room['room_type'] === 'suite' ? 'selected' : ''; ?>>Suite</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bei kwa Usiku (TZS):</label>
                    <input type="number" name="price" step="0.01" class="form-control" 
                           value="<?php echo htmlspecialchars($room['price_per_night']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status:</label>
                    <select name="status" class="form-select">
                        <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="booked" <?php echo $room['status'] === 'booked' ? 'selected' : ''; ?>>Booked</option>
                        <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maelezo:</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($room['description']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sasisha Chumba</button>
            </form>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>
