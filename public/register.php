<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Customer.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Tafadhali jaza sehemu zote muhimu.";
    } elseif ($password !== $confirm_password) {
        $error = "Password hazifanani.";
    } elseif (strlen($password) < 6) {
        $error = "Password inatakiwa iwe na herufi/namba angalau 6.";
    } else {
        $database = new Database();
        $conn = $database->connect();

        if ($role === 'admin') {
            $user = new Admin($conn);
        } else {
            $user = new Customer($conn);
        }

        $user->setFullName($full_name);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setPhone($phone);

        try {
            if ($user->register()) {
                $success = "Usajili umefanikiwa! Sasa unaweza kuingia (login).";
            } else {
                $error = "Kuna tatizo, jaribu tena.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email hii tayari imesajiliwa. Tumia email nyingine au ingia (login) badala yake.";
            } else {
                $error = "Kuna tatizo la kiufundi, jaribu tena baadaye.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Usajili - Hostel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow p-4" style="width: 450px;">
            <h2 class="text-center mb-4">🏨 Jisajili (Register)</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label class="form-label">Jina Kamili:</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Namba ya Simu:</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rudia Password:</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Wewe ni:</label>
                    <select name="role" class="form-select">
                        <option value="customer">Mteja (Customer)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Jisajili</button>
            </form>

            <p class="text-center mt-3">Tayari una akaunti? <a href="login.php">Ingia hapa</a></p>
        </div>
    </div>

    <footer class="text-center mt-4">
        <small>&copy; 2026 Hostel Booking System - CBE Project</small>
    </footer>
</body>
</html>