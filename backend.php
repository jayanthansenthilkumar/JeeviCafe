<?php
session_start();
require 'includes/db.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$role = trim(strtolower($_SESSION['role'] ?? 'guest'));
$is_admin = ($role === 'admin');
$is_staff = ($role === 'staff');

if (!$user_id && !in_array($action, ['login', 'register'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$response = ['status' => 'error', 'message' => 'Invalid or missing action'];

try {
    switch ($action) {
        case 'login':
            $username = $_POST['username'];
            $password = $_POST['password'];
            $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                if ($password === $row['password']) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $row['role'];
                    
                    $redirect = "index.php"; // Default for students
                    if ($row['role'] === 'staff') $redirect = "facultyIndex.php";
                    if ($row['role'] === 'admin') $redirect = "dashboard.php";
                    
                    $response = ['status' => 'success', 'redirect' => $redirect];
                } else {
                    $response['message'] = "Invalid password.";
                }
            } else {
                $response['message'] = "User not found.";
            }
            break;

        case 'register':
            $reg_num = $_POST['register_number'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $reg_role = ($_POST['role'] === 'staff') ? 'staff' : 'user';
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, register_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $password, $reg_role, $reg_num);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Profile created natively!', 'redirect' => 'login.php'];
            } else {
                $response['message'] = "Username or Reg Number already securely exists.";
            }
            break;

        case 'logout':
            session_destroy();
            $response = ['status' => 'success', 'redirect' => 'login.php'];
            break;

        case 'place_order':
            if (!in_array($role, ['user', 'staff'])) throw new Exception("Access Denied");
            $food_name = $_POST['food_name'];
            $quantity = $_POST['quantity'];
            $pickup_time = $_POST['pickup_time'];
            
            $mQ = $conn->prepare("SELECT price FROM menu WHERE food_name = ?");
            $mQ->bind_param("s", $food_name);
            $mQ->execute();
            $mRow = $mQ->get_result()->fetch_assoc();
            
            if (!$mRow) throw new Exception("Invalid item");
            $cost = $mRow['price'] * $quantity;
            $points = floor($cost * 10);
            
            $uQ = $conn->query("SELECT wallet FROM users WHERE id=$user_id");
            if ($uQ->fetch_assoc()['wallet'] < $cost) throw new Exception("Insufficient Funds in Wallet");
            
            $conn->query("UPDATE users SET wallet = wallet - $cost, loyalty_points = loyalty_points + $points WHERE id=$user_id");
            $stmt = $conn->prepare("INSERT INTO orders (user_id, food_name, pickup_time, quantity, status, total_price) VALUES (?, ?, ?, ?, 'pending', ?)");
            $stmt->bind_param("issid", $user_id, $food_name, $pickup_time, $quantity, $cost);
            $stmt->execute();
            $response = ['status' => 'success', 'message' => "Ordered! \$$cost deducted, $points points earned!"];
            break;

        case 'surprise_me':
            if (!in_array($role, ['user', 'staff'])) throw new Exception("Denied");
            $rand = $conn->query("SELECT *, RAND() as rnd FROM menu WHERE is_available=1 ORDER BY rnd LIMIT 1");
            if($row = $rand->fetch_assoc()) {
                $food_name = $row['food_name'];
                $cost = $row['price'];
                $points = floor($cost * 10);
                
                $uQ = $conn->query("SELECT wallet FROM users WHERE id=$user_id");
                if ($uQ->fetch_assoc()['wallet'] < $cost) throw new Exception("Insufficient Vault Funds for an AI Surprise ($$cost).");
                
                $conn->query("UPDATE users SET wallet = wallet - $cost, loyalty_points = loyalty_points + $points WHERE id=$user_id");
                $stmt = $conn->prepare("INSERT INTO orders (user_id, food_name, pickup_time, quantity, status, total_price) VALUES (?, ?, 'ASAP', 1, 'pending', ?)");
                $stmt->bind_param("isd", $user_id, $food_name, $cost);
                $stmt->execute();
                
                $response = ['status' => 'success', 'message' => "🎰 AI Picked: $food_name !\r\n \$$cost deducted, $points points earned!"];
            } else {
                throw new Exception("Menu is empty.");
            }
            break;

        case 'redeem_voucher':
            if (!in_array($role, ['user', 'staff'])) throw new Exception("Denied. Managerial Accounts cannot claim discounts.");
            $code = strtoupper(trim($_POST['code'] ?? $_POST['id'] ?? ''));
            
            $vQ = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND is_active=1");
            $vQ->bind_param("s", $code);
            $vQ->execute();
            $v = $vQ->get_result()->fetch_assoc();
            
            if(!$v) throw new Exception("Invalid or expired promotional code.");
            
            // Critical Role Segmentation!
            if ($v['target_role'] !== $role) {
                throw new Exception("Security Alert: This promotional code is geographically locked to the " . strtoupper($v['target_role']) . " demographic. You cannot claim it!");
            }
            
            // Explicit limitation for staff only, as requested.
            if ($role === 'staff') {
                $staffCheck = $conn->query("SELECT * FROM user_vouchers WHERE user_id=$user_id");
                if ($staffCheck->num_rows > 0) {
                    throw new Exception("Policy Enforcement: Kitchen Staff accounts are strictly limited to redeeming exactly one (1) lifetime bonus voucher.");
                }
            }

            if($v['current_uses'] >= $v['max_uses']) throw new Exception("Voucher has reached full usage capacity globally.");
            
            $vid = $v['id'];
            $check = $conn->query("SELECT * FROM user_vouchers WHERE user_id=$user_id AND voucher_id=$vid");
            if($check->num_rows > 0) throw new Exception("You have already claimed this specific voucher on your account.");
            
            $val = $v['discount_value'];
            $conn->query("INSERT INTO user_vouchers (user_id, voucher_id) VALUES ($user_id, $vid)");
            $conn->query("UPDATE vouchers SET current_uses = current_uses + 1 WHERE id=$vid");
            $conn->query("UPDATE users SET wallet = wallet + $val WHERE id=$user_id");
            
            if($v['current_uses'] + 1 >= $v['max_uses']) {
                $conn->query("UPDATE vouchers SET is_active=0 WHERE id=$vid");
            }
            
            $response = ['status' => 'success', 'message' => "ACCEPTED! \$$val added securely to your Virtual Vault."];
            break;

        case 'create_voucher':
            if (!$is_admin) throw new Exception("Denied");
            $code = strtoupper(preg_replace('/\s+/', '', $_POST['code']));
            $value = floatval($_POST['discount_value']);
            $limit = intval($_POST['max_uses']);
            $target = $_POST['target_role'];
            
            $stmt = $conn->prepare("INSERT INTO vouchers (code, discount_value, max_uses, target_role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdis", $code, $value, $limit, $target);
            if($stmt->execute()) {
                $response = ['status' => 'success', 'message' => "Global Drop: Voucher $code was generated for $target!"];
            } else {
                throw new Exception("Code already exists in the system.");
            }
            break;
            
        case 'revoke_voucher':
            if (!$is_admin) throw new Exception("Denied");
            $id = intval($_POST['id']);
            $conn->query("UPDATE vouchers SET is_active=0 WHERE id=$id");
            $response = ['status' => 'success'];
            break;

        case 'cancel_order':
            $id = intval($_POST['id']);
            $hasAuth = ($is_admin || $is_staff) ? "1=1" : "user_id=$user_id";
            $checkQ = $conn->query("SELECT * FROM orders WHERE id=$id AND status='pending' AND $hasAuth");
            if ($checkQ->num_rows > 0) {
                $order = $checkQ->fetch_assoc();
                $refund = $order['total_price'];
                $buyer_id = $order['user_id'];
                $conn->query("UPDATE orders SET status='cancelled' WHERE id=$id");
                $conn->query("UPDATE users SET wallet = wallet + $refund WHERE id=$buyer_id");
                $response = ['status' => 'success', 'message' => 'Order Cancelled & Refunded'];
            } else {
                throw new Exception("Cannot process rejection on this order.");
            }
            break;

        case 'serve_order':
            if (!$is_admin) throw new Exception("Denied");
            $id = intval($_POST['id']);
            $conn->query("UPDATE orders SET status='completed' WHERE id=$id");
            $response = ['status' => 'success', 'message' => 'Marked as Served'];
            break;

        case 'add_food':
            if (!$is_admin) throw new Exception("Denied");
            $stmt = $conn->prepare("INSERT INTO menu (food_name, price, diet_type) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $_POST['food_name'], $_POST['price'], $_POST['diet_type']);
            $stmt->execute();
            $response = ['status' => 'success'];
            break;

        case 'toggle_food':
            if (!$is_admin) throw new Exception("Denied");
            $id = intval($_POST['id']);
            $conn->query("UPDATE menu SET is_available = NOT is_available WHERE id=$id");
            $response = ['status' => 'success'];
            break;

        case 'delete_food':
            if (!$is_admin) throw new Exception("Denied");
            $id = intval($_POST['id']);
            $conn->query("DELETE FROM menu WHERE id=$id");
            $response = ['status' => 'success'];
            break;

        case 'add_funds':
            if (!$is_admin) throw new Exception("Denied");
            $uid = intval($_POST['id']);
            $conn->query("UPDATE users SET wallet = wallet + 50.00 WHERE id=$uid");
            $response = ['status' => 'success'];
            break;

        case 'add_poll':
            if (!$is_admin) throw new Exception("Denied");
            $stmt = $conn->prepare("INSERT INTO menu_polls (item_name) VALUES (?)");
            $stmt->bind_param("s", $_POST['item_name']);
            $stmt->execute();
            $response = ['status' => 'success'];
            break;

        case 'end_poll':
            if (!$is_admin) throw new Exception("Denied");
            $winnerQ = $conn->query("SELECT * FROM menu_polls WHERE is_active=1 ORDER BY votes DESC LIMIT 1");
            if ($w = $winnerQ->fetch_assoc()) {
                $win_name = $w['item_name'];
                $stmt = $conn->prepare("INSERT INTO menu (food_name, price, diet_type) VALUES (?, 6.50, 'Non-Veg')");
                $stmt->bind_param("s", $win_name);
                $stmt->execute();
                $conn->query("UPDATE menu_polls SET is_active=0");
                $conn->query("TRUNCATE TABLE user_votes");
                $response = ['status' => 'success', 'message' => "$win_name promoted to Live Menu!"];
            } else {
                $response = ['status' => 'error', 'message' => 'No polls active.'];
            }
            break;

        case 'delete_poll':
            if (!$is_admin) throw new Exception("Denied");
            $id = intval($_POST['id']);
            $conn->query("DELETE FROM menu_polls WHERE id=$id");
            $response = ['status' => 'success'];
            break;

        case 'vote':
            if (!in_array($role, ['user', 'staff'])) throw new Exception("Denied");
            $poll_id = intval($_POST['id']);
            $check = $conn->query("SELECT * FROM user_votes WHERE user_id=$user_id");
            if ($check->num_rows == 0) {
                $conn->query("INSERT INTO user_votes (user_id, poll_id) VALUES ($user_id, $poll_id)");
                $conn->query("UPDATE menu_polls SET votes = votes + 1 WHERE id=$poll_id");
                $conn->query("UPDATE users SET loyalty_points = loyalty_points + 15 WHERE id=$user_id");
                $response = ['status' => 'success', 'message' => 'Voted! Earned 15 Points.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Already voted.'];
            }
            break;

        case 'run_prediction':
            if (!$is_admin) throw new Exception("Denied");
            shell_exec(escapeshellcmd("python predict.py"));
            $response = ['status' => 'success', 'message' => 'AI Model Run Successfully'];
            break;
            
        case 'rate_order':
            if (!in_array($role, ['user', 'staff'])) throw new Exception("Denied");
            $order_id = intval($_POST['order_id']);
            $rating = intval($_POST['rating']);
            $review = $_POST['review'];
            $conn->query("UPDATE orders SET is_rated=1 WHERE id=$order_id");
            $stmt = $conn->prepare("INSERT INTO feedback (order_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $order_id, $user_id, $rating, $review);
            $stmt->execute();
            $response = ['status' => 'success', 'redirect' => ($role==='staff' ? 'facultyOrders.php' : 'myOrders.php')];
            break;
    }
} catch (Exception $e) {
    // Explicitly return JSON errors caught from manual Throw Exceptions above!
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
