
<?php
include('../conn.php');

// Initialize variables
$agenda = $startTime = $date = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $project_id=$_POST['request_id'];
    // Sanitize and validate form data
    $agenda = isset($_POST['agenda']) ? htmlspecialchars($_POST['agenda']) : '';
    $startTime = isset($_POST['start_time']) ? htmlspecialchars($_POST['start_time']) : '';
    $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '';

    // Get selected employees
    $selectedEmployees = isset($_POST['employees']) ? $_POST['employees'] : [];

    // Insert the meeting schedule into the 'meetings' table for each selected employee
    foreach ($selectedEmployees as $employee_id) {
        $sql = "INSERT INTO meetings (agenda, employee_id, start_time, date)
                VALUES ('$agenda', $employee_id, '$startTime', '$date')";

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

   // echo '<script>alert("Meeting scheduled successfully!");</script>';
}
// Fetch employees and their designations from the 'employee' and 'designation' tables
$sqlEmployees = "SELECT e.id, e.fname, d.desig_type
                 FROM employee e 
                 JOIN designation d ON e.desig_id = d.desig_id 
                 WHERE e.id IN (SELECT DISTINCT pa.assigned_to FROM task pa WHERE pa.request_id = '$project_id')
                 GROUP BY e.id";

$resultEmployees = $conn->query($sqlEmployees);
$employees = [];

if ($resultEmployees->num_rows > 0) {
    while ($rowEmployee = $resultEmployees->fetch_assoc()) {
        $employees[] = $rowEmployee;
    }
}
?>

<!DOCTYPE html>
<html lang="en">    
<head>
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

    input[type="text"],
    input[type="time"],
    input[type="date"] {
        width: calc(100% - 24px);
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    input[type="checkbox"] {
        margin-right: 8px;
    }

    button[type="submit"] {
        background-color: #0074D9;
        color: #fff;
        border: none;
        padding: 7px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #0056a7;
    }

    /* Sidebar styles */
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
    padding: 6px 5px;
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

    .back-button:hover {
        background-color: #777;
    }
     /* Custom styling for the checkbox container */
        .checkbox-list {
            display: flex;
            flex-wrap: wrap;
        }
        .checkbox-container {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px; /* Added margin bottom for spacing */
        }
        .checkbox-container label {
            display: flex; /* Align checkbox and text vertically */
            align-items: center;
            margin-bottom: 5px; /* Added margin bottom for spacing */
        }
      
       /* Custom styling for the checkbox */
       .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 20px;
            height: 20px;
            vertical-align: middle;
            cursor: pointer;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .custom-checkbox input:checked ~ .checkmark:after {
            content: "";
            position: absolute;
            display: block;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
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
    <form action="" method="post">
        <input type="hidden" name="request_id" value="<?php echo $_POST['request_id']; ?>">

        <label for="agenda">Agenda:</label>
        <input type="text" name="agenda" id="agenda" required><br><br>

        <label>Employees:</label><br>
        <?php foreach ($employees as $employee): ?>
            <div class="checkbox-container">
                <label for="employee_<?php echo $employee['id']; ?>">
                    <?php echo $employee['fname'] . '(' . $employee['desig_type'].')'; ?>
                    <input type="checkbox" name="employees[]" id="employee_<?php echo $employee['id']; ?>" value="<?php echo $employee['id']; ?>">
                    <span class="checkmark"></span>
                </label>
            </div>
        <?php endforeach; ?>
        <br>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" id="start_time" required><br><br>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required><br><br>

        <button type="submit">Schedule Meeting</button>
    </form>
    </div>

</body>
</html>
