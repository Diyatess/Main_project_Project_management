<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the form is submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle approval/rejection logic here
    // Example:
    $leaveId = $_POST['leave_id'];
    $status = $_POST['status'];
    $update_query = "UPDATE leave_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $leaveId);
    if ($stmt->execute()) {
        echo '<script>alert("Leave request updated successfully.")</script>';
    } else {
        echo "Error updating leave request: " . $conn->error;
    }
    $stmt->close();
}

// Fetch leave details
$sqlLeaveDetails = "SELECT lr.id, e.fname AS employee_name, lr.start_date, lr.end_date, lr.leave_type, lr.reason, lr.status, lr.apply_status
                    FROM leave_requests lr
                    JOIN employee e ON lr.empid = e.id WHERE lr.apply_status=1";

$resultLeaveDetails = $conn->query($sqlLeaveDetails);
if (!$resultLeaveDetails) {
    die("Error fetching leave details: " . $conn->error);
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Daily Reports</title>
    <style>
   /* Style the sidebar */
   .sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 76px;
    left: 0;
    background-color: #333;
    overflow-x: hidden;
    transition: 0.5s;
    text-align: left;
    padding-top: 60px;
    color: #fff;
    z-index: 1;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add a box shadow for depth */
}

/* Sidebar links */
.sidebar a {
    padding: 10px 15px;
    text-decoration: none;
    font-size: 18px;
    color: #fff;
    display: block;
    transition: 0.3s;
}

/* Change color on hover */
.sidebar a:hover {
    background-color: #555;
}

/* Add active class to the current link (highlight it) */
.sidebar a.active {
    background-color: #007bff;
}

/* Close button */
.closebtn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 30px;
    cursor: pointer;
}

/* Add a black background color to the top navigation */
.topnav {
    background-color: #333;
    overflow: hidden;
}

/* Style the topnav links */
.topnav a {
    float: left;
    display: block;
    color: white;
    text-align: center;
    padding: 14px 20px;
    text-decoration: none;
}

/* Change color on hover */
.topnav a:hover {
    background-color: #ddd;
    color: black;
}

/* Updated CSS for the main content area */
.container {
    max-width: calc(100% - 250px); /* Subtract the width of the sidebar from the max-width of the container */
    margin-left: 250px; /* Set the margin-left to the width of the sidebar */
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    transition: margin-left 0.5s; /* Add a transition for smooth animation */
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
        h1 {
            font-size: 32px;
            color: #fff;
            text-align: center; /* Center the heading text */
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
            color: #000;
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
        .back-button {
            background-color: #555;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #777;
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
        /* Container styles */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Date form styles */
        #dateForm {
            margin-bottom: 20px;
        }

        #selected_date {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 30%;
            font-size: 16px;
            margin-bottom: 10px;
        }

        #selected_date:focus {
            outline: none;
            border-color: #007bff;
        }

        input[type="submit"] {
            display: inline-block;
            background-color: #000;
            border: none;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 7px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #3b003b;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

    </style>
    <script>
        // Function to disable future dates
        function disableFutureDates() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("selected_date").setAttribute('max', today);
        }

        // Function to validate date input
        function validateDate() {
            var selectedDate = document.getElementById("selected_date").value;
            var today = new Date().toISOString().split('T')[0];

            if (selectedDate > today) {
                alert("Please select a date on or before today.");
                return false;
            }

            return true;
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
<body onload="disableFutureDates()">
<a class="navbar-brand" href="../index.php" style="float: left;">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span>
        </a>
    <header>
        <!-- Logout button -->
 <button onclick="logout()" type="button" style="float: right;">Logout</button>    
        <h2>Leave Request</h2>
    </header>
<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <a href="tdashboard.php">   
        <span class="dashboard-icon">📊</span> Dashboard
    </a>
    <a href="add_task.php">
        <span class="icon">&#10010;</span> Add Task
    </a>
    <a href="view_status.php">
        <span class="icon">&#128196;</span> View Status
    </a>
    <a href="select_project.php">
            <span class="icon">&#9201;</span> Daily Standup Meeting
    </a>
    <a href="client_meeting.php">
            <span class="icon">&#9201;</span> Sprint Review
    </a>
    <a href="report.php">
        <span class="icon">&#128221;</span> Monitor Daily Progress
    </a>
    <a href="view_projects.php">
        <span class="icon">&#128213;</span> View Approved/Denied Projects
    </a>
    <a href="functional.php">
        <span class="icon">📏</span> Calculate Functional Point
    </a>
    <a href="view_leave.php">
        <span class="icon"></span> View Leave
    </a>
    <a href="salary.php">
        <span class="icon"></span>Salary
    </a>
    <a href="deploy.php">
        <span class="icon"></span>Deploy Project
    </a>
    </div>

    <div class="container">
        <h2>Leave Requests</h2>
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%;">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Leave Type</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultLeaveDetails->num_rows > 0) {
                    while ($rowLeaveDetails = $resultLeaveDetails->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $rowLeaveDetails['employee_name']; ?></td>
                            <td><?php echo $rowLeaveDetails['start_date']; ?></td>
                            <td><?php echo $rowLeaveDetails['end_date']; ?></td>
                            <td><?php echo $rowLeaveDetails['leave_type']; ?></td>
                            <td><?php echo $rowLeaveDetails['reason']; ?></td>
                            <td><?php echo $rowLeaveDetails['status']; ?></td>
                            <td>
                            <form method="post" action="">
                            <input type="hidden" name="leave_id" value="<?php echo $rowLeaveDetails['id']; ?>">
                                        <button type="submit" name="status" value="approved" style="background-color: green; color: white;" <?php echo $rowLeaveDetails['status'] == 'approved' ? 'disabled' : ''; ?>>Accept</button>
                                        <button type="submit" name="status" value="rejected" style="background-color: red; color: white;" <?php echo $rowLeaveDetails['status'] == 'rejected' ? 'disabled' : ''; ?>>Reject</button>
                            </form>

                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No leave requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>