<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process clock in/out
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_GET['email'] ?? '';
    if (empty($email)) {
        echo "Email parameter is missing";
        exit;
    }

    // Fetch the employee ID based on the email
    $empid_query = "SELECT id FROM employee WHERE email = '$email'";
    $empid_result = $conn->query($empid_query);

    if ($empid_result->num_rows > 0) {
        $row = $empid_result->fetch_assoc();
        $empid = $row['id'];

        $current_time = date('H:i:s');
        $is_within_entry_time = $current_time >= '08:30:00' && $current_time <= '09:30:00';
        $is_within_exit_time = $current_time >= '16:00:00' && $current_time <= '17:00:00'; // Changed to 5:00 PM

        if (isset($_POST['clock_in_out'])) {
            if ($is_within_entry_time) {
                // Check if the employee has already clocked in today
                $check_query = "SELECT * FROM attendance WHERE empid = $empid AND DATE(date) = CURDATE()";
                $check_result = $conn->query($check_query);

                if ($check_result->num_rows > 0) {
                    // Employee has already clocked in, update exit time
                    $update_query = "UPDATE attendance SET exit_time = NOW() WHERE empid = $empid AND DATE(date) = CURDATE()";
                    if ($conn->query($update_query) === TRUE) {
                        echo "Clock out successful";
                    } else {
                        echo "Error updating exit time: " . $conn->error;
                    }
                } else {
                    // Employee has not clocked in yet, insert new record
                    $insert_query = "INSERT INTO attendance (empid, date, entry_time, exit_time, status) VALUES ($empid, CURDATE(), NOW(), NOW(), 'Present')";
                    if ($conn->query($insert_query) === TRUE) {
                        echo "Clock in successful";
                    } else {
                        echo "Error inserting attendance record: " . $conn->error;
                    }
                }
            } else {
                echo "Attendance cannot be marked at this time for clock in";
            }
        } elseif (isset($_POST['clock_out'])) {
            if ($is_within_exit_time) {
                // Update the exit time for the current date
                $update_query = "UPDATE attendance SET exit_time = NOW() WHERE empid = $empid AND DATE(date) = CURDATE()";
                if ($conn->query($update_query) === TRUE) {
                    echo "Clock out successful";
                } else {
                    echo "Error updating exit time: " . $conn->error;
                }
            } else {
                echo "Attendance cannot be marked at this time for clock out";
            }
        } else {
            echo "Invalid operation";
        }
    } else {
        echo "Employee not found";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Management System</title>
</head>
<body>
    <h2>Attendance Management System</h2>
    <form method="post" action="">
        <button type="submit" name="clock_in_out">Clock In</button>
        <button type="submit" name="clock_out">Clock Out</button>
    </form>
</body>
</html>
