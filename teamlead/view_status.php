<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the list of projects from project_requests table
$sqlProjects = "SELECT DISTINCT pr.project_name
               FROM project_requests pr
               JOIN task t ON pr.request_id = t.request_id";
$resultProjects = $conn->query($sqlProjects);

if ($resultProjects->num_rows > 0) {
    $projects = [];
    while ($rowProject = $resultProjects->fetch_assoc()) {
        $projects[] = $rowProject['project_name'];
    }
} else {
    $projects = [];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Status</title>
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
        form {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 24px;
            margin: 20px 0;
        }
        p {
            margin: 10px 0;
        }
        strong {
            font-weight: bold;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }
        input[type="submit"] {
            background-color: #0074D9;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056a7;
        }
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
          /* Project Tasks Styles */
    #projectTasksContainer {
        margin-top: 20px;
        
    }

    .project-info {
        background-color: #0074D9;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .project-info h3 {
        margin: 0;
        font-size: 24px;
    }

    .task-list {
        list-style: none;
        padding: 0;
    }

    .task-item {
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
        padding: 15px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .task-description {
        font-size: 18px;
        font-weight: bold;
    }

    .assigned-to {
        margin-top: 10px;
        color: #555;
    }

    .status {
        margin-top: 10px;
        font-weight: bold;
        color: #0074D9;
    }

    .completion-percentage {
        margin-top: 10px;
        font-weight: bold;
        color: #28a745;
    }

    /* Style the project dropdown */
    #projectDropdown {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
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
<header>
<a class="navbar-brand" href="../index.php" style="float: left;">
        <img src="../images/logo.png" alt="" />
        <span> TaskMasters Hub</span>
    </a>

    <!-- Logout button -->
 <button onclick="logout()" type="button" style="float: right;">Logout</button>
        <h1>View Tasks and Status</h1>
    </header>
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
        <h2>View Task Status</h2>

        <label for="projectDropdown">Select a Project:</label>
<select id="projectDropdown" onchange="fetchProjectTasks()">
    <option value="" selected disabled>Select a project</option>
    <?php foreach ($projects as $project): ?>
        <option value="<?php echo $project; ?>"><?php echo $project; ?></option>
    <?php endforeach; ?>
</select>

<div id="projectTasksContainer"></div>

        <script>
            function fetchProjectTasks() {
                var projectDropdown = document.getElementById('projectDropdown');
                var selectedProject = projectDropdown.value;

                // Fetch tasks for the selected project
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'fetch_project_tasks.php?project=' + selectedProject, true);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Update the projectTasksContainer with the fetched tasks
                        document.getElementById('projectTasksContainer').innerHTML = xhr.responseText;
                    } else {
                        console.error('Error fetching project tasks');
                    }
                };

                xhr.onerror = function () {
                    console.error('Network error while fetching project tasks');
                };

                xhr.send();
            }
        </script>
    </div>
</body>
</html>