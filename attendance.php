<?php
// Include your database connection code here
include('../conn.php');

// Get the email ID from the URL
$email = isset($_GET['email']) ? $_GET['email'] : '';
if (empty($email)) {
    echo 'Email parameter is missing';
    exit;
}

// Sanitize the email to prevent SQL injection
$email = $conn->real_escape_string($email);

// Fetch the employee ID corresponding to the email ID
$sqlEmployeeId = "SELECT id FROM employee WHERE email = '$email'";
$resultEmployeeId = $conn->query($sqlEmployeeId);

if ($resultEmployeeId && $resultEmployeeId->num_rows > 0) {
    $rowEmployeeId = $resultEmployeeId->fetch_assoc();
    $empid = $rowEmployeeId['id'];

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the data from the AJAX request
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $entry_time = isset($_POST['entry_time']) ? $_POST['entry_time'] : '';
        $exit_time = ''; // Set exit time as empty for now
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        // Insert the attendance record into the 'attendance' table
        $sql = "INSERT INTO attendance (empid, date, entry_time, exit_time, status)
                VALUES ($empid, '$date', '$entry_time', '$exit_time', '$status')";

        if ($conn->query($sql) === TRUE) {
            echo 'Attendance marked successfully!';
        } else {
            echo 'Error marking attendance: ' . $conn->error;
        }

        // Close the database connection
        //$conn->close();
    } else {
        // Default values for demonstration
        $date = date('Y-m-d');
        $entry_time = date('H:i:s');
        $exit_time = '';
        $status = 'Present';
    }
} else {
    echo 'Employee not found';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
</head>
<body>
    <div class="main-content">
        <h2 style="color: #333; text-align: center;">Mark Attendance</h2>
        
        <!-- Hidden input fields to store entry time, date, and status -->
        <input type="hidden" id="date" name="date">
        <input type="hidden" id="entry_time" name="entry_time">
        <input type="hidden" id="status" name="status">

        <button onclick="fillAndMarkAttendance()">Mark Attendance</button>
    </div>

    <script>
    function fillAndMarkAttendance() {
        const date = new Date().toISOString().slice(0, 10); // Get current date in YYYY-MM-DD format
        const entry_time = new Date().toLocaleTimeString('en-US', { hour12: false });
        const status = 'Present'; // Assuming always marking present

        // Fill the hidden input fields with the values
        document.getElementById('date').value = date;
        document.getElementById('entry_time').value = entry_time;
        document.getElementById('status').value = status;

        // Send an AJAX request to attendance.php to insert the attendance record
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'attendance.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Attendance marked successfully!');
                } else {
                    alert('Error marking attendance. Please try again.');
                }
            }
        };
        xhr.send(`date=${date}&entry_time=${entry_time}&status=${status}`);
    }
    </script>
</body>
</html>


