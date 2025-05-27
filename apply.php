<?php
include 'db.php';

$scholarship = null;
if (isset($_GET['scholarship_id'])) {
    $id = intval($_GET['scholarship_id']);
    $stmt = $conn->prepare("SELECT title FROM scholarships WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $scholarship = $res->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Apply - ScholarSphere</title>
<link rel="stylesheet" href="style.css" />
<style>
  /* Additional styles for apply form */
  body {
    background: #f0f4f8;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .apply-container {
    max-width: 700px;
    margin: 3rem auto;
    background: white;
    padding: 2rem 2.5rem;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgb(0 0 0 / 0.1);
    animation: fadeInUp 1s ease forwards;
    position: relative;
  }
  h2 {
    color: #155e75; /* primary dark teal */
    text-align: center;
    margin-bottom: 0.5rem;
  }
  .scholarship-title {
    color: #0e7490; /* medium teal */
    font-weight: 600;
    margin-bottom: 2rem;
    text-align: center;
  }
  form label {
    display: block;
    margin: 0.8rem 0 0.3rem 0;
    color: #0f172a; /* dark navy */
    font-weight: 600;
  }
  form input[type="text"],
  form input[type="email"],
  form input[type="tel"],
  form input[type="file"],
  form textarea {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 2px solid #38bdf8; /* sky-400 */
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }
  form input[type="text"]:focus,
  form input[type="email"]:focus,
  form input[type="tel"]:focus,
  form input[type="file"]:focus,
  form textarea:focus {
    outline: none;
    border-color: #0284c7; /* sky-600 */
  }
  form textarea {
    resize: vertical;
    min-height: 100px;
  }
  .submit-btn {
    margin-top: 1.8rem;
    background: #0284c7; /* sky-600 */
    color: white;
    border: none;
    padding: 0.9rem 2.5rem;
    font-size: 1.2rem;
    border-radius: 50px;
    cursor: pointer;
    display: block;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 6px 12px rgb(2 132 199 / 0.4);
    transition: background 0.3s ease;
  }
  .submit-btn:hover {
    background: #0369a1; /* sky-700 */
  }
  .form-svg {
    position: absolute;
    top: -60px;
    right: -60px;
    width: 120px;
    opacity: 0.15;
    animation: float 6s ease-in-out infinite;
  }
  @keyframes fadeInUp {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
  }
  @keyframes float {
    0%, 100% { transform: translateY(0);}
    50% { transform: translateY(-15px);}
  }
</style>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">ScholarSphere</div>
  <img src="logo.svg" alt="Logo" class="nav-logo" />
  <div class="nav-right"><button onclick="location.href='status.php'" class="status-btn">Application Status</button></div>
</nav>

<div class="apply-container">
  <!-- SVG decoration -->
  <svg class="form-svg" xmlns="http://www.w3.org/2000/svg" fill="#0284c7" viewBox="0 0 64 64">
    <circle cx="32" cy="32" r="30" />
  </svg>

  <h2>Apply for Scholarship</h2>
  <?php if ($scholarship): ?>
    <div class="scholarship-title"><?= htmlspecialchars($scholarship['title']) ?></div>
  <?php else: ?>
    <p style="text-align:center; color:#ef4444;">Invalid scholarship selected.</p>
  <?php endif; ?>

  <?php if ($scholarship): ?>
  <form action="submit_application.php" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="scholarship_id" value="<?= $id ?>" />
    <label for="fullname">Full Name *</label>
    <input type="text" id="fullname" name="fullname" required placeholder="Your full name" />

    <label for="email">Email *</label>
    <input type="email" id="email" name="email" required placeholder="you@example.com" />

    <label for="phone">Phone Number *</label>
    <input type="tel" id="phone" name="phone" required placeholder="+1 234 567 8901" />

    <label for="address">Address *</label>
    <textarea id="address" name="address" required placeholder="Your address"></textarea>

    <label for="document">Upload Supporting Document (PDF, max 2MB) *</label>
    <input type="file" id="document" name="document" accept=".pdf" required />

    <button type="submit" class="submit-btn">Submit Application</button>
  </form>
  <?php endif; ?>
</div>

<footer class="footer">
  <p>© 2025 ScholarSphere. All rights reserved.</p>
</footer>

</body>
</html>
