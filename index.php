<?php 
require 'includes/header.php'; 

$menu = $conn->query("SELECT * FROM menu WHERE is_available = 1");
?>
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
                        <?php echo htmlspecialchars($row['food_name']); ?> - $<?php echo $row['price']; ?>
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
        
        <!-- Voucher Redemption Block -->
        <div class="form-container" style="margin:0; background:#f0f4f8; border: 2px solid #2b6cb0;">
            <h3 style="margin-bottom: 10px; color: #2b6cb0;">Redeem Rewards Code</h3>
            <p style="font-size:12px; color:#4a5568; margin-bottom:15px;">Input an Admin Promo Code for instant Virtual Vault Cash injections!</p>
            <form class="ajax-form" data-action="redeem_voucher" style="display:flex; gap:10px;">
                <input type="text" name="code" placeholder="PROMO CODE" style="text-transform:uppercase; flex:1;" required>
                <button type="submit" class="btn" style="background:#2b6cb0;">Claim</button>
            </form>
        </div>
        
        <!-- AI Roulette "Surprise Me" Block -->
        <div class="form-container" style="margin:0; background:#fff5f5; border: 2px solid #e53e3e; text-align:center;">
            <h3 style="margin-bottom: 10px; color: #e53e3e;">Feeling Lucky? 🎰</h3>
            <p style="font-size:12px; color:#4a5568; margin-bottom:15px;">Can't decide what to eat? Let our Engine randomly fulfill an order from the available menu securely from your vault.</p>
            <button onclick="confirmAction('surprise_me', null, 'Deduct funds and authorize AI to randomly pick your meal?')" class="btn" style="background:#e53e3e; width:100%;">Surprise Me!</button>
        </div>
        
    </div>
</div>
<?php require 'includes/footer.php'; ?>
