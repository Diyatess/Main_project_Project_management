<?php
// Include your database connection code here
include('../conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the POST request
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $taskId = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;

    // Update the status in the 'task' table using prepared statement
    $updateSql = "UPDATE task SET status = ? WHERE task_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $status, $taskId);

    if ($stmt->execute()) {
        // Status updated successfully
        echo 'Status updated successfully';
    } else {
        // Error updating status
        echo 'Error updating status: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Invalid request method
    echo 'Invalid request method';
}

// Close the database connection
$conn->close();
?>
