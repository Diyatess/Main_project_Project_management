<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Assuming your table has a column 'employee_id'
    $sqlUserInfo = "SELECT id, fname, lname, desig_id FROM employee WHERE email = '$email'";
    $resultUserInfo = $conn->query($sqlUserInfo);

    if ($resultUserInfo->num_rows > 0) {
        $rowUserInfo = $resultUserInfo->fetch_assoc();
        $id = $rowUserInfo['id'];
        $fname = $rowUserInfo['fname'];
        $lname = $rowUserInfo['lname'];
        $desig_id = $rowUserInfo['desig_id'];

        // Fetch tasks assigned to the user based on employee_id
        $sqlTasks = "SELECT t.*, p.project_name
                     FROM task t
                     INNER JOIN project_requests p ON t.request_id = p.request_id
                     WHERE assigned_to = '$id'
                     ORDER BY p.project_name"; // Modify this line for sorting

        $resultTasks = $conn->query($sqlTasks);
        $tasks = [];

        if ($resultTasks->num_rows > 0) {
            while ($rowTask = $resultTasks->fetch_assoc()) {
                $tasks[$rowTask['project_name']][] = $rowTask;
            }
        }
    } else {
        $fname = "Unknown";
        $lname = "";
        $tasks = [];
    }
} else {
    var_dump($_GET);
    echo "Email not provided in the URL.";
    exit();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tasks</title>
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
            font-style: revert-layer;
        }
         /* Add this style for the button */
         button {
            background-color: #0074D9;
            color: #fff;
            cursor: pointer;
            padding: 8px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 10px;
        }

        /* New styles for the completion date */
        .completion-date {
            font-weight: bold;
            color: #4CAF50;
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

        function logout() {
            // Clear the session or perform any other necessary logout tasks
            history.pushState(null, null, window.location.href);
            window.onpopstate = function (event) {
                history.go(1);
            };
            window.location.replace("../login_client.php");
        }

        window.onload = function () {
            window.history.forward();
            document.onkeydown = function (e) {
                if (e.keyCode === 9) {
                    return false;
                }
            };
        }

        window.addEventListener('popstate', function (event) {
            window.location.replace("../login_client.php");
        });

        function updateStatus(taskId) {
            var status = document.getElementById('status_' + taskId).value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('Status updated successfully');
                    alert('Status updated successfully'); // Show an alert box
                } else {
                    console.error('Error updating status');
                }
            };

            xhr.onerror = function () {
                console.error('Network error while updating status');
            };

            xhr.send('status=' + status + '&task_id=' + taskId);
        }
      
    </script>
</head>
<body>
    <a class="navbar-brand" href="../index.php" style="float: left;">
        <img src="../images/logo.png" alt="" />
        <span> TaskMasters Hub</span>
    </a>

    <header>
        <!-- Logout button -->
        <h2>Tasks Assigned to <?php echo $fname . " " . $lname; ?>:</h2>
    </header>

    <div class="container">
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $projectName => $projectTasks): ?>
                <h3><?php echo $projectName; ?></h3>
                <ul class="task-list">
                    <?php foreach ($projectTasks as $task): ?>
                        <li class="task-item">
                            <strong>Task ID: <?php echo $task['task_id']; ?></strong><br>
                            Task Description: <?php echo $task['task_description']; ?><br>
                            Assignment Date: <?php echo $task['assignment_date']; ?><br>
                            Completion Date: <span class="completion-date"><?php echo $task['completion_date']; ?></span><br>
                            Project Name: <?php echo $task['project_name']; ?><br>
                            Status:
                            <select name="status" id="status_<?php echo $task['task_id']; ?>">
                                <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Started" <?php echo ($task['status'] == 'Started') ? 'selected' : ''; ?>>Started</option>
                                <option value="On Progress" <?php echo ($task['status'] == 'On Progress') ? 'selected' : ''; ?>>On Progress</option>
                                <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <button onclick="updateStatus(<?php echo $task['task_id']; ?>)">Update Status</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tasks assigned to <?php echo $fname . " " . $lname; ?>.</p>
        <?php endif; ?>
    </div>

<!-- Sidebar --> 
<div id="mySidebar" class="sidebar">
        <a href="mdashboard.php?email=<?php echo $email; ?>">
            <span class="dashboard-icon">📊</span> Dashboard
        </a>
        <a href="leave.php?email=<?php echo $email; ?>&action=apply">Apply Leave</a>
        <a href="view_leavestatus.php?email=<?php echo $email; ?>&action=view">View Approved/Rejected Leave</a>
        <!--<a href="view_task.php?email=<?php echo $email; ?>">
            <span class="icon">&#128065;</span> View Task
        </a>-->
        <a href="dailyreport.php?email=<?php echo $email; ?>">
            <span class="icon">&#128196;</span> Daliy Report
        </a>
       
        <!--<a href="view_status.php">
            <span class="icon">&#9888;</span> View Status
        </a>
        <a href="view_meeting.php">
            <span class="icon">&#9201;</span> View Meeting
        </a>-->
        <a href="change_password.php?email=<?php echo $email; ?>">
            <span class="icon">&#128274;</span> Change Password
        </a>
        <a href="view_salary_slip.php?email=<?php echo $email; ?>">
            <span class="icon"></span> Salary Details
        </a>
        <!--<a href="view_projects.php">
            <span class="icon">&#128213;</span> View Approved/Denied Projects
        </a>-->
        <button onclick="logout()" type="button" style="float: right;">Logout</button>
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>

</body>
</html>