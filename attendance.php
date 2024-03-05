<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from the query string
$email = $_GET['email'] ?? '';

// Fetch the employee ID based on the email
$empid_query = "SELECT id FROM employee WHERE email = '$email'";
$empid_result = $conn->query($empid_query);

if ($empid_result->num_rows > 0) {
    $row = $empid_result->fetch_assoc();
    $empid = $row['id'];

    // Process clock in
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clock_in'])) {
        // Check if the employee has already clocked in today
        $check_query = "SELECT * FROM attendance WHERE empid = $empid AND DATE(date) = CURDATE()";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows > 0) {
            echo '<script>alert("Already Marked in today.")</script>';
        } else {
            // Employee has not clocked in yet, insert new record
            $insert_query = "INSERT INTO attendance (empid, date, entry_time, status) VALUES ($empid, CURDATE(), NOW(), 'Present')";
            if ($conn->query($insert_query) === TRUE) {
                echo '<script>alert("Marked attendance successful.")</script>';
            } else {
                echo "Error inserting attendance record: " . $conn->error;
            }
        }
    }

    // Process clock out
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clock_out'])) {
        // Update the exit time for the current date
        $update_query = "UPDATE attendance SET exit_time = NOW() WHERE empid = $empid AND DATE(date) = CURDATE()";
        if ($conn->query($update_query) === TRUE) {
            echo '<script>alert("Marked out successful.")</script>';
        } else {
            echo "Error updating exit time: " . $conn->error;
        }
    }

    // Fetch attendance information for this month, excluding Sundays
    $attendance_query = "SELECT date, entry_time, exit_time, status FROM attendance WHERE empid = $empid AND MONTH(date) = MONTH(NOW()) AND DAYOFWEEK(date) != 1";
    $attendance_result = $conn->query($attendance_query);

    $attendance_info = "";
    if ($attendance_result->num_rows > 0) {
        // Attendance records found, display them
        $attendance_info .= "<h3>Attendance for this month:</h3>";
        $attendance_info .= "<table border='1'>
                                <tr>
                                    <th>Date</th>
                                    <th>Entry Time</th>
                                    <th>Exit Time</th>
                                    <th>Present/Absent</th>
                                </tr>";
        while ($row = $attendance_result->fetch_assoc()) {
            $attendance_info .= "<tr>
                                    <td>" . $row['date'] . "</td>
                                    <td>" . $row['entry_time'] . "</td>
                                    <td>" . $row['exit_time'] . "</td>
                                    <td>" . $row['status'] . "</td>
                                </tr>";
        }
        $attendance_info .= "</table>";
    } else {
        // No attendance records found for this month
        $attendance_info = "Absent for this month";
    }

} else {
    echo "Employee not found";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Management System</title>
    <style>
   /* Style the sidebar */
   .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 76px;
            left: -250px;
            background-color: #333;
            overflow-x: hidden;
            transition: 0.5s;
            text-align: left;
            padding-top: 60px;
            color: #fff;
        }

        .sidebar a {
            padding: 8px 16px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
            margin: 15px 0;
        }
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }
        .openbtn {
            font-size: 30px;
            cursor: pointer;
            position: fixed;
            z-index: 1;
            top: 10px;
            left: 10px;
            color: #fff;
        }
        .icon {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Add a background color for the links */
        .sidebar a {
            background-color: #333;
        }

        /* On hover, the background color and text color change */
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }
/*main */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 24px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 32px;
            color: #fff;
            text-align: center; /* Center the heading text */
        }
        .profile-info {
            display: flex;
            align-items: center;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #0074D9;
            margin-right: 20px;
        }
        .profile-picture::before {
            content: "👤"; /* Display a user icon or your profile image */
            font-size: 50px;
            line-height: 100px;
            text-align: center;
            display: block;
        }
        .profile-details {
            color: #333;
        }
        .task-list {
            list-style: none;
            padding: 0;
        }
        .task-item {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .task-item strong {
            color: #0074D9;
        }
        a {
            text-decoration: none;
            color: #0074D9;
        }
        a.navbar-brand {
            color: black;  /* Set the text color to black */
            text-decoration: none;  /* Remove the underline */
            font-weight: bold;
            color: #fff;
            font-size: 24px;
            margin-left: 45px;
            padding: 0px;

        }
        img {
            width: 39px;
            height: 39px;
        }
        button{
            block-size: 27px;
            background: white;
            border: black;
            float: right;
            font-style: revert-layer;
        }
/* Clock in button */
button[name="clock_in"] {
    background-color: #4CAF50; /* Green */
    color: white;
    font-size: large;
    padding: 5px 32px;
    margin-left: 500px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Clock out button */
button[name="clock_out"] {
    background-color: #f44336; /* Red */
    color: white;
    font-size: large;
    padding: 5px 32px;
  
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
}

tr:hover {
    background-color: #f2f2f2;
}

    </style>
    <script>
        let sidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            if (sidebarOpen) {
                sidebar.style.left = "-250px";
            } else {
                sidebar.style.left = "0";
            }
            sidebarOpen = !sidebarOpen;
        }
        // Logout function
        function logout() {
            // Clear the session or perform any other necessary logout tasks
            // Disable the ability to go back
            history.pushState(null, null, window.location.href);
            window.onpopstate = function (event) {
                history.go(1);
            };

            // Redirect to the login page
            window.location.replace("../login_client.php");
        }

    // Disable caching to prevent back button from showing the logged-in page
    window.onload = function () {
        window.history.forward();
        document.onkeydown = function (e) {
            if (e.keyCode === 9) {
                return false;
            }
        };
    }

    // Redirect to the login page if the user tries to go back
    window.addEventListener('popstate', function (event) {
        window.location.replace("../login_client.php");
    });
    </script>
</head>
<body>
<a class="navbar-brand" href="../index.php" style="float: left;">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span>
        </a>
    <header>
        <!-- Logout button -->
        <button ><a href="mdashboard.php?email=<?php echo $email; ?>">Back</a></button>
        <h2>Attendance Management System</h2>
    </header>
    <!-- Sidebar --> 
<div id="mySidebar" class="sidebar">
        <a href="mdashboard.php?email=<?php echo $email; ?>">
            <span class="dashboard-icon">📊</span> Dashboard
        </a>
        <!--<a href="view_profile.php?email=<?php echo $email; ?>">
            <span class="icon" style="color: white;">🧑‍💻</span> Edit Profile
        </a>-->
    
        <a href="attendance.php?email=<?php echo $email; ?>">
            <span class="icon">&#128065;</span> Mark Attendance
        </a>
        <a href="dailyreport.php?email=<?php echo $email; ?>">
            <span class="icon">&#128196;</span> Daliy Report
        </a>
        <a href="chat.php?email=<?php echo $email; ?>">
            <span class="icon">&#128172;</span> Chat
        </a>

        <!--<a href="view_status.php">
            <span class="icon">&#9888;</span> View Status
        </a>-->
        <a href="schedule_meeting.php">
            <span class="icon">&#9201;</span> View Meeting
        </a>
        <a href="change_password.php?email=<?php echo $email; ?>">
            <span class="icon">&#128274;</span> Change Password
        </a>

        <!--<a href="view_projects.php">
            <span class="icon">&#128213;</span> View Approved/Denied Projects
        </a>-->
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    <div class="container">
    <form method="post" action="">
        <button type="submit" name="clock_in">Mark In</button>
        <button type="submit" name="clock_out">Mark Out</button>
    </form>
    <br><br>
    <!-- Display attendance information -->
    <div class="attendance-info">
        <?php echo $attendance_info; ?>
    </div>
</div>
</body>
</html>
