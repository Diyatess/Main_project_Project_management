<?php
session_start();

include('../conn.php');

// Extract request_id from the URL
if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update the status of the request to "Approved" in the database
        $updateStatusSql = "UPDATE project_requests SET status = 'Approved' WHERE request_id = ?";

        if ($stmt = $conn->prepare($updateStatusSql)) {
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Handle the database connection error
            echo "Error: " . $conn->error;
            exit; // Exit on error
        }
    }

    // Fetch associated request details and client information from the database
    $sql = "SELECT project_name, project_description FROM project_requests WHERE request_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($projectName, $projectDescription); // Correct variable name
        $stmt->fetch();
        $stmt->close();
    } else {
        // Handle the database connection error
        echo "Error: " . $conn->error;
        exit; // Exit on error
    }
} else {
    // Handle the case where request_id is not provided
    echo "Request ID is missing.";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Project Proposal</title>
    <style>
         <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin: -14px;
        }
        h1 {
            font-size: 24px;
        }
        .container {
            max-width: 800px;
            margin: 14px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #555;
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
        /*Send proposal link */
        .send-proposal-link {
        display: inline-block;
        padding: 10px 20px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
        float: right
    }

    .send-proposal-link:hover {
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

function validateField(fieldId, errorMessage) {
    var field = document.getElementById(fieldId);
    var value = field.value.trim();
    var messageElement = document.getElementById(fieldId + "_message");

    if (fieldId === "project_title" || fieldId === "project_description" || fieldId === "modules") {
        if (value === "") {
            messageElement.textContent = errorMessage;
            messageElement.style.display = "block";
            return false;
        } else {
            messageElement.style.display = "none";
            return true;
        }
    }
}

function validateForm() {
    var isValid = true;
    isValid = validateField("project_title", "Project Title is required") && isValid;
    isValid = validateField("project_description", "Project Description is required") && isValid;
    isValid = validateField("modules", "Modules are required") && isValid;
   if (isValid) {
        // Form is valid, proceed with submission
        document.getElementById("yourForm").submit();
    } 
}
</script>
</head>
   
<body>
    <header>
 <!-- Logout button -->
        <button onclick="logout()" type="button" style="float: right;">Logout</button>        
        <h1>Create a Software Project Proposal Draft</h1>
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

    <form method="post" action="generate-proposal.php" onsubmit="return validateForm()">
        <label for="project_title">Project Title:</label>
        <input type="text" name="project_title" id="project_title" required value="<?php echo $projectName; ?>">
        <p id="project_title_message" style="display:none; color:red;"></p>

        <label for="project_description">Project Description:</label>
        <input type="text" name="project_description" id="project_description"  value="<?php echo $projectDescription; ?>" required>
        <p id="project_description_message" style="display:none; color:red;"></p>

        <label for="modules">Modules:</label>
        <input type="text" name="modules" id="modules" required>
        <p id="modules_message" style="display:none; color:red;"></p>

        <input type="submit" value="Generate Proposal">
    </form>
        
    </div>
</body>
</html>