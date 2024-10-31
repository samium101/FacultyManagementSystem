<?php
// add_event.php
session_start();
require 'db.php';

// Check if user is logged in and is a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $organization = trim($_POST['organization']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $description = trim($_POST['description']);

    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO events (user_id, title, organization, event_date, event_time, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $title, $organization, $event_date, $event_time, $description);
    if ($stmt->execute()) {
        // Redirect back to dashboard with success message
        header("Location: dashboard.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // If not a POST request, redirect to dashboard
    header("Location: dashboard.php");
    exit();
}
?>
