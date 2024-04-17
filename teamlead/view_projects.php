<?php
session_start();
include('../conn.php');

// Fetch approved and denied projects
$sql = "SELECT project_requests.project_name, project_requests.project_description, 
project_requests.client_email AS project_client_email, project_requests.status AS project_status, 
client.cname AS client_name, client.email AS client_email FROM project_requests JOIN client ON project_requests.client_email = client.email 
WHERE project_requests.status IN ('Approved', 'Denied')";

$result = $conn->query($sql);

$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Approved/Denied Projects</title>
    <style>
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
            text-align: center;
        }
        .project-list {
            list-style: none;
            padding: 0;
        }
        .project-item {
            margin: 20px 0;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .project-item strong {
            color: #0074D9;
        }
        a {
            text-decoration: none;
            color: #0074D9;
        }
        a.navbar-brand {
            color: black;
            text-decoration: none;
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
        .action-button {
        display: inline-block;
        padding: 10px 10px;
        margin: 5px;
        text-decoration: none;
        color: #fff;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 12px;
        transition: background-color 0.3s ease;
    }

    .reply-button {
        background-color: #4CAF50; /* Green */
    }
    </style>
    <script>
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
    <span>TaskMasters Hub</span>
</a>
<a class="navbar-brand" href="tdashboard.php" style="float: right;">
    <span>Back</span>
</a>
<header>
    <h1>Project Details</h1>
  
</header>
<div class="container">
    <h2>Approved/Denied Projects</h2>
    <ul class="project-list">
        <?php foreach ($projects as $project) : ?>
            <li class="project-item">
                <strong>Project Name:</strong> <?php echo $project['project_name']; ?><br>
                <strong>Project Description:</strong> <?php echo $project['project_description']; ?><br>
                <strong>Project Status:</strong> <?php echo $project['project_status']; ?><br>
                <strong>Client Name:</strong> <?php echo $project['client_name']; ?><br>
                <strong>Client Email:</strong> <?php echo $project['client_email']; ?><br>
                <a href="message.php?request_id=<?php echo $project['project_client_email']; ?>" class="action-button reply-button">Reply</a>

            </li>
        <?php endforeach; ?>
    </ul>
</div>
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
</body>
</html>
