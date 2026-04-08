<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Jeevi's Cafe</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js" defer></script>
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2>Jeevi's Cafe Login</h2>
        <form class="ajax-form" data-action="login">
            <div class="form-group" style="text-align:left;">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        <p style="margin-top: 15px;">New user? <a href="register.php" style="color: #2b6cb0;">Register here</a></p>
    </div>
</div>
</body>
</html>
