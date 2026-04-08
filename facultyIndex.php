<?php 
require 'includes/header.php'; 
if ($role !== 'staff') die('<h2><center><br><br>Access Restricted. Staff / Faculty Only.</center></h2>');

// Live Tracking: See if the student is already waiting on food right now!
$liveQ = $conn->query("SELECT * FROM orders WHERE user_id=$user_id AND status='pending' ORDER BY id DESC LIMIT 1");
$isWaiting = ($liveQ && $liveQ->num_rows > 0);
if($isWaiting) $liveOrder = $liveQ->fetch_assoc();

$menu = $conn->query("SELECT * FROM menu WHERE is_available = 1");
?>

<?php if($isWaiting): ?>
<div style="background-color: #ebf8ff; border-left: 4px solid #2b6cb0; padding: 20px; margin-bottom: 25px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <h3 style="color:#2b6cb0; margin-bottom: 5px;">🔥 Live Tracking: Order #<?php echo $liveOrder['id']; ?> is Preparing!</h3>
    <p style="color:#4a5568; font-size:14px;">The Kitchen Staff is actively preparing your <strong><?php echo htmlspecialchars($liveOrder['food_name']); ?></strong> (Qty: <?php echo $liveOrder['quantity']; ?>). Please be ready for your <strong><?php echo htmlspecialchars($liveOrder['pickup_time']); ?></strong> pickup schedule.</p>
</div>
<?php endif; ?>

<div style="display:flex; gap:20px;">
    <!-- Core Standard Order Flow -->
    <div class="form-container" style="flex: 2; margin:0;">
        <h2 style="margin-bottom: 20px; color: #333;">Place Your Order</h2>
        <form class="ajax-form" data-action="place_order">
            <div class="form-group">
                <label for="food_name">Select Menu Item</label>
                <select id="food_name" name="food_name" required>
                    <option value="">-- Select Food --</option>
                    <?php while ($row = $menu->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['food_name']); ?>">
                        <?php echo htmlspecialchars($row['food_name']); ?> 
                        [<?php echo htmlspecialchars($row['diet_type'] ?? 'Veg'); ?>] 
                        - $<?php echo $row['price']; ?>
                    </option>
                    <?php endwhile; ?>
                    <?php if($menu->num_rows == 0): ?>
                    <option value="" disabled>No items available right now</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" max="20" required>
            </div>
            <div class="form-group">
                <label for="pickup_time">Pickup Time Schedule</label>
                <select id="pickup_time" name="pickup_time" required>
                    <option value="ASAP">Make it ASAP (Right Now)</option>
                    <option value="11:30 AM">11:30 AM Break</option>
                    <option value="12:00 PM">12:00 PM Lunch</option>
                    <option value="12:30 PM">12:30 PM Lunch</option>
                    <option value="01:00 PM">01:00 PM Lunch</option>
                    <option value="04:00 PM">04:00 PM Evening Snack</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Complete Order</button>
        </form>
    </div>
    
    <!-- Unique Interactive Micro-Features side column -->
    <div style="flex: 1; display:flex; flex-direction:column; gap:20px;">
        
        <!-- Removed Custom Redemption Block because it exists in the Dedicated Navbar Tab now -->
        
        <!-- AI Roulette "Surprise Me" Block -->
        <div class="form-container" style="margin:0; background:#fff5f5; border: 2px solid #e53e3e; text-align:center;">
            <h3 style="margin-bottom: 10px; color: #e53e3e;">Feeling Lucky? 🎰</h3>
            <p style="font-size:12px; color:#4a5568; margin-bottom:15px;">Can't decide what to eat? Let our AI randomly fulfill an order from the available menu securely.</p>
            <button onclick="confirmAction('surprise_me', null, 'Deduct funds and authorize AI to randomly pick your meal?')" class="btn" style="background:#e53e3e; width:100%;">Surprise Me!</button>
        </div>
        
    </div>
</div>
<?php require 'includes/footer.php'; ?>
