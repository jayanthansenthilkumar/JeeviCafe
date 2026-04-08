<?php
require 'includes/header.php';
if ($role !== 'admin') die("Access Denied");

$menuItems = $conn->query("SELECT * FROM menu");
?>
<div style="display: flex; gap: 40px;">
    
    <div class="form-container" style="flex: 1; margin: 0; align-self: flex-start; max-width: 350px;">
        <h3 style="margin-bottom: 20px; color: #333;">Add New Menu Item</h3>
        <form class="ajax-form" data-action="add_food">
            <div class="form-group">
                <label>Food Name</label>
                <input type="text" name="food_name" required>
            </div>
            <div class="form-group">
                <label>Dietary Restrictions</label>
                <select name="diet_type">
                    <option value="Veg">Vegetarian (Veg)</option>
                    <option value="Non-Veg">Meat (Non-Veg)</option>
                    <option value="Vegan">Vegan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Add to Menu</button>
        </form>
    </div>

    <!-- View Menu Items -->
    <div style="flex: 2;">
        <h3 style="margin-bottom: 20px;">Live Stock Management</h3>
        <p style="font-size:14px; margin-bottom:15px; color:#4a5568;">Kitchen Staff can flag items Out Of Stock if ingredients are missing.</p>
        <table>
            <thead>
                <tr>
                    <th>Item / Diet</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $menuItems->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($row['food_name']); ?><br>
                        <?php 
                            $diet = $row['diet_type'] ?? 'Veg'; 
                            $clr = ($diet === 'Veg' || $diet === 'Vegan') ? '#38a169' : '#e53e3e';
                        ?>
                        <span style="font-size:11px; font-weight:bold; color:<?php echo $clr; ?>;">[<?php echo htmlspecialchars($diet); ?>]</span>
                    </td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <?php echo $row['is_available'] ? '<span class="badge badge-completed">Available</span>' : '<span class="badge" style="background-color: #cbd5e0; color: #4a5568;">Out of Stock</span>'; ?>
                    </td>
                    <td>
                        <button onclick="handleAction('toggle_food', <?php echo $row['id']; ?>)" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #4a5568;">Toggle Stock</button>
                        <button onclick="confirmAction('delete_food', <?php echo $row['id']; ?>, 'Delete item permanently?')" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #e53e3e; margin-left: 5px;">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($menuItems->num_rows == 0): ?>
                <tr><td colspan="4">No items currently exist. Add some to the left!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
