<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Hardcoded credentials
    $valid_username = 'muneeb';
    $valid_password = '1409';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | ScholarSphere</title>
  <style>
    body {
      background: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 320px;
    }
    .login-box h2 {
      text-align: center;
      color: #2563eb;
      margin-bottom: 24px;
    }
    .login-box input {
      width: 100%;
      padding: 12px;
      margin-bottom: 16px;
      border: 2px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }
    .login-box button {
      width: 100%;
      padding: 12px;
      background: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
    }
    .login-box button:hover {
      background: #1d4ed8;
    }
    .error {
      color: #dc2626;
      text-align: center;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required autofocus>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Log In</button>
    </form>
  </div>
</body>
</html>
