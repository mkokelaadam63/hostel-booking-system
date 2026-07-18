<?php
require_once __DIR__ . '/User.php';

class Admin extends User {

    public function __construct($conn) {
        parent::__construct($conn);
        $this->role = "admin";
    }

    public function getDashboard() {
        return "admin_dashboard.php";
    }

    public function addRoom($room_number, $room_type, $price, $description) {
        $sql = "INSERT INTO rooms (room_number, room_type, price_per_night, description) 
                VALUES (:room_number, :room_type, :price, :description)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':room_type', $room_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);

        return $stmt->execute();
    }

    public function getAllRooms() {
        $sql = "SELECT * FROM rooms ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoomById($room_id) {
        $sql = "SELECT * FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $room_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRoom($room_id, $room_number, $room_type, $price, $status, $description) {
        $sql = "UPDATE rooms 
                SET room_number = :room_number, room_type = :room_type, 
                    price_per_night = :price, status = :status, description = :description
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':room_type', $room_type);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $room_id);

        return $stmt->execute();
    }

    public function deleteRoom($room_id) {
        $sql = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $room_id);
        return $stmt->execute();
    }

    public function viewAllBookings() {
        $sql = "SELECT bookings.*, users.full_name, rooms.room_number 
                FROM bookings 
                JOIN users ON bookings.user_id = users.id 
                JOIN rooms ON bookings.room_id = rooms.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bookings as &$booking) {
            $booking['full_name'] = self::decryptData($booking['full_name']);
            $booking['total_price'] = self::decryptData($booking['total_price']);
        }

        return $bookings;
    }

    // Ripoti ya jumla ya mfumo
    public function getReportSummary() {
        $report = [];

        $sql1 = "SELECT COUNT(*) as total FROM bookings";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute();
        $report['total_bookings'] = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

        $sql2 = "SELECT booking_status, COUNT(*) as count FROM bookings GROUP BY booking_status";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute();
        $report['by_status'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $sql3 = "SELECT status, COUNT(*) as count FROM rooms GROUP BY status";
        $stmt3 = $this->conn->prepare($sql3);
        $stmt3->execute();
        $report['rooms_by_status'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        $sql4 = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
        $stmt4 = $this->conn->prepare($sql4);
        $stmt4->execute();
        $report['total_customers'] = $stmt4->fetch(PDO::FETCH_ASSOC)['total'];

        $sql5 = "SELECT total_price FROM bookings";
        $stmt5 = $this->conn->prepare($sql5);
        $stmt5->execute();
        $all_prices = $stmt5->fetchAll(PDO::FETCH_ASSOC);

        $total_revenue = 0;
        foreach ($all_prices as $price_row) {
            $total_revenue += (float) self::decryptData($price_row['total_price']);
        }
        $report['total_revenue'] = $total_revenue;

        return $report;
    }
}
