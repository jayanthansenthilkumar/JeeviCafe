<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';
$stmt = $conn->prepare("SELECT username, role, wallet, loyalty_points FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$username = $me['username'];
$role = trim(strtolower($me['role']));
$user_id = $_SESSION['user_id'];
$wallet = $me['wallet'];
$loyalty_points = $me['loyalty_points'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeevi's Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js" defer></script>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <h2>Jeevi's Cafe</h2>
        <ul>
            <?php if ($role === 'user'): ?>
            <li><a href="index.php">Place Order</a></li>
            <li><a href="vote.php">Community Menu Poll</a></li>
            <li><a href="studentVouchers.php">Claim Live Offers</a></li>
            <li><a href="myOrders.php">My Orders</a></li>
            <?php endif; ?>
            
            <?php if ($role === 'staff'): ?>
            <li><a href="facultyIndex.php">Request Canteen Order</a></li>
            <li><a href="facultyVote.php">Faculty Menu Input</a></li>
            <li><a href="facultyVouchers.php">Faculty Rewards Hub</a></li>
            <li><a href="facultyOrders.php">My Order History</a></li>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php">Business Dashboard</a></li>
            <li><a href="users.php">Manage Students</a></li>
            <li><a href="vouchers.php">Voucher Promotions</a></li>
            <li><a href="orders.php">Manage Orders</a></li>
            <li><a href="managePolls.php">Voting Engine</a></li>
            <li><a href="menuManager.php">Manage Live Menu</a></li>
            <li><a href="prediction.php">AI & ML Pipelines</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h3><?php 
                if($role === 'admin') echo 'Canteen Administrator'; 
                elseif($role === 'staff') echo 'College Faculty Portal';
                else echo 'Student Portal'; 
            ?></h3>
            <div class="user-info">
                <?php if($role === 'user' || $role === 'staff'): ?>
                <span class="badge badge-completed" style="background:#dd6b20; font-size:14px; margin-right:10px; padding: 6px 12px;">🌟 <span id="nav-points"><?php echo $loyalty_points; ?></span> Points</span>
                <span class="badge badge-completed" style="background:#38a169; font-size:14px; margin-right:15px; padding: 6px 12px;">💳 Vault: $<span id="nav-wallet"><?php echo number_format($wallet, 2); ?></span></span>
                <?php endif; ?>
                <span>Logged in as <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <button onclick="handleAction('logout')" class="btn-logout" style="margin-left: 10px; border:none; cursor:pointer;">Logout</button>
            </div>
        </div>
        <div class="content-body">
