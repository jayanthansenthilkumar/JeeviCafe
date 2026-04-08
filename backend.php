<?php
session_start();
require 'includes/db.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'guest';
$is_admin = ($role === 'admin');

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
                    $response = ['status' => 'success', 'redirect' => ($row['role'] === 'admin' ? "dashboard.php" : "index.php")];
                } else {
                    $response['message'] = "Invalid password.";
                }
            } else {
                $response['message'] = "User not found.";
            }
            break;

        case 'register':
            $username = $_POST['username'];
            $password = $_POST['password'];
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Account created! Please login.', 'redirect' => 'login.php'];
            } else {
                $response['message'] = "Username already exists.";
            }
            break;

        case 'logout':
            session_destroy();
            $response = ['status' => 'success', 'redirect' => 'login.php'];
            break;

        case 'place_order':
            if ($role !== 'user') throw new Exception("Access Denied");
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
            if ($role !== 'user') throw new Exception("Denied");
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
            if ($role !== 'user') throw new Exception("Denied");
            $code = strtoupper(trim($_POST['code']));
            
            $vQ = $conn->prepare("SELECT * FROM vouchers WHERE code=? AND is_active=1");
            $vQ->bind_param("s", $code);
            $vQ->execute();
            $v = $vQ->get_result()->fetch_assoc();
            
            if(!$v) throw new Exception("Invalid or expired code.");
            if($v['current_uses'] >= $v['max_uses']) throw new Exception("Voucher has reached full usage capacity globally.");
            
            $vid = $v['id'];
            $check = $conn->query("SELECT * FROM user_vouchers WHERE user_id=$user_id AND voucher_id=$vid");
            if($check->num_rows > 0) throw new Exception("You have already claimed this voucher.");
            
            $val = $v['discount_value'];
            $conn->query("INSERT INTO user_vouchers (user_id, voucher_id) VALUES ($user_id, $vid)");
            $conn->query("UPDATE vouchers SET current_uses = current_uses + 1 WHERE id=$vid");
            $conn->query("UPDATE users SET wallet = wallet + $val WHERE id=$user_id");
            
            if($v['current_uses'] + 1 >= $v['max_uses']) {
                $conn->query("UPDATE vouchers SET is_active=0 WHERE id=$vid");
            }
            
            $response = ['status' => 'success', 'message' => "CODE ACCEPTED! \$$val added securely to your Virtual Vault."];
            break;

        case 'create_voucher':
            if (!$is_admin) throw new Exception("Denied");
            $code = strtoupper(preg_replace('/\s+/', '', $_POST['code']));
            $value = floatval($_POST['discount_value']);
            $limit = intval($_POST['max_uses']);
            
            $stmt = $conn->prepare("INSERT INTO vouchers (code, discount_value, max_uses) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $code, $value, $limit);
            if($stmt->execute()) {
                $response = ['status' => 'success', 'message' => "Global Drop: Voucher $code was generated!"];
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
            $checkQ = $conn->query($is_admin ? "SELECT * FROM orders WHERE id=$id AND status='pending'" : "SELECT * FROM orders WHERE id=$id AND user_id=$user_id AND status='pending'");
            if ($checkQ->num_rows > 0) {
                $order = $checkQ->fetch_assoc();
                $refund = $order['total_price'];
                $buyer_id = $order['user_id'];
                $conn->query("UPDATE orders SET status='cancelled' WHERE id=$id");
                $conn->query("UPDATE users SET wallet = wallet + $refund WHERE id=$buyer_id");
                $response = ['status' => 'success', 'message' => 'Order Cancelled & Refunded'];
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
            $stmt = $conn->prepare("INSERT INTO menu (food_name, price) VALUES (?, ?)");
            $stmt->bind_param("sd", $_POST['food_name'], $_POST['price']);
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
                $stmt = $conn->prepare("INSERT INTO menu (food_name, price) VALUES (?, 6.50)");
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
            if ($role !== 'user') throw new Exception("Denied");
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
            if ($role !== 'user') throw new Exception("Denied");
            $order_id = intval($_POST['order_id']);
            $rating = intval($_POST['rating']);
            $review = $_POST['review'];
            $conn->query("UPDATE orders SET is_rated=1 WHERE id=$order_id");
            $stmt = $conn->prepare("INSERT INTO feedback (order_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $order_id, $user_id, $rating, $review);
            $stmt->execute();
            $response = ['status' => 'success', 'redirect' => 'my_orders.php'];
            break;
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
