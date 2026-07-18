CREATE DATABASE IF NOT EXISTS hostel_booking_db;
USE hostel_booking_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name TEXT NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    phone TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(50) NOT NULL,
    room_type ENUM('single','double','suite') NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    status ENUM('available','booked') NOT NULL DEFAULT 'available',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_price TEXT NOT NULL,
    booking_status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- default admin login: admin@hostel.com / admin123
INSERT INTO users (full_name, email, password, role, phone) VALUES
('Admin', 'JEOnGgV2xAVct0naYaHZMamLY5A81PB35n+VmW2YSFA=', '$2b$10$3En3LpAiCjvrWIKEq/KQ1OIumTkHDMRoK2z7sspnoNIrPsd.DLHyu', 'admin', 'W8IBxdT7xG2injmYUfgo+Q==');
