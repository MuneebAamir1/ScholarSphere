<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $scholarship_id = intval($_POST['scholarship_id'] ?? 0);
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');  // Your form may or may not have this
    // Adjust fields according to your actual form

    if (!$scholarship_id || !$fullname || !$email) {
        die("Required fields missing");
    }

    // Validate and upload file (same as before)
    if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        die("Document upload failed.");
    }
    $allowedTypes = ['application/pdf'];
    $fileType = $_FILES['document']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Only PDF files allowed.");
    }
    if ($_FILES['document']['size'] > 2 * 1024 * 1024) {
        die("File too large.");
    }
    $uploadsDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }
    $fileName = uniqid('doc_') . '.pdf';
    $filePath = $uploadsDir . $fileName;
    if (!move_uploaded_file($_FILES['document']['tmp_name'], $filePath)) {
        die("Failed to save uploaded file.");
    }

    // 1. Insert student info into students table (or check if already exists)
    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Student already exists, get id
        $stmt->bind_result($student_id);
        $stmt->fetch();
    } else {
        // Insert new student
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO students (fullname, email, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $fullname, $email);
        if (!$stmt->execute()) {
            die("Failed to insert student: " . $stmt->error);
        }
        $student_id = $stmt->insert_id;
    }
    $stmt->close();

    // 2. Insert into applications table
    $stmt = $conn->prepare("INSERT INTO applications (student_id, scholarship_id, document_path, submission_date, status) VALUES (?, ?, ?, NOW(), 'Pending')");
    if ($stmt === false) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("iis", $student_id, $scholarship_id, $fileName);

    if ($stmt->execute()) {
        header("Location: index.php?msg=Application submitted successfully");
        exit();
    } else {
        die("Failed to insert application: " . $stmt->error);
    }
}
