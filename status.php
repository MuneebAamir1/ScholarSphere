<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "ScholarSphere";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$status = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
$sql = "SELECT a.status, s.title 
        FROM applications a 
        JOIN students st ON a.student_id = st.id 
        JOIN scholarships s ON a.scholarship_id = s.id 
        WHERE st.email = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Check Application Status | ScholarSphere</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f4f8;
      color: #222;
      margin: 0;
      padding: 0;
    }
    .status-container {
      max-width: 800px;
      margin: 80px auto;
      padding: 40px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      display: flex;
      gap: 30px;
      align-items: center;
      animation: slideUp 1s ease;
    }
    .status-form {
      flex: 1;
    }
    .status-form h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #1a73e8;
    }
    .status-form input[type="email"] {
      width: 100%;
      padding: 12px;
      border: 2px solid #1a73e8;
      border-radius: 12px;
      margin-bottom: 20px;
      font-size: 16px;
    }
    .status-form button {
      padding: 12px 24px;
      background: #1a73e8;
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .status-form button:hover {
      background: #0c56d0;
    }
    .result-box {
      margin-top: 20px;
    }
    .result-box p {
      font-size: 16px;
      background: #e3f2fd;
      padding: 10px;
      border-left: 5px solid #1a73e8;
      border-radius: 8px;
    }
    .svg-img {
      width: 250px;
    }
    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    @media (max-width: 768px) {
      .status-container {
        flex-direction: column;
        text-align: center;
      }
      .svg-img {
        width: 180px;
      }
    }
  </style>
</head>
<body>

  <div class="status-container">
    <img src="status-illustration.svg" alt="Status Check" class="svg-img">
    <div class="status-form">
      <h2>🎓 Check Your Scholarship Application Status</h2>
      <form method="POST">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Check Status</button>
      </form>

      <div class="result-box">
        <?php if (isset($result) && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <p>📌 Scholarship: <strong><?= htmlspecialchars($row['title']) ?></strong><br>
            ✅ Status: <strong><?= htmlspecialchars($row['status']) ?></strong></p>
          <?php endwhile; ?>
        <?php elseif (isset($result)): ?>
          <p>❌ No applications found for this email.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

</body>
</html>
