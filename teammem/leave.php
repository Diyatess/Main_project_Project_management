
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
        $leave_type = $_POST['leave_type'];
        if ($leave_type === 'Other') {
            $leave_type = $_POST['other_leave_type'];
        }
        $reason = $_POST['reason'];

        // Validate input
        if ($start_date >= $end_date) {
            echo '<script>alert("End date must be after start date.")</script>';
        } else {
            // Insert the leave request into the database
            $insert_query = "INSERT INTO leave_requests (empid, start_date, end_date, leave_type, reason) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("issss", $empid, $start_date, $end_date, $leave_type, $reason);
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
    <title>Apply leave</title>
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

        <label for="leave_type" style="display: block; margin-bottom: 5px;">Leave Type:</label>
        <select id="leave_type" name="leave_type" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
            <option value="PL">Privilege Leave (PL)</option>
            <option value="CL">Casual Leave (CL)</option>
            <option value="SL">Sick Leave (SL)</option>
            <option value="ML">Maternity Leave (ML)</option>
            <option value="Comp-off">Compensatory Off (Comp-off)</option>
            <option value="Marriage Leave">Marriage Leave</option>
            <option value="Paternity Leave">Paternity Leave</option>
            <option value="Bereavement Leave">Bereavement Leave</option>
            <option value="LOP/LWP">Loss of Pay (LOP) / Leave Without Pay (LWP)</option>
            <option value="Other">Other</option>
        </select>

        <div id="other_leave_type_input" style="display: none;">
            <label for="other_leave_type" style="display: block; margin-bottom: 5px;">Other Leave Type:</label>
            <input type="text" id="other_leave_type" name="other_leave_type" style="width: 100%; padding: 8px; margin-bottom: 10px;">
        </div>

        <label for="reason" style="display: block; margin-bottom: 5px;">Reason:</label>
        <textarea id="reason" name="reason" rows="4" cols="50" required style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea><br>

        <input type="submit" value="Submit" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
    </form>
    </div>
    <!-- JavaScript to show/hide the "Other Leave Type" input -->
    <script>
        document.getElementById("leave_type").addEventListener("change", function() {
            var otherLeaveTypeInput = document.getElementById("other_leave_type_input");
            if (this.value === "Other") {
                otherLeaveTypeInput.style.display = "block";
            } else {
                otherLeaveTypeInput.style.display = "none";
            }
        });
    </script>


     <!-- Sidebar --> 
     <div id="mySidebar" class="sidebar">
        <a href="mdashboard.php?email=<?php echo $email; ?>">
            <span class="dashboard-icon">📊</span> Dashboard
        </a>
        <a href="leave.php?email=<?php echo $email; ?>&action=apply">Apply Leave</a>
        <a href="view_leavestatus.php?email=<?php echo $email; ?>&action=view">View Approved/Rejected Leave</a>
        <a href="attendance.php?email=<?php echo $email; ?>">
            <span class="icon"></span> Mark Attendance
        </a>
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