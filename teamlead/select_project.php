<?php
include('../conn.php');

// Fetch projects from the 'projects' table
$sqlProjects = "SELECT * FROM project_requests where status='Approved'";
$resultProjects = $conn->query($sqlProjects);
$projects = [];

if ($resultProjects->num_rows > 0) {
    while ($rowProject = $resultProjects->fetch_assoc()) {
        $projects[] = $rowProject;
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    // Redirect to schedule_meeting.php only if request_id matches
    if ($_POST['request_id'] == $_GET['request_id']) {
        header("Location: schedule_meeting.php?request_id={$_POST['request_id']}");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Project</title>
    <style>
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
        text-align: center;
        margin-bottom: 20px;
    }

    form {
        padding: 20px;
        border-radius: 8px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
    }

    input[type="submit"] {
        background-color: #0074D9;
        color: #fff;
        border: none;
        padding: 7px 10px;
        border-radius: 5px;
        cursor: pointer;
    } 
    input[type="submit"]:hover {
        background-color: #0056a7;
    }

    /* Sidebar styles */
    .sidebar {
    height: 100%;
    width: 251px;
    position: fixed;
    top: 70px;
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
    padding: 6px 13px;
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* On hover, the background color and text color change */
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
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

    .icon {
        margin-right: 10px;
        font-size: 20px;
    }

    /* Back button style */
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
    .custom-select {
        padding: 5px;
        font-size: 16px;
        border-radius: 5px;
    }

    .back-button:hover {
        background-color: #777;
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
        <span> TaskMasters Hub</span>
    </a>
<header>
     <!-- Logout button -->
     <button onclick="logout()" type="button" style="float: right;">Logout</button>
    <h1>Schedule Meeting</h1>
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

    <form action="schedule_meeting.php" method="post">
    <label for="project_id" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;"> Select Project:</label>
    <select name="request_id" class="custom-select">
        <?php foreach ($projects as $project): ?>
            <option value="<?php echo $project['request_id']; ?>"><?php echo htmlspecialchars($project['project_name']); ?></option>
        <?php endforeach; ?>
    </select><br><br>
        <input type="submit" value="Select Project">
    </form>
</body>
</html>
