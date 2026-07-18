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

$error = "";
$success = "";

// SEARCH FILTERS
$search_type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Jenga query kutegemea filters zilizowekwa
$sql = "SELECT * FROM rooms WHERE status = 'available'";
$params = [];

if (!empty($search_type)) {
    $sql .= " AND room_type = :room_type";
    $params[':room_type'] = $search_type;
}

if (!empty($max_price) && is_numeric($max_price)) {
    $sql .= " AND price_per_night <= :max_price";
    $params[':max_price'] = $max_price;
}

$sql .= " ORDER BY price_per_night ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$available_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// BOOKING SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    if (empty($room_id) || empty($check_in) || empty($check_out)) {
        $error = "Tafadhali jaza sehemu zote.";
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $error = "Tarehe ya kutoka lazima iwe baada ya tarehe ya kuingia.";
    } else {
        $sql2 = "SELECT price_per_night FROM rooms WHERE id = :room_id";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':room_id', $room_id);
        $stmt2->execute();
        $room = $stmt2->fetch(PDO::FETCH_ASSOC);

        $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        $total_price = $nights * $room['price_per_night'];

        $customer = new Customer($conn);
        if ($customer->bookRoom($_SESSION['user_id'], $room_id, $check_in, $check_out, $total_price)) {
            $sql3 = "UPDATE rooms SET status = 'booked' WHERE id = :room_id";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bindParam(':room_id', $room_id);
            $stmt3->execute();

            $success = "Booking imefanikiwa! Jumla: TZS " . number_format($total_price, 2);

            // Refresh orodha ya vyumba baada ya booking
            $stmt->execute($params);
            $available_rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Fanya Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">🏨 Hostel Booking</span>
            <a href="user_dashboard.php" class="btn btn-outline-light btn-sm">← Rudi Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- SEARCH FORM -->
            <div class="col-md-4">
                <div class="card shadow p-3 mb-4">
                    <h5>🔍 Tafuta Chumba</h5>
                    <form method="GET" action="book_room.php">
                        <div class="mb-3">
                            <label class="form-label">Aina ya Chumba:</label>
                            <select name="room_type" class="form-select">
                                <option value="">Zote</option>
                                <option value="single" <?php echo $search_type === 'single' ? 'selected' : ''; ?>>Single</option>
                                <option value="double" <?php echo $search_type === 'double' ? 'selected' : ''; ?>>Double</option>
                                <option value="suite" <?php echo $search_type === 'suite' ? 'selected' : ''; ?>>Suite</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bei ya Juu (TZS):</label>
                            <input type="number" name="max_price" class="form-control" 
                                   placeholder="mfano: 50000" 
                                   value="<?php echo htmlspecialchars($max_price); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Tafuta</button>
                        <a href="book_room.php" class="btn btn-outline-secondary w-100 mt-2">Futa Filters</a>
                    </form>
                </div>
            </div>

            <!-- BOOKING FORM -->
            <div class="col-md-8">
                <div class="card shadow p-4">
                    <h3 class="mb-4">Fanya Booking</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if (empty($available_rooms)): ?>
                        <div class="alert alert-warning">Hakuna vyumba vinavyolingana na utafutaji wako.</div>
                    <?php else: ?>
                        <form method="POST" action="book_room.php">
                            <div class="mb-3">
                                <label class="form-label">Chagua Chumba:</label>
                                <select name="room_id" class="form-select" required>
                                    <?php foreach ($available_rooms as $room): ?>
                                        <option value="<?php echo $room['id']; ?>">
                                            Chumba <?php echo htmlspecialchars($room['room_number']); ?> 
                                            (<?php echo htmlspecialchars($room['room_type']); ?>) - 
                                            TZS <?php echo number_format($room['price_per_night'], 2); ?>/usiku
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tarehe ya Kuingia:</label>
                                <input type="date" name="check_in" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tarehe ya Kutoka:</label>
                                <input type="date" name="check_out" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Thibitisha Booking</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>
