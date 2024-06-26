<?php
session_start();
include('../conn.php');

// Check if the email is provided in the URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get project name and description from the form
    $projectname = $_POST['projectname'];
    $description = $_POST['description'];

    // Check if the client's email already exists in the project_requests table
    $checkStatusSql = "SELECT * FROM project_requests WHERE client_email = '$email'";
    $result = $conn->query($checkStatusSql);

    if ($result === false) {
        // Handle query error
        echo "Error in query: " . $conn->error;
    } else {
        // Check if there are any existing project requests for the client
        if ($result->num_rows > 0) {
            echo '<script>alert("Invalid request. Already sent a request.");</script>';
        } else {
            // Insert the project request into the project_requests table
            $insertSql = "INSERT INTO project_requests (project_name, project_description, client_email, send_status) VALUES ('$projectname', '$description', '$email', 'Send')";

            if ($conn->query($insertSql) === TRUE) {
                // Get the auto-generated request_id from the last insert
                $requestId = $conn->insert_id;

                // Update the client's record with the request_id
                $updateSql = "UPDATE client SET request_id = '$requestId' WHERE email = '$email'";

                if ($conn->query($updateSql) === TRUE) {
                    echo '<script>alert("Project request submitted successfully!");</script>';
                    echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
                } else {
                    echo "Error updating client record: " . $conn->error;
                }
            } else {
                echo "Error inserting project request: " . $conn->error;
            }
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Project Request</title>
    <style>
   /* Style the sidebar */
   .sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 100px;
    left: 0;
    background-color: #333;
    overflow-x: hidden;
    transition: 0.5s;
    text-align: left;
    padding-top: 10px;
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

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    width: 50%;
}

label {
    font-weight: bold;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 8px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button[type="submit"] {
    background-color: #008cba;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}
.edit-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
</style>
</head>
<body>
<header>
    <a href="client_dashboard.php?email=<?php echo $email; ?>" style="float: right;"><button class="edit-button" type="button">Back</button></a>
    <a class="navbar-brand" href="../index.php" style="float: left;"><img src="../images/logo.png" alt="" /><span> TaskMasters Hub</span></a>
    <h2>Project Request</h2>
</header>
<div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <!--<a href="view_messages.php?email=<?php echo $email; ?>" class="red-dot">View Message</a>-->
        <a href="sugg.php?email=<?php echo $email; ?>">Suggestions/Add-ons</a>
        <a href="viewmeeting.php?email=<?php echo $email; ?>">View Meeting</a>
        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>
        <a href="payment.php?email=<?php echo $email; ?>">Make Payment</a>
        
    </div>
<div class="container">
       
    <!-- Add a hidden input field to store the email -->
    <input type="hidden" name="email" value="<?php echo $email; ?>">
    
    <label for="projectname">Project Name:</label>
    <input type="text" name="projectname" required>

    <label for="description">Project Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <div class="button-container">
        <button type="submit">Submit Request</button>
    </div>
    
    </div>
</body>
</html>


