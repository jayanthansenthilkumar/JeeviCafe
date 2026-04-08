<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$u = $conn->query("SELECT * FROM users WHERE role='user'");
?>
<h2 style="margin-bottom: 20px;">Student Database & Wallets</h2>
<table>
    <thead>
        <tr>
            <th>Student Account ID</th>
            <th>Registered Username</th>
            <th>Available Vault Credit</th>
            <th>Administrative Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $u->fetch_assoc()): ?>
        <tr>
            <td>Account #<?php echo $row['id']; ?></td>
            <td style="font-weight:bold;"><?php echo htmlspecialchars($row['username']); ?></td>
            <td style="color:#38a169; font-weight:bold; font-size:18px;">$<?php echo number_format($row['wallet'], 2); ?></td>
            <td>
                <button onclick="handleAction('add_funds', <?php echo $row['id']; ?>)" class="btn" style="padding: 5px 10px; font-size:12px; background-color:#2b6cb0;">Add $50 Stipend</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require 'includes/footer.php'; ?>
