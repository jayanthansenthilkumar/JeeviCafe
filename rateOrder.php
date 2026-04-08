<?php
require 'includes/header.php';
if ($role !== 'user') die("Access Denied");

if (!isset($_GET['id'])) header("Location: myOrders.php");
$order_id = intval($_GET['id']);

$check = $conn->query("SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id AND status='completed' AND is_rated=0");
if ($check->num_rows == 0) {
    echo "<h3>Invalid order or already rated.</h3>";
    require 'includes/footer.php';
    exit();
}
$order = $check->fetch_assoc();
?>
<div class="form-container">
    <h3 style="margin-bottom: 20px;">Rate Your <?php echo htmlspecialchars($order['food_name']); ?></h3>
    <form class="ajax-form" data-action="rate_order">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <div class="form-group">
            <label>Star Rating</label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                <option value="3">⭐⭐⭐ (3/5)</option>
                <option value="2">⭐⭐ (2/5)</option>
                <option value="1">⭐ (1/5)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Comments (Optional)</label>
            <textarea name="review" rows="4" style="width: 100%; border: 1px solid #cbd5e0; border-radius: 4px; padding: 10px;"></textarea>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Submit Feedback</button>
    </form>
</div>
<?php require 'includes/footer.php'; ?>
