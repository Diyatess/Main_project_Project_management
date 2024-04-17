<?php
include('../conn.php');

// Check if the email ID is provided in the URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Fetch meetings for the user with the provided email ID from the 'meetings' table
    $sqlMeetings = "SELECT m.*,e.email FROM meetings m 
    JOIN employee e ON m.employee_id = e.id
    WHERE email = '$email'";
    $resultMeetings = $conn->query($sqlMeetings);

    if ($resultMeetings === false) {
        // Handle the SQL error, such as redirecting to an error page
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    $meetings = [];

    if ($resultMeetings->num_rows > 0) {
        while ($rowMeeting = $resultMeetings->fetch_assoc()) {
            $meetings[] = $rowMeeting;
        }
    }
} else {
    // Redirect to an error page or handle the case where email is not provided
    header("Location: error.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meetings</title>
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

        /* New styles for the completion date */
        .completion-date {
            font-weight: bold;
            color: #ff0000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
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
    <h1>View Meetings </h1>
</header>
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
        </a>-->
        <a href="view_meeting.php?email=<?php echo $email?>">
            <span class="icon">&#9201;</span> View Meeting
        </a>
        <a href="change_password.php?email=<?php echo $email; ?>">
            <span class="icon">&#128274;</span> Change Password
        </a>
        <a href="view_salary_slip.php?email=<?php echo $email; ?>">
            <span class="icon"></span> Salary Details
        </a>
        <button onclick="logout()" type="button" style="float: right;">Logout</button>
    </div>

    <div class="container">
    <h2>Upcoming Meetings</h2>
    <table>
        <thead>
            <tr>
                <th>Agenda</th>
                <th>Start Time</th>
                <th>Date</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meetings as $meeting): ?>
                <?php if (strtotime($meeting['date'] . ' ' . $meeting['start_time']) > time()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($meeting['agenda']); ?></td>
                        <td><?php echo $meeting['start_time']; ?></td>
                        <td><?php echo $meeting['date']; ?></td>
                        <td><a href="<?php echo $meeting['meeting_link']; ?>" target="_blank"><?php echo parse_url($meeting['meeting_link'], PHP_URL_HOST); ?></a></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Past Meetings</h2>
    <table>
        <thead>
            <tr>
                <th>Agenda</th>
                <th>Start Time</th>
                <th>Date</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meetings as $meeting): ?>
                <?php if (strtotime($meeting['date'] . ' ' . $meeting['start_time']) <= time()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($meeting['agenda']); ?></td>
                        <td><?php echo $meeting['start_time']; ?></td>
                        <td><?php echo $meeting['date']; ?></td>
                        <td><a href="<?php echo $meeting['meeting_link']; ?>" target="_blank"><?php echo parse_url($meeting['meeting_link'], PHP_URL_HOST); ?></a></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
