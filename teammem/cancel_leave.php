<?php
// Include your database connection code here
include('../conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve leave ID and email from POST data
    $leaveId = $_POST['id'];
    $email = $_POST['email'];

    // Retrieve emp_id based on email
    $sqlEmpId = "SELECT id FROM employee WHERE email = ?";
    $stmtEmpId = $conn->prepare($sqlEmpId);
    $stmtEmpId->bind_param("s", $email);
    $stmtEmpId->execute();
    $resultEmpId = $stmtEmpId->get_result();

    if ($resultEmpId->num_rows > 0) {
        // Fetch emp_id
        $rowEmpId = $resultEmpId->fetch_assoc();
        $empid = $rowEmpId['id'];

        // Update the leave request status in the database based on leave ID
        $update_query = "UPDATE leave_requests SET apply_status = 0 WHERE empid = ? AND id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $empid, $leaveId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error canceling leave request: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Employee ID not found.";
    }
}

// Close connection
$conn->close();
?>
