<?php 
require 'includes/header.php'; 
if ($role !== 'staff') die("Access Denied");

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");
?>
<h2 style="margin-bottom: 20px;">My Order History</h2>
<table>
    <thead>
        <tr>
            <th>Order #</th>
            <th>Canteen Item</th>
            <th>Pickup Scheduled</th>
            <th>Receipt</th>
            <th>Logistics / Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $orders->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $row['id']; ?></td>
            <td style="font-weight:bold;">
                <?php echo htmlspecialchars($row['food_name']); ?>
                <br><span style="font-size:12px; color:#718096; font-weight:normal;">Qty: <?php echo $row['quantity']; ?></span>
            </td>
            <td>
                <?php if($row['pickup_time'] === 'ASAP'): ?>
                    <span class="badge" style="background-color: #dd6b20;">ASAP</span>
                <?php else: ?>
                    <span class="badge" style="background-color: #2b6cb0;">⏱️ <?php echo htmlspecialchars($row['pickup_time']); ?></span>
                <?php endif; ?>
            </td>
            <td style="color:#38a169; font-weight:bold;">$<?php echo number_format($row['total_price'], 2); ?></td>
            <td>
                <?php if ($row['status'] == 'completed'): ?>
                    <span class="badge badge-completed" style="margin-bottom:5px;">Food Served!</span><br>
                    <?php if(!$row['is_rated']): ?>
                        <a href="rateOrder.php?id=<?php echo $row['id']; ?>" class="btn" style="padding: 3px 6px; font-size: 11px; background-color: #dd6b20;">Leave Feedback</a>
                    <?php else: ?>
                        <span style="font-size: 11px; color:#718096">Rated ⭐</span>
                    <?php endif; ?>
                <?php elseif ($row['status'] == 'cancelled'): ?>
                    <span class="badge" style="background:#e53e3e;">Cancelled (Refunded)</span>
                <?php else: ?>
                    <span class="badge badge-pending" style="margin-bottom: 5px;">Currently Preparing</span><br>
                    <button onclick="confirmAction('cancel_order', <?php echo $row['id']; ?>, 'Cancel and instantly refund wallet?')" style="color: #e53e3e; font-size: 13px; font-weight:bold; background:none; border:none; cursor:pointer;">❌ Cancel Order</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if($orders->num_rows == 0): ?>
        <tr><td colspan="5" style="text-align: center; padding:20px;">You haven't placed any orders yet. Click "Place Order" to begin!</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>
