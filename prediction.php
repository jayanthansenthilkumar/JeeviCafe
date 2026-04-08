<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$predictions = $conn->query("SELECT * FROM predictions ORDER BY predicted_qty DESC");
?>
<h2 style="margin-bottom: 20px;">AI Demand & Kitchen Logistics</h2>
<table>
    <thead>
        <tr>
            <th>Food Item</th>
            <th>Predicted Units</th>
            <th>Demand Category</th>
            <th>Kitchen Peak Prep-Time</th>
            <th>Last Computed Data</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $predictions->fetch_assoc()): ?>
        <tr>
            <td style="font-weight: bold;"><?php echo htmlspecialchars($row['food_name']); ?></td>
            <td style="color: #2b6cb0; font-weight: bold;"><?php echo round($row['predicted_qty'], 1); ?> Units</td>
            <td>
                <?php 
                $cat = $row['demand_category'];
                if($cat == 'High Demand') echo "<span class='badge' style='background-color: #e53e3e;'>High Danger</span>";
                elseif($cat == 'Medium Demand') echo "<span class='badge' style='background-color: #dd6b20;'>Medium</span>";
                else echo "<span class='badge' style='background-color: #38a169;'>Low</span>";
                ?>
            </td>
            <td style="font-weight: bold; color: #4a5568;">
                ⏱️ <?php echo htmlspecialchars($row['peak_hour']); ?>
            </td>
            <td style="font-size: 13px; color: #718096;"><?php echo $row['prediction_date']; ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if($predictions->num_rows == 0): ?>
        <tr><td colspan="5" style="text-align: center; padding: 20px;">AI Model has not been executed yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>
