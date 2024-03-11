<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your database connection code here
include('../conn.php');

// Initialize variables
$task = $employee = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $task = isset($_POST['task']) ? htmlspecialchars($_POST['task']) : '';
    $employee = isset($_POST['employee']) ? intval($_POST['employee']) : 0;

    // Set assignment date to the current date
    $assignment_date = date('Y-m-d');

    // Set default status as 'Pending'
    $status = 'Pending';

    // Assuming you have a task_id from the form or any other source
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;

    // Update the task in the 'task' table
    $sql = "UPDATE task SET task_description = ?, assigned_to = ?, assignment_date = ?, status = ? WHERE task_id = ?";
    $stmt = $conn->prepare($sql);
    echo $sql;

    // Check for errors in preparing the statement
    if (!$stmt) {
        die("Error in preparing the statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sissi", $task, $employee, $assignment_date, $status, $task_id);

    // Check for errors in binding parameters
    if ($stmt->errno) {
        die("Error in binding parameters: " . $stmt->error);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo "Task updated successfully!";
    } else {
        // Output error information
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Form not submitted.";
}

// Close the database connection
$conn->close();
?>
