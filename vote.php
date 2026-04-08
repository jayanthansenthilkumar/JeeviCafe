<?php
require 'includes/header.php';
if ($role !== 'user') die("Access Denied");

$polls = $conn->query("SELECT * FROM menu_polls WHERE is_active=1");
$has_voted = $conn->query("SELECT * FROM user_votes WHERE user_id=$user_id")->num_rows > 0;
?>
<div class="form-container" style="max-width: 650px;">
    <h2 style="margin-bottom: 20px; color: #2b6cb0;">Next Week's Special Additions</h2>
    <p style="margin-bottom: 20px; color: #4a5568;">Help decide our culinary future! Vote for the item you want to see permanently added to Jeevi's Cafe next week. <br><strong style="color:#dd6b20">Reward: 15 Jeevi Points!</strong></p>
    
    <div style="display:flex; flex-direction:column; gap:15px;">
        <?php while ($row = $polls->fetch_assoc()): ?>
        <div style="border:1px solid #cbd5e0; padding:15px; border-radius:8px; display:flex; justify-content:space-between; background:#fff; align-items: center;">
            <div>
                <h3 style="color:#2d3748; margin-bottom: 5px;"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                <p style="color:#718096; font-size:14px;">Current Popularity: <strong style="color:#dd6b20; font-size: 16px;"><?php echo $row['votes']; ?> Votes</strong></p>
            </div>
            <?php if(!$has_voted): ?>
                <button onclick="handleAction('vote', <?php echo $row['id']; ?>)" class="btn" style="width:140px; text-align: center;">VOTE NOW (+15)</button>
            <?php else: ?>
                <span class="badge badge-completed" style="height:25px;">Vote Tallied</span>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php if($polls->num_rows == 0): ?>
            <p style="text-align:center; padding: 20px; background: whitesmoke; border-radius: 8px;">No active polls running at the moment.</p>
        <?php endif; ?>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
