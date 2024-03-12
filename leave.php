<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from URL parameter using GET method
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    //echo $email; // Uncomment this line to check if email is correctly retrieved
} else {
    echo "Email not found in URL.";
}

// Assume the form is submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve emp_id based on email
    $sqlEmpId = "SELECT id FROM employee WHERE email = '$email'";
    $resultEmpId = $conn->query($sqlEmpId);

    if ($resultEmpId->num_rows > 0) {
        // Fetch emp_id
        $rowEmpId = $resultEmpId->fetch_assoc();
        $empid = $rowEmpId['id'];

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $reason = $_POST['reason'];

        // Validate input
        if ($start_date >= $end_date) {
            echo '<script>alert("End date must be after start date.")</script>';
        } else {
            // Insert the leave request into the database
            $insert_query = "INSERT INTO leave_requests (empid, start_date, end_date, reason) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isss", $empid, $start_date, $end_date, $reason);
            if ($stmt->execute()) {
                echo '<script>alert("Leave request submitted successfully.")</script>';
            } else {
                echo "Error submitting leave request: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        echo "Employee ID not found.";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reporting</title>
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
        <h2>Apply Leave</h2>
    </header>
    <div class="container">
    <form method="post" action="" style="max-width: 400px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
        <!-- Assume the email is passed as a hidden input field in the form -->
        <input type="hidden" id="email" name="email" value="<?php echo $_GET['email']; ?>">

        <label for="start_date" style="display: block; margin-bottom: 5px;">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label for="end_date" style="display: block; margin-bottom: 5px;">End Date:</label>
        <input type="date" id="end_date" name="end_date" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label for="reason" style="display: block; margin-bottom: 5px;">Reason:</label>
        <textarea id="reason" name="reason" rows="4" cols="50" required style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea><br>

        <input type="submit" value="Submit" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
    </form>
</div>

     <!-- Sidebar --> 
<div id="mySidebar" class="sidebar">
        <a href="mdashboard.php?email=<?php echo $email; ?>">
            <span class="dashboard-icon">📊</span> Dashboard
        </a>
        <!--<a href="view_profile.php?email=<?php echo $email; ?>">
            <span class="icon" style="color: white;">🧑‍💻</span> Edit Profile
        </a>-->
    
        <!--<a href="view_task.php?email=<?php echo $email; ?>">
            <span class="icon">&#128065;</span> View Task
        </a>-->
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
    <script>
        function validateForm() {
            var reportContent = document.getElementById("report_content").value.trim();
            if (reportContent === "") {
                alert("Please enter your daily report.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>