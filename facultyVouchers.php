<?php 
require 'includes/header.php'; 
if ($role !== 'staff') die("Access Denied");

// Dynamically fetch available global drops specifically authorized for THIS demographics role (user or staff)
$vouchers = $conn->query("SELECT * FROM vouchers WHERE is_active=1 AND target_role='$role' ORDER BY id DESC");

// Get already verified redemptions for this account
$claimed = [];
$cQ = $conn->query("SELECT voucher_id FROM user_vouchers WHERE user_id=$user_id");
while($cR = $cQ->fetch_assoc()) $claimed[] = $cR['voucher_id'];

?>
<h2 style="margin-bottom: 20px;">Active Live Promotions</h2>
<p style="margin-bottom: 25px; color: #4a5568; max-width:800px; line-height:1.6;">
    Browse directly through Admins' actively issued Promo Coupons below! You can claim Virtual Wallet Credits by single-clicking `Redeem` before the global supply limits are completely exhausted! 
    <?php if($role === 'staff'): ?>
    <br><strong style="color:#e53e3e;">FACULTY OBLIGATION WARNING:</strong> College Faculty accounts are limited to redeeming one promotional coupon sign-on bonus as per university policy!
    <?php endif; ?>
</p>

<div style="display:flex; flex-wrap:wrap; gap:20px;">
    <?php while ($row = $vouchers->fetch_assoc()): ?>
    <?php $is_claimed = in_array($row['id'], $claimed); ?>
    
    <div class="form-container" style="margin:0; width: 300px; display:flex; flex-direction:column; background-color: #f7fafc; border: 1px solid #e2e8f0; border-top: 5px solid #2b6cb0;">
        <h3 style="color:#2b6cb0; text-align:center; font-size:24px; letter-spacing:2px; margin-top:10px; text-transform: uppercase;">
            <?php echo htmlspecialchars($row['code']); ?>
        </h3>
        <p style="text-align:center; font-size:32px; font-weight:bold; color:#38a169; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); margin: 15px 0;">
            +$<?php echo number_format($row['discount_value'], 2); ?>
        </p>
        <p style="text-align:center; font-size:12px; color:#718096; margin-bottom:20px;">
            Global Limit Used: <strong><?php echo $row['current_uses']; ?> / <?php echo $row['max_uses']; ?></strong>
        </p>
        
        <?php if($is_claimed): ?>
            <span class="badge badge-completed" style="text-align:center; background:#a0aec0; padding:12px; font-size: 14px; border-radius: 4px;">Already Redeemed by You</span>
        <?php else: ?>
            <button onclick="handleAction('redeem_voucher', '<?php echo htmlspecialchars($row['code']); ?>')" class="btn" style="background:#2b6cb0; font-size: 14px; padding: 12px; font-weight: bold; border-radius: 4px; box-shadow: 0 4px 6px rgba(43,108,176, 0.2);">
                💰 INSTANT CLAIM TO WALLET
            </button>
        <?php endif; ?>
    </div>
    
    <?php endwhile; ?>
    
    <?php if($vouchers->num_rows == 0): ?>
    <div style="padding: 40px; width:100%; border: 2px dashed #cbd5e0; text-align: center; color: #718096; background: #fff; border-radius: 8px;">
        <h3>No Drops Currently Active...</h3>
        <p>The Administration hasn't broadcasted any live promotions right now. Check back later!</p>
    </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
