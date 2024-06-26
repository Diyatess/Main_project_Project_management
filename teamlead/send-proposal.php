<?php
session_start();
include('../conn.php');

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
} else {
    // Handle the case where request_id is missing
    echo "Request ID is missing.";
    exit;
}

// Check if the proposal has already been sent for this request ID
$sql_check_proposal = "SELECT COUNT(*) FROM client_attachments WHERE request_id = ?";
$stmt_check_proposal = $conn->prepare($sql_check_proposal);
$stmt_check_proposal->bind_param('i', $request_id);
$stmt_check_proposal->execute();
$stmt_check_proposal->bind_result($count);
$stmt_check_proposal->fetch();
$stmt_check_proposal->close();

if ($count > 0) {
    // Proposal has already been sent, show an error message or redirect as needed
    echo '<script>alert("Proposal Already Send!");</script>';
    echo '<script>window.location.href = "tdashboard.php";</script>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["request_id"])) {
    $request_id = $_POST["request_id"];

    // Check if a proposal file is uploaded
    if (isset($_FILES["proposal_file"]) && $_FILES["proposal_file"]["error"] == 0) {
        $allowed_extensions = ['pdf'];
        $file_name = $_FILES["proposal_file"]["name"];
        $file_tmp = $_FILES["proposal_file"]["tmp_name"];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            // Specify the directory to store proposal files
            $file_destination = "../proposal/" . $file_name;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($file_tmp, $file_destination)) {

                // Proposal file uploaded successfully, now store its details in the database

                // Step 1: Retrieve the client_id by joining the project_requests and client tables
                $sql_select_client_id = "SELECT c.id FROM project_requests pr
                                        JOIN client c ON pr.client_email = c.email
                                        WHERE pr.request_id = ?";

                if ($stmt_client_id = $conn->prepare($sql_select_client_id)) {
                    $stmt_client_id->bind_param("i", $request_id);
                    $stmt_client_id->execute();
                    $stmt_client_id->bind_result($client_id);
                    $stmt_client_id->fetch();
                    $stmt_client_id->close();

                    // Step 2: Insert file details into the `client_attachments` table
                    $sql_insert = "INSERT INTO client_attachments (client_id, request_id, file_name, file_path, upload_time) VALUES (?, ?, ?, ?, NOW())";

                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $stmt_insert->bind_param("iiss", $client_id, $request_id, $file_name, $file_destination);

                        if ($stmt_insert->execute()) {
                            // Proposal sent successfully, show success message or redirect
                            echo '<script>alert("Proposal sent successfully!");</script>';
                            echo '<script>window.location.href = "tdashboard.php";</script>';
                            exit;
                        } else {
                            // Handle the error in inserting file details
                            echo "Error inserting file details: " . $stmt_insert->error;
                        }
                    } else {
                        // Handle the database connection error for insert operation
                        echo "Error: " . $conn->error;
                    }
                } else {
                    // Handle the error in retrieving the client_id
                    echo "Error: " . $conn->error;
                }
            } else {
                // Handle file upload error
                echo "Error uploading the proposal file.";
            }
        } else {
            // Alert the user that only PDF files are allowed
            echo '<script>alert("Only PDF files are allowed.");</script>';
        }
    } else {
        // Alert the user to attach a PDF file
        echo '<script>alert("Please attach a PDF file.");</script>';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
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
     <!-- Logout button -->
     <button onclick="logout()" type="button" style="float: right;">Logout</button>
        <h1>Send a Message to Client</h1>
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

<form action="" method="post" enctype="multipart/form-data">
    <h2>Send Proposal</h2>
    <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
    <label for="proposal_file">Proposal File (PDF only):</label>
    <input type="file" name="proposal_file" accept=".pdf" required>
    <br>
    <input type="submit" value="Send Proposal">
</form>
</div>
</body>
</html>
