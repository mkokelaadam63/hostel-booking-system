<?php
require_once __DIR__ . '/User.php';

class Customer extends User {

    public function __construct($conn) {
        parent::__construct($conn);
        $this->role = "customer";
    }

    public function getDashboard() {
        return "user_dashboard.php";
    }

    public function bookRoom($user_id, $room_id, $check_in, $check_out, $total_price) {
        $encrypted_price = self::encryptData($total_price);

        $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, total_price) 
                VALUES (:user_id, :room_id, :check_in, :check_out, :total_price)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':check_in', $check_in);
        $stmt->bindParam(':check_out', $check_out);
        $stmt->bindParam(':total_price', $encrypted_price);

        return $stmt->execute();
    }

    public function viewMyBookings($user_id) {
        $sql = "SELECT bookings.*, rooms.room_number, rooms.room_type 
                FROM bookings 
                JOIN rooms ON bookings.room_id = rooms.id 
                WHERE bookings.user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bookings as &$booking) {
            $booking['total_price'] = self::decryptData($booking['total_price']);
        }

        return $bookings;
    }
}
