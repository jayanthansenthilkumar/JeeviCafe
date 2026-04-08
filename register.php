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
<title>Register - Jeevi's Cafe</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js" defer></script>
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2>Create Profile</h2>
        <form class="ajax-form" data-action="register">
            <div class="form-group" style="text-align:left;">
                <label>Official Identification Number (ID)</label>
                <input type="text" name="register_number" placeholder="Registration # / ID Badge #" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <label>System Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group" style="text-align:left;">
                <label>Access Profile Type</label>
                <select name="role" required>
                    <option value="user">College Student</option>
                    <option value="staff">College Faculty / Staff</option>
                </select>
            </div>
            <button type="submit" class="btn" style="background-color: #38a169; width: 100%;">Create Secure Profile</button>
        </form>
        <p style="margin-top: 15px;">Already have an account? <a href="login.php" style="color: #2b6cb0;">Login here</a></p>
    </div>
</div>
</body>
</html>
