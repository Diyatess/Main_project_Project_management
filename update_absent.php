<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for employees who did not mark attendance today (excluding Sunday)
$absent_query = "SELECT id FROM employee WHERE id NOT IN (SELECT empid FROM attendance WHERE DATE(date) = CURDATE() AND WEEKDAY(date) != 6)";
$absent_result = $conn->query($absent_query);

if ($absent_result->num_rows > 0) {
    while ($row = $absent_result->fetch_assoc()) {
        $empid = $row['id'];

        // Insert an attendance record with status 'Absent' for each missing employee
        $insert_query = "INSERT INTO attendance (empid, date, status) VALUES ($empid, CURDATE(), 'Absent')";
        if ($conn->query($insert_query) !== TRUE) {
            echo "Error inserting attendance record: " . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>
