<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$polls = $conn->query("SELECT * FROM menu_polls WHERE is_active=1 ORDER BY votes DESC");
?>
<h2 style="margin-bottom: 20px;">Democratic Voting Engine</h2>
<div style="display:flex; gap:30px;">
    <div class="form-container" style="flex:1; max-width: 400px; margin:0; align-self: flex-start;">
        <h3 style="margin-bottom: 20px;">Nominate Item for Voting</h3>
        <form class="ajax-form" data-action="add_poll">
            <div class="form-group">
                <label>Proposed New Food Item</label>
                <input type="text" name="item_name" placeholder="e.g. Sushi Rolls" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Add candidate to Poll</button>
        </form>
        <br><hr style="border: 0; border-top: 1px solid #cbd5e0;"><br>
        <p style="font-size:14px; color:#718096; margin-bottom:15px; line-height: 1.5;">Concluding the poll will take the highest-voted candidate, securely inject it into the Main Live Food Menu, and reset the engine completely.</p>
        <button onclick="confirmAction('end_poll', null, 'Conclude poll and deploy winner to the Canteen?')" class="btn" style="background:#e53e3e; text-align:center; display:block; width:100%;">Conclude & Push Winner</button>
    </div>

    <div style="flex:2;">
        <h3>Live Analytics Board</h3>
        <br>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <?php while ($row = $polls->fetch_assoc()): ?>
            <div style="background:#fff; border:1px solid #cbd5e0; padding:20px; border-radius:8px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin-bottom:5px; color:#2d3748;"><?php echo htmlspecialchars($row['item_name']); ?></h3>
                    <p>Current Verified Tally: <strong style="color:#2b6cb0; font-size:22px;"><?php echo $row['votes']; ?></strong></p>
                </div>
                <button onclick="confirmAction('delete_poll', <?php echo $row['id']; ?>, 'Withdraw this candidate?')" class="btn" style="background:#e53e3e; font-size: 11px; padding: 5px 10px;">Withdraw Candidate</button>
            </div>
            <?php endwhile; ?>
            <?php if($polls->num_rows == 0): ?>
                <div style="padding: 20px; border: 1px dashed #cbd5e0; border-radius: 8px; text-align: center; color: #718096;">
                    No candidates nominated yet. Add one via the form.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
