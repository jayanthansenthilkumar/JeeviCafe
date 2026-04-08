<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$vouchers = $conn->query("SELECT * FROM vouchers ORDER BY id DESC");
?>
<h2 style="margin-bottom: 20px;">Economy & Voucher Drops</h2>
<div style="display:flex; gap:30px;">
    <div class="form-container" style="flex:1; max-width: 350px; margin:0; align-self: flex-start;">
        <h3 style="margin-bottom: 20px;">Generate Promo Code</h3>
        <form class="ajax-form" data-action="create_voucher">
            <div class="form-group">
                <label>Security Code</label>
                <input type="text" name="code" placeholder="e.g. SUMMER50" style="text-transform:uppercase" required>
            </div>
            <div class="form-group">
                <label>Credit Reward Value ($)</label>
                <input type="number" step="0.50" name="discount_value" placeholder="10.00" required>
            </div>
            <div class="form-group">
                <label>Global Claim Limit (Accounts)</label>
                <input type="number" name="max_uses" placeholder="50" required>
            </div>
            <button type="submit" class="btn" style="width: 100%; background-color:#38a169;">Deploy Code to Server</button>
        </form>
    </div>

    <div style="flex:2;">
        <h3>Live Voucher Database</h3>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Access Code</th>
                    <th>Reward</th>
                    <th>Claims / Limits</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $vouchers->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight:bold; letter-spacing:1px;"><?php echo htmlspecialchars($row['code']); ?></td>
                    <td style="color:#38a169; font-weight:bold;">+$<?php echo number_format($row['discount_value'], 2); ?></td>
                    <td><?php echo $row['current_uses']; ?> / <?php echo $row['max_uses']; ?></td>
                    <td>
                        <?php if($row['is_active']): ?>
                            <span class="badge badge-completed" style="background:#2b6cb0;">Active</span>
                        <?php else: ?>
                            <span class="badge" style="background:#e53e3e;">Revoked / Exhausted</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['is_active']): ?>
                            <button onclick="confirmAction('revoke_voucher', <?php echo $row['id']; ?>, 'Immediately invalidate this code globally?')" class="btn" style="background:#e53e3e; font-size:11px; padding:4px 8px;">Revoke</button>
                        <?php else: ?>
                            <span style="font-size:11px; color:#a0aec0;">Locked</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($vouchers->num_rows == 0): ?>
                    <tr><td colspan="5" style="text-align:center;">No promo codes generated in the system.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
