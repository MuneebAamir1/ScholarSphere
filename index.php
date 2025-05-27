<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ScholarSphere</title>
</head>
<body>
    <nav class="navbar">
    <div class="nav-left">🎓 ScholarSphere</div>
    <div class="nav-logo">
      <img src="img/logo.png" alt="Logo" class="logo-img" />
    </div>
    <div class="nav-right">
      <a href="status.php" class="status-btn">📬 Status</a>
    </div>
  </nav>
  <section class="hero">
  <div class="hero-content">
    <h1 class="hero-heading">🎓 Discover Scholarships<br>That Match Your Dreams</h1>
    <p class="hero-sub">Apply in just a few clicks. No logins, no stress.</p>
    <a href="#scholarships" class="hero-btn">Browse Scholarships</a>
  </div>
  <div class="hero-image">
    <img src="img/scholar-hero.svg" alt="Scholarship Illustration" />
  </div>
</section>

<section class="benefits-section">
  <h2 class="benefits-title">🎯 Why Apply for Scholarships?</h2>
  <div class="benefits-container">
    <div class="benefit-card">
      <div class="benefit-icon">💰</div>
      <h3>Reduce Financial Burden</h3>
      <p>Cover tuition fees, books, and living expenses without student loans.</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">🏆</div>
      <h3>Recognize Your Talent</h3>
      <p>Get rewarded for your academic, athletic, or artistic achievements.</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">🚀</div>
      <h3>Expand Opportunities</h3>
      <p>Access better programs, internships, and learning environments.</p>
    </div>
    <div class="benefit-card">
      <div class="benefit-icon">📜</div>
      <h3>Boost Your Resume</h3>
      <p>Stand out with recognized awards and achievements.</p>
    </div>
  </div>
</section>

<section class="scholarships-section" id="scholarships">
  <img src="img/scholarship-blob.svg" alt="Scholarship Visual" class="scholar-bg" />
  <h2 class="section-title">📚 Available Scholarships</h2>
  <div class="scholarship-grid">
<?php
include 'db.php';

$sql = "SELECT * FROM scholarships ORDER BY deadline ASC";
$result = $conn->query($sql);

$delay_classes = ['','delay-1','delay-2','delay-3','delay-4','delay-5']; 
$i = 0;

if ($result->num_rows > 0) {
    while($scholarship = $result->fetch_assoc()) {
        $delay_class = $delay_classes[$i % count($delay_classes)]; 
        
        echo '<div class="scholarship-card animated ' . $delay_class . '">';
        echo '<h3>' . htmlspecialchars($scholarship['title']) . '</h3>';
        echo '<p><strong>Deadline:</strong> ' . date("F j, Y",
         strtotime($scholarship['deadline'])) . '</p>';
        echo '<p><strong>Eligibility:</strong> ' 
        . htmlspecialchars($scholarship['eligibility']) . '</p>';
        echo '<a href="apply.php?scholarship_id=' . $scholarship['id'] . '" class="apply-btn">Apply Now</a>';
        echo '</div>';
        
        $i++;
    }
} else {
    echo '<p>No scholarships found.</p>';
}
?>
</div>

</section>
<footer class="footer-section">
  <div class="footer-container">
    <div class="footer-left">
      <h3>🎓 ScholarSphere</h3>
      <p>Helping students connect with the financial aid they deserve.</p>
    </div>
    <div class="footer-right">
      <p><strong>Contact:</strong> info@scholarsphere.edu</p>
      <div class="social-icons">
        <img src="img/facebook.svg" alt="Facebook">
        <img src="img/twitter.svg" alt="Twitter">
        <img src="img/linkedin.svg" alt="LinkedIn">
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 ScholarSphere. All rights reserved.</p>
  </div>
</footer>


</body>
</html>