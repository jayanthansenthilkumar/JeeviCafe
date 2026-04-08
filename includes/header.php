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
$wallet = $me['wallet'];
$loyalty_points = $me['loyalty_points'] ?? 0;
$currentPage = basename($_SERVER['PHP_SELF']);
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
            <li><a href="index.php" class="<?php echo ($currentPage=='index.php')?'active':''; ?>">Place Order</a></li>
            <li><a href="vote.php" class="<?php echo ($currentPage=='vote.php')?'active':''; ?>">Community Menu Poll</a></li>
            <li><a href="studentVouchers.php" class="<?php echo ($currentPage=='studentVouchers.php')?'active':''; ?>">Claim Live Offers</a></li>
            <li><a href="myOrders.php" class="<?php echo ($currentPage=='myOrders.php')?'active':''; ?>">My Orders</a></li>
            <?php endif; ?>
            
            <?php if ($role === 'staff'): ?>
            <li><a href="facultyIndex.php" class="<?php echo ($currentPage=='facultyIndex.php')?'active':''; ?>">Request Canteen Order</a></li>
            <li><a href="facultyVote.php" class="<?php echo ($currentPage=='facultyVote.php')?'active':''; ?>">Faculty Menu Input</a></li>
            <li><a href="facultyVouchers.php" class="<?php echo ($currentPage=='facultyVouchers.php')?'active':''; ?>">Faculty Rewards Hub</a></li>
            <li><a href="facultyOrders.php" class="<?php echo ($currentPage=='facultyOrders.php')?'active':''; ?>">My Order History</a></li>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php" class="<?php echo ($currentPage=='dashboard.php')?'active':''; ?>">Business Dashboard</a></li>
            <li><a href="users.php" class="<?php echo ($currentPage=='users.php')?'active':''; ?>">Manage Students</a></li>
            <li><a href="vouchers.php" class="<?php echo ($currentPage=='vouchers.php')?'active':''; ?>">Voucher Promotions</a></li>
            <li><a href="orders.php" class="<?php echo ($currentPage=='orders.php')?'active':''; ?>">Manage Orders</a></li>
            <li><a href="managePolls.php" class="<?php echo ($currentPage=='managePolls.php')?'active':''; ?>">Voting Engine</a></li>
            <li><a href="menuManager.php" class="<?php echo ($currentPage=='menuManager.php')?'active':''; ?>">Manage Live Menu</a></li>
            <li><a href="prediction.php" class="<?php echo ($currentPage=='prediction.php')?'active':''; ?>">AI & ML Pipelines</a></li>
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
                <div class="badge-group">
                    <span class="badge" style="background:#FFF5E6; color:#D4A373; border: 1px solid #D4A373;">🌟 <span id="nav-points"><?php echo $loyalty_points; ?></span> Pts</span>
                    <span class="badge" style="background:#F0F5E4; color:#6B8E23; border: 1px solid #6B8E23;">💳 $<span id="nav-wallet"><?php echo number_format($wallet, 2); ?></span></span>
                </div>
                <?php endif; ?>
                
                <div class="user-dropdown-container">
                    <div class="user-avatar-btn">
                        <div class="avatar-circle"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                        <span class="avatar-text"><?php echo htmlspecialchars($username); ?> ▾</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0)" onclick="toggleChatbot()">🤖 Ask AI Assistant</a>
                        <a href="<?php echo ($role === 'user') ? 'myOrders.php' : (($role === 'staff') ? 'facultyOrders.php' : 'orders.php'); ?>">🧾 Order Ledger</a>
                        <div class="divider"></div>
                        <a href="javascript:void(0)" onclick="handleAction('logout')" class="logout-link">🚪 Disconnect Profile</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
