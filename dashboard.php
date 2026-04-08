<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$totalOrdersQ = $conn->query("SELECT COUNT(*) AS total FROM orders");
$totalOrders = $totalOrdersQ->fetch_assoc()['total'];

$popularFoodQ = $conn->query("SELECT food_name, SUM(quantity) as total_qty FROM orders GROUP BY food_name ORDER BY total_qty DESC LIMIT 1");
$popularFood = ($popularFoodRow = $popularFoodQ->fetch_assoc()) ? $popularFoodRow['food_name'] : 'N/A';

$ratingQ = $conn->query("SELECT AVG(rating) as avg_r FROM feedback");
$avgRating = ($rRow = $ratingQ->fetch_assoc()) && $rRow['avg_r'] ? round($rRow['avg_r'], 1) . ' ⭐' : 'N/A';

$revQ = $conn->query("SELECT SUM(total_price) as rev FROM orders WHERE status='completed'");
$rev = ($rRow = $revQ->fetch_assoc()) && $rRow['rev'] ? '$' . number_format($rRow['rev'], 2) : '$0.00';
?>
<h2 style="margin-bottom: 20px;">Business Dashboard</h2>
<div class="card-container">
    <div class="card">
        <h3>Total Volume Handled</h3>
        <p><?php echo $totalOrders; ?></p>
    </div>
    <div class="card" style="border-top: 4px solid #38a169;">
        <h3>Gross Safe Revenue</h3>
        <p style="color: #38a169;"><?php echo $rev; ?></p>
    </div>
    <div class="card">
        <h3>Global Student Rating</h3>
        <p><?php echo $avgRating; ?></p>
    </div>
</div>

<h2 style="margin-bottom: 20px;">Administrative Actions</h2>
<div style="display: flex; gap: 10px;">
    <button onclick="window.location.href='users.php'" class="btn" style="width: 200px;">Assign Stipends (Wallets)</button>
    <button onclick="window.location.href='menu_manager.php'" class="btn" style="width: 200px; background-color: #4a5568;">Manage Foods</button>
    <button onclick="handleAction('run_prediction')" class="btn" style="width: 200px; background-color: #38a169;">Run Analytics Engine</button>
</div>
<?php require 'includes/footer.php'; ?>
