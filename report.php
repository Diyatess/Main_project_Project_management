<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch unique dates for daily reports
$sqlDates = "SELECT DISTINCT report_date FROM daily_reports ORDER BY report_date DESC";
$resultDates = $conn->query($sqlDates);

// Initialize variables
$selected_date = null;
$report_data = "";

// Check if date is selected
if (isset($_POST['selected_date'])) {
    $selected_date = $_POST['selected_date'];

    if ($selected_date !== "") {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT employee.fname, employee.lname, daily_reports.report_content FROM daily_reports JOIN employee ON daily_reports.emp_id = employee.id WHERE daily_reports.report_date = ? ORDER BY employee.fname ASC");
        $stmt->bind_param("s", $selected_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Display the selected date at the top of the table
        $report_data .= "<h2>Reports for " . date("M d, Y", strtotime($selected_date)) . "</h2>";

            // Display the daily reports in tabular format
            $report_data .= "<table border='1'>";
            $report_data .= "<tr><th>Employee Name</th><th>Daily Report</th></tr>";
            while ($row = $result->fetch_assoc()) {
                $report_data .= "<tr>";
                $report_data .= "<td>" . $row['fname'] . " " . $row['lname'] . "</td>";
                $report_data .= "<td>" . $row['report_content'] . "</td>";
                $report_data .= "</tr>";
            }
            $report_data .= "</table>";
        } else {
            $report_data = "<p>No daily reports found for selected date.</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        $report_data = "<p>Please select a date.</p>";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Daily Reports</title>
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
            color: #000;
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
        /* Container styles */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Date form styles */
        #dateForm {
            margin-bottom: 20px;
        }

        #selected_date {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 30%;
            font-size: 16px;
            margin-bottom: 10px;
        }

        #selected_date:focus {
            outline: none;
            border-color: #007bff;
        }

        input[type="submit"] {
            display: inline-block;
            background-color: #000;
            border: none;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #3b003b;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

    </style>
    <script>
        // Function to disable future dates
        function disableFutureDates() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("selected_date").setAttribute('max', today);
        }

        // Function to validate date input
        function validateDate() {
            var selectedDate = document.getElementById("selected_date").value;
            var today = new Date().toISOString().split('T')[0];

            if (selectedDate > today) {
                alert("Please select a date on or before today.");
                return false;
            }

            return true;
        }
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
<body onload="disableFutureDates()">
<a class="navbar-brand" href="../index.php" style="float: left;">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span>
        </a>
    <header>
        <!-- Logout button -->
        <a href="tdashboard.php" class="back-button" style="float: right;">Back</a>        <h2>Daily Report</h2>
    </header>
    <div id="mySidebar" class="sidebar">
        <a href="tdashboard.php">
            <span class="dashboard-icon">📊</span> Dashboard
        </a>
        <!-- <a href="edit_profile.php">
            <span class="icon">&#9998;</span> Edit Profile
        </a>-->
        <a href="add_task.php">
            <span class="icon">&#10010;</span> Add Task
        </a>
        <a href="view_status.php">
            <span class="icon">&#128196;</span> View Status
        </a>
        <a href="schedule_meeting.php">
            <span class="icon">&#9201;</span> Schedule Meeting
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
       
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    <div class="container">

        <h2>Select a Date:</h2>
        <form id="dateForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="date" id="selected_date" name="selected_date"><br><br>
            <input type="submit" value="Get Daily Reports">    
        </form>

        <div id="dailyReports">
            <br>
            <?php echo $report_data; ?>
        </div>
    </div>
</body>
</html>
