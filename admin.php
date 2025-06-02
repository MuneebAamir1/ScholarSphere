<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>


<?php
$mysqli = new mysqli("localhost", "root", "", "ScholarSphere");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] === 'delete_scholarship' && isset($_GET['id'])) {
    $scholarship_id = (int)$_GET['id'];

    $stmt = $mysqli->prepare("DELETE FROM applications WHERE scholarship_id = ?");
    if ($stmt === false) {
        die("Prepare failed (applications delete): " . $mysqli->error);
    }
    $stmt->bind_param("i", $scholarship_id);
    if (!$stmt->execute()) {
        die("Execute failed (applications delete): " . $stmt->error);
    }
    $stmt->close();

    // Delete scholarship
    $stmt2 = $mysqli->prepare("DELETE FROM scholarships WHERE id = ?");
    if ($stmt2 === false) {
        die("Prepare failed (scholarship delete): " . $mysqli->error);
    }
    $stmt2->bind_param("i", $scholarship_id);
    if (!$stmt2->execute()) {
        die("Execute failed (scholarship delete): " . $stmt2->error);
    }
    $stmt2->close();

    // Redirect back to admin.php to avoid resubmission
    header("Location: admin.php");
    exit();
}

// Handle scholarship add form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_scholarship'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $eligibility = trim($_POST['eligibility']);
    $deadline = $_POST['deadline'];

    if ($title && $deadline) {
        $stmt = $mysqli->prepare("INSERT INTO scholarships (title, description, eligibility, deadline)
         VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $eligibility, $deadline);
        if ($stmt->execute()) {
            $message = "Scholarship added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Title and deadline are required.";
    }
}


if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id     = (int)$_GET['id'];

    // 1) DELETE scholarship (and its applications)
    if ($action === 'delete_scholarship') {
        // delete dependent applications
        $stmt = $mysqli->prepare("DELETE FROM applications WHERE scholarship_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        // delete scholarship itself
        $stmt = $mysqli->prepare("DELETE FROM scholarships WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php");
        exit();
    }

    // 2) APPROVE / REJECT application
    if (in_array($action, ['approve', 'reject'])) {
        // map action → ENUM value
        $status = $action === 'approve' ? 'accepted' : 'rejected';

        // prepare
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        if (!($stmt = $mysqli->prepare($sql))) {
            die("Prepare failed: " . $mysqli->error);
        }

        // bind & execute
        $stmt->bind_param("si", $status, $id);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        // ensure we updated something
        if ($stmt->affected_rows === 0) {
            die("No application updated (id = $id, status = $status).");
        }

        $stmt->close();
        header("Location: admin.php");
        exit();
    }
}



// Fetch applications
$applications = $mysqli->query("SELECT * FROM application_summary ORDER BY submission_date DESC");

// Fetch students info 
$students = $mysqli->query("SELECT fullname, email, phone, address, created_at
 FROM students ORDER BY created_at DESC");

// Fetch scholarships 
$scholarships = $mysqli->query("SELECT * FROM scholarships ORDER BY deadline ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Panel - ScholarSphere</title>
<style>
  /* Same styling as before */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef2f7;
    margin: 0; padding: 20px;
    color: #333;
  }
  h1, h2 {
    text-align: center;
    color: #3b82f6;
  }
  form {
    background: white;
    max-width: 700px;
    margin: 20px auto 40px auto;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgb(59 130 246 / 0.2);
    animation: fadeInDown 1s ease forwards;
  }
  form h2 {
    margin-bottom: 20px;
    color: #2563eb;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
  }
  input[type="text"],
  input[type="date"],
  textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 18px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s;
  }
  input[type="text"]:focus,
  input[type="date"]:focus,
  textarea:focus {
    border-color: #3b82f6;
    outline: none;
  }
  button {
    background-color: #3b82f6;
    color: white;
    font-weight: 700;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s;
  }
  button:hover {
    background-color: #2563eb;
  }
  .message {
    max-width: 700px;
    margin: 0 auto 20px auto;
    padding: 12px 20px;
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
    border-radius: 8px;
    font-weight: 600;
  }
  table {
    margin: 20px auto;
    border-collapse: collapse;
    width: 90%;
    max-width: 900px;
    background: white;
    box-shadow: 0 4px 8px rgb(59 130 246 / 0.2);
    animation: fadeInUp 1s ease forwards;
  }
  th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
    vertical-align: middle;
  }
  th {
    background-color: #3b82f6;
    color: white;
  }
  tr:nth-child(even) {
    background-color: #f0f6ff;
  }
  .btn {
    padding: 6px 12px;
    margin-right: 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
  }
  .approve {
    background-color: #10b981;
  }
  .reject {
    background-color: #ef4444;
  }
  .delete {
    background-color: #ef4444;
  }
  @keyframes fadeInDown {
    0% {
      opacity: 0;
      transform: translateY(-30px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }
  @keyframes fadeInUp {
    0% {
      opacity: 0;
      transform: translateY(30px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
</head>
<body>

<h1>ScholarSphere Admin Panel</h1>

<?php if (isset($message)): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Scholarship Add Form -->
<form method="POST" action="admin.php">
  <h2>Add New Scholarship</h2>
  <label for="title">Title *</label>
  <input type="text" id="title" name="title" required placeholder="Enter scholarship title" />

  <label for="description">Description</label>
  <textarea id="description" name="description" rows="4" placeholder="Brief description..."></textarea>

  <label for="eligibility">Eligibility Criteria</label>
  <textarea id="eligibility" name="eligibility" rows="3" placeholder="Who can apply?"></textarea>

  <label for="deadline">Application Deadline *</label>
  <input type="date" id="deadline" name="deadline" required />

  <button type="submit" name="add_scholarship">Add Scholarship</button>
</form>

<!-- Applications Table -->
<h2>Applications</h2>
<table>
  <thead>
    <tr>
      <th>Application ID</th>
      <th>Student Name</th>
      <th>Scholarship</th>
      <th>Status</th>
      <th>Submitted On</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($applications && $applications->num_rows > 0): ?>
      <?php while ($row = $applications->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['application_id']) ?></td>
          <td><?= htmlspecialchars($row['fullname']) ?></td>
          <td><?= htmlspecialchars($row['scholarship_title']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['submission_date']) ?></td>
          <td>
            <?php if ($row['status'] === 'pending'): ?>
  <a href="admin.php?action=approve&id=<?= $row['application_id'] ?>" class="btn approve">Approve</a>
  <a href="admin.php?action=reject&id=<?= $row['application_id'] ?>" class="btn reject">Reject</a>
<?php else: ?>
  <em>No actions</em>
<?php endif; ?>

          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;">No applications found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Manage Scholarships Table -->
<h2>Manage Scholarships</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Description</th>
      <th>Eligibility</th>
      <th>Deadline</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($scholarships && $scholarships->num_rows > 0): ?>
      <?php while ($sch = $scholarships->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($sch['id']) ?></td>
          <td><?= htmlspecialchars($sch['title']) ?></td>
          <td><?= nl2br(htmlspecialchars($sch['description'])) ?></td>
          <td><?= nl2br(htmlspecialchars($sch['eligibility'])) ?></td>
          <td><?= htmlspecialchars($sch['deadline']) ?></td>
          <td>
            <a href="admin.php?action=delete_scholarship&id=<?= $sch['id'] ?>" class="btn delete" onclick="return confirm('Delete this scholarship?')">Delete</a>


        </td>
        </tr>
     <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;">No scholarships found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<style>
.logout-btn {
    background: #ef4444;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: none;
}
.logout-btn:hover {
    background: #dc2626;
}
</style>
<a href="logout.php" class="logout-btn">Logout</a>

</body>
</html>
