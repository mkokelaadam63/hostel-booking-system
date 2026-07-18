<?php

abstract class User {
    protected $id;
    protected $full_name;
    protected $email;
    protected $password;
    protected $role;
    protected $phone;
    protected $conn;

    protected static $secret_key = "HOSTEL_SECRET_KEY_2026";
    protected static $cipher = "AES-128-ECB";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public static function encryptData($data) {
        return openssl_encrypt($data, self::$cipher, self::$secret_key);
    }

    public static function decryptData($data) {
        return openssl_decrypt($data, self::$cipher, self::$secret_key);
    }

    public function setFullName($name) {
        $this->full_name = self::encryptData($name);
    }

    public function setEmail($email) {
        $this->email = self::encryptData($email);
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setPhone($phone) {
        $this->phone = self::encryptData($phone);
    }

    public function register() {
        $sql = "INSERT INTO users (full_name, email, password, role, phone) 
                VALUES (:full_name, :email, :password, :role, :phone)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public function login($email, $password) {
        $encrypted_email = self::encryptData($email);

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $encrypted_email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $user['full_name'] = self::decryptData($user['full_name']);
            $user['email'] = self::decryptData($user['email']);
            $user['phone'] = self::decryptData($user['phone']);
            return $user;
        }

        return false;
    }

    abstract public function getDashboard();
}

