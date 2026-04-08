<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$orders = $conn->query("SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY order_date DESC");
?>
<h2 style="margin-bottom: 20px;">Kitchen Display System (Live)</h2>
<table>
    <thead>
        <tr>
            <th>Receipt #</th>
            <th>Requested Meal</th>
            <th>Pickup Scheduled</th>
            <th>Total Value</th>
            <th>Status</th>
            <th>Kitchen Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $orders->fetch_assoc()): ?>
        <tr>
            <td>
                <?php echo $row['id']; ?><br>
                <span style="font-size:12px; color:#2b6cb0; font-weight:bold;">@<?php echo htmlspecialchars($row['username'] ?? 'System Guest'); ?></span>
            </td>
            <td style="font-weight: bold;">
                <?php echo htmlspecialchars($row['food_name']); ?><br>
                <span style="font-weight:normal; font-size:12px; color:#718096;">Qty: <?php echo $row['quantity']; ?></span>
            </td>
            <td>
                <?php if($row['pickup_time'] === 'ASAP'): ?>
                    <span class="badge" style="background-color: #e53e3e;">🔥 ASAP</span>
                <?php else: ?>
                    <span class="badge" style="background-color: #2b6cb0;">⏱️ <?php echo htmlspecialchars($row['pickup_time']); ?></span>
                <?php endif; ?>
            </td>
            <td style="color:#38a169; font-weight:bold;">$<?php echo number_format($row['total_price'], 2); ?></td>
            <td>
                <?php if ($row['status'] == 'completed'): ?>
                    <span class="badge badge-completed">Served</span>
                <?php elseif($row['status'] == 'cancelled'): ?>
                    <span class="badge" style="background:#e53e3e;">Refunded</span>
                <?php else: ?>
                    <span class="badge badge-pending">Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                <button onclick="handleAction('serve_order', <?php echo $row['id']; ?>)" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #38a169;">✅ Serve</button>
                <button onclick="confirmAction('cancel_order', <?php echo $row['id']; ?>, 'Kitchen Reject order and refund student wallet?')" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #e53e3e; margin-left: 5px;">❌ Reject</button>
                <?php else: ?>
                <span style="color: #a0aec0; font-size: 12px;">Locked</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if($orders->num_rows == 0): ?>
        <tr><td colspan="6" style="text-align: center; padding: 20px;">No incoming orders!</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>
