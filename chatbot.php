<?php
session_start();
header('Content-Type: application/json');
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['reply' => 'Access Denied. Please log in contextually before consulting the AI.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$role = strtolower(trim($_SESSION['role'] ?? 'user'));
$message = strtolower(trim($_POST['message'] ?? ''));

$reply = "I'm sorry, I didn't understand that command. Try asking for the 'menu', your 'balance', or tell me to 'order [food name]'.";

if (empty($message)) {
    echo json_encode(['reply' => 'Please type a message.']);
    exit();
}

// Intent: Balance / Wallet / Points
if (strpos($message, 'balance') !== false || strpos($message, 'wallet') !== false || strpos($message, 'points') !== false) {
    $q = $conn->query("SELECT wallet, loyalty_points FROM users WHERE id=$user_id");
    $me = $q->fetch_assoc();
    $reply = "💳 Virtual Vault: <strong>$" . number_format($me['wallet'], 2) . "</strong>\n🌟 Loyalty Points: <strong>" . $me['loyalty_points'] . "</strong>";
}
// Intent: Read Menu
elseif (strpos($message, 'menu') !== false || strpos($message, 'food') !== false || strpos($message, 'eat') !== false) {
    $q = $conn->query("SELECT food_name, price, diet_type FROM menu WHERE is_available=1");
    if ($q->num_rows > 0) {
        $reply = "Here is the Live Menu right now:\n\n";
        while($r = $q->fetch_assoc()) {
            $reply .= "• " . $r['food_name'] . " [".$r['diet_type']."] - $" . number_format($r['price'], 2) . "\n";
        }
        $reply .= "\nTo buy something, just type 'Order [Item Name]'.";
    } else {
        $reply = "The kitchen currently has no items in stock!";
    }
}
// Intent: Surprise Me
elseif (strpos($message, 'surprise') !== false || strpos($message, 'random') !== false) {
    if (!in_array($role, ['user', 'staff'])) {
        $reply = "Administrators cannot place food orders.";
    } else {
        $rand = $conn->query("SELECT *, RAND() as rnd FROM menu WHERE is_available=1 ORDER BY rnd LIMIT 1");
        if ($row = $rand->fetch_assoc()) {
            $food_name = $row['food_name'];
            $cost = $row['price'];
            $points = floor($cost * 10);
            
            $uQ = $conn->query("SELECT wallet FROM users WHERE id=$user_id");
            if ($uQ->fetch_assoc()['wallet'] < $cost) {
                $reply = "Insufficient Vault Funds for an AI Surprise ($$cost).";
            } else {
                $conn->query("UPDATE users SET wallet = wallet - $cost, loyalty_points = loyalty_points + $points WHERE id=$user_id");
                $stmt = $conn->prepare("INSERT INTO orders (user_id, food_name, pickup_time, quantity, status, total_price) VALUES (?, ?, 'ASAP', 1, 'pending', ?)");
                $stmt->bind_param("isd", $user_id, $food_name, $cost);
                $stmt->execute();
                $reply = "🎰 AI Roulette Executed!\nI have securely ordered **$food_name** for you.\n$$cost was deducted and $points points earned.";
            }
        } else {
            $reply = "Surprise failed. Menu is empty.";
        }
    }
}
// Intent: Cancel Order
elseif (strpos($message, 'cancel') !== false) {
    $checkQ = $conn->query("SELECT * FROM orders WHERE user_id=$user_id AND status='pending' ORDER BY id DESC LIMIT 1");
    if ($checkQ->num_rows > 0) {
        $order = $checkQ->fetch_assoc();
        $id = $order['id'];
        $refund = $order['total_price'];
        $conn->query("UPDATE orders SET status='cancelled' WHERE id=$id");
        $conn->query("UPDATE users SET wallet = wallet + $refund WHERE id=$user_id");
        $reply = "✅ Action Authorized! I have forcefully cancelled Order #$id.\n$$refund has been instantly refunded to your Virtual Vault.";
    } else {
        $reply = "You do not have any pending orders capable of being cancelled.";
    }
}
// Intent: Order Food
elseif (strpos($message, 'order') !== false || strpos($message, 'buy') !== false || strpos($message, 'want') !== false) {
    if (!in_array($role, ['user', 'staff'])) {
        $reply = "System Administrators cannot route food orders to themselves.";
    } else {
        // Attempt to extract item and directly purchase
        $menuQ = $conn->query("SELECT food_name, price FROM menu WHERE is_available=1");
        $foundItem = null;
        while($item = $menuQ->fetch_assoc()) {
            if (strpos($message, strtolower($item['food_name'])) !== false) {
                $foundItem = $item;
                break;
            }
        }
        
        if ($foundItem) {
            $food_name = $foundItem['food_name'];
            $cost = $foundItem['price'];
            $points = floor($cost * 10);
            
            $uQ = $conn->query("SELECT wallet FROM users WHERE id=$user_id");
            if ($uQ->fetch_assoc()['wallet'] < $cost) {
                $reply = "Transaction Rejected: Insufficient Vault Funds to purchase $food_name ($$cost).";
            } else {
                $conn->query("UPDATE users SET wallet = wallet - $cost, loyalty_points = loyalty_points + $points WHERE id=$user_id");
                $stmt = $conn->prepare("INSERT INTO orders (user_id, food_name, pickup_time, quantity, status, total_price) VALUES (?, ?, 'ASAP', 1, 'pending', ?)");
                $stmt->bind_param("isd", $user_id, $food_name, $cost);
                $stmt->execute();
                $reply = "🍟 Order Accepted via Chat!\n1x $food_name has been sent directly to the Kitchen for ASAP pickup!\n$$cost deducted, $points points earned.";
            }
        } else {
            $reply = "I couldn't match any food item in your text. Please make sure the exact food name from the menu is in your message!";
        }
    }
}

echo json_encode(['reply' => $reply]);
?>
