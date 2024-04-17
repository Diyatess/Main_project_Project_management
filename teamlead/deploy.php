<?php
session_start();
include('../conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["request_id"])) {
    $request_id = $_POST["request_id"];

    // Check if a deployment file is uploaded
    if (isset($_FILES["deployment_file"]) && $_FILES["deployment_file"]["error"] == 0) {
        $allowed_extensions = ['zip'];
        $deployment_file_name = $_FILES["deployment_file"]["name"];
        $deployment_file_tmp = $_FILES["deployment_file"]["tmp_name"];
        $deployment_file_extension = pathinfo($deployment_file_name, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array(strtolower($deployment_file_extension), $allowed_extensions)) {
            // Specify the directory to store deployment files
            $deployment_file_destination = "../deployment/" . $deployment_file_name;

            // Move the uploaded deployment file to the destination directory
            if (move_uploaded_file($deployment_file_tmp, $deployment_file_destination)) {
                // Deployment file uploaded successfully, now store its details in the database

                // Insert deployment file details into the `deployment_data` table
                $sql_insert_deployment = "INSERT INTO deployment_data (project_id, file_path) VALUES (?, ?)";

                if ($stmt_insert_deployment = $conn->prepare($sql_insert_deployment)) {
                    $stmt_insert_deployment->bind_param("is", $request_id, $deployment_file_destination);

                    // Check if deployment file details inserted successfully
                    if ($stmt_insert_deployment->execute()) {
                        // Deployment file details inserted successfully
                        echo '<script>alert("Deployment file uploaded successfully!");</script>';
                        
                        // Redirect to the feedback form page
                        echo '<script>window.location.href = "feedback_form.php?project_id=' . $request_id . '";</script>';
                        exit; // Exit to prevent further execution
                    } else {
                        // Handle the error in inserting deployment file details
                        echo "Error inserting deployment file details: " . $stmt_insert_deployment->error;
                    }
                } else {
                    // Handle the database connection error for deployment file insert operation
                    echo "Error: " . $conn->error;
                }
            } else {
                // Handle deployment file upload error
                echo "Error uploading the deployment file.";
            }
        } else {
            // Alert the user that only ZIP files are allowed
            echo '<script>alert("Only ZIP files are allowed.");</script>';
        }
    } else {
        // Alert the user to attach a ZIP file
        echo '<script>alert("Please attach a ZIP file.");</script>';
    }
}

// Fetch projects for dropdown
$sql = "SELECT pr.request_id, pr.project_name, c.cname AS client_name FROM project_requests pr JOIN client c ON pr.client_email = c.email";
$result = mysqli_query($conn, $sql);
$projects = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment</title>
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
    padding: 7px 15px;
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
       /* Unique styles for the form */
       

    /* Unique styles for the buttons */
    #uploadButton {
        background-color: #34ab14;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 6px 22px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    #uploadButton:hover {
        background-color: #3f7631;
    }
    .custom-select {
        padding: 5px;
        font-size: 14px;
        border-radius: 5px;
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
        <h1>Deployment </h1>
    </header>
    <div class="container">
    <form  action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
    <h2>Upload Deployment File</h2><br><br>
    <div>
        <label for="project_id">Select Project:</label>
        <select name="request_id" id="project_id" class="custom-select" required>
            <option value="" selected disabled>Select a project</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?php echo $project['request_id']; ?>"><?php echo $project['project_name'] . ' - ' . $project['client_name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
    </div>
    <div>
        <label for="deployment_file">Deployment File (ZIP only):</label>
        <input type="file" name="deployment_file" id="deployment_file" accept=".zip" required>
    </div><br><br>
    <div>
        <button id="uploadButton" type="submit">Upload Deployment File</button>
    </div>
</form>
    </div>

    <?php
    // Close the connection after all operations are completed
    $conn->close();
    ?>
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
<script>
    function validateForm() {
        var project_id = document.getElementById("project_id").value;
        var fileInput = document.getElementById("deployment_file");
        var file = fileInput.files[0];

        // Check if a project is selected
        if (project_id == "") {
            alert("Please select a project.");
            return false;
        }

        // Check if a file is selected
        if (!file) {
            alert("Please select a file.");
            return false;
        }

        // Check if the file is a ZIP file
        var fileType = file.type;
        if (fileType != "application/zip") {
            alert("Only ZIP files are allowed.");
            return false;
        }

        // Check the file size (optional)
        var maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert("File size exceeds the limit (10MB).");
            return false;
        }

        return true;
    }
</script>
</html>
