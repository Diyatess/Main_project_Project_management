<?php
include('../conn.php');

// Get the employee ID based on the email
$email = isset($_GET['email']) ? $_GET['email'] : '';
$sql = "SELECT id FROM employee WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $empid = $row['id'];

    // Insert the attendance record with entry time
    $insertEntrySql = "INSERT INTO attendance (empid, date, entry_time, status) 
                  VALUES ('$empid', CURDATE(), CURTIME(), 'Present')";

    if ($conn->query($insertEntrySql) === TRUE) {
        echo "Entry time marked successfully!";
    } else {
        echo "Error marking entry time: " . $conn->error;
    }
} else {
    echo "Employee not found";
}

$conn->close();
?>
