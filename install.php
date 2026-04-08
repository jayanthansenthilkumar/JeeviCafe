<?php
/**
 * Jeevi's Cafe - Single File Installer
 * Run this file in your browser ONCE to structurally deploy the entire database context!
 * It will self-delete upon successful execution for security.
 */

$host = 'localhost';
$username = 'root';
$password = ''; // Default XAMPP

// 1. Establish Master MySQL connection
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("<h1 style='color:red;'>Connection failed: " . $conn->connect_error . "</h1>");
}

// 2. Initialize Core Database
$conn->query("CREATE DATABASE IF NOT EXISTS smart_canteen_db");
$conn->select_db('smart_canteen_db');

// 3. Assemble Architectures (Tables)
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin', 'staff') DEFAULT 'user',
        register_number VARCHAR(50) UNIQUE,
        wallet DECIMAL(10,2) DEFAULT 0.00,
        loyalty_points INT DEFAULT 0
    )",
    "CREATE TABLE IF NOT EXISTS menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        food_name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        diet_type VARCHAR(50) DEFAULT 'Veg',
        is_available BOOLEAN DEFAULT TRUE
    )",
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        food_name VARCHAR(100) NOT NULL,
        pickup_time VARCHAR(50) NOT NULL,
        quantity INT DEFAULT 1,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        total_price DECIMAL(10,2) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_rated BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        discount_value DECIMAL(10,2) NOT NULL,
        max_uses INT NOT NULL,
        current_uses INT DEFAULT 0,
        target_role ENUM('user', 'staff') DEFAULT 'user',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS user_vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        voucher_id INT NOT NULL,
        claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS menu_polls (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(100) NOT NULL,
        votes INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE
    )",
    "CREATE TABLE IF NOT EXISTS user_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        poll_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (poll_id) REFERENCES menu_polls(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT NOT NULL,
        review_text TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
         die("Error deploying table architecture: " . $conn->error);
    }
}

// 4. Inject Native Profiles (Using 'IGNORE' to prevent duplicate execution errors natively)
$conn->query("INSERT IGNORE INTO users (username, password, role, wallet, register_number) VALUES 
    ('admin', 'admin123', 'admin', 0.00, 'ADMIN-001'),
    ('student1', 'student1', 'user', 50.00, 'STU-4509'),
    ('staff123', 'staff123', 'staff', 0.00, 'FAC-1004')
");

// 5. Populate Starting Menu items
$checkMenu = $conn->query("SELECT * FROM menu");
if ($checkMenu->num_rows == 0) {
    $conn->query("INSERT INTO menu (food_name, price, diet_type) VALUES 
        ('Gourmet Chicken Sub', 12.50, 'Non-Veg'),
        ('Spicy Paneer Wrap', 8.00, 'Veg'),
        ('Vegan Avocado Bowl', 10.50, 'Vegan')
    ");
}

// Structural finish
if (file_exists(__FILE__)) {
    unlink(__FILE__); // Autodestructs the script securely!
}

echo "<div style='font-family:sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; background: #ebf8ff; border-radius: 15px; text-align: center; border: 2px solid #2b6cb0;'>";
echo "<h1 style='color:#2b6cb0;'>✅ System Architecture Booted!</h1>";
echo "<p style='color:#4a5568;'>The Smart Canteen Database has been successfully fully mounted, built, and populated.</p>";
echo "<br><hr style='border: 1px solid #cbd5e0;'><br>";
echo "<p><strong>Admin Node:</strong> admin / admin123</p>";
echo "<p><strong>Student Node:</strong> student1 / student1</p>";
echo "<p><strong>Faculty Node:</strong> staff123 / staff123</p>";
echo "<br><a href='login.php' style='display:inline-block; padding: 12px 24px; background: #2b6cb0; color:white; border-radius:8px; text-decoration:none; font-weight:bold;'>Advance to Portals 🚀</a>";
echo "</div>";

$conn->close();
?>
