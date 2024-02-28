<?php
session_start();
include('../conn.php');

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="st.css"> 
       
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

    <style>
    .red-dot {
    position: relative;
    text-decoration: none; /* Remove the underline */
    color: #007BFF; /* Link text color */
}

.red-dot::before {
    content: '\2022'; /* Unicode character for a bullet (•) */
    position: absolute;
    top: -5px; /* Adjust the vertical position of the dot */
    left: 0;
    color: red; /* Red color for the dot */
}

.red-dot:hover {
    text-decoration: underline; /* Underline on hover */
    color: #0056b3; /* Change link text color on hover */
}
body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
       
        .proposal {
            margin: 10px 0;
        }
        .proposal h2 {
            font-size: 20px;
            color: #333;
        }
        .status {
            font-weight: bold;
            color: green;
        }
        .download-link {
            color: #0074d9;
            text-decoration: none;
        }
    
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
            color: #00d2fc;
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
           

        }
        .dashboard-container {
            display: flex;
            justify-content: center;
            
        }

        .dashboard-box {
            max-width: 600px; /* Adjust the width as needed */
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

    </style>
</head>
<body>
    <header>
    <button onclick="logout()" type="button" style="float: right;">Logout</button>
        <a class="navbar-brand" href="../index.php" style="float: left;">
                <img src="../images/logo.png" alt="" />
                <span> TaskMasters Hub</span>
            </a>
            <h2 style="color: #fff;padding: 26px;">Client Dashboard</h2>
    </header>
    <div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <a href="view_messages.php?email=<?php echo $email; ?>" class="red-dot">View Message</a>
        <a href="sugg.html">Suggestions/Add-ons</a>

        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>
        <a href="payment.php?email=<?php echo $email; ?>">Make Payment</a>
        
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    
    <div id="container" class="dashboard-container">
    <div class="dashboard-box">
        <h2>Welcome to Your Dashboard <?php echo $email; ?>!</h2>
        <p>This is your client dashboard. You can view your project progress, provide suggestions, make payments, and update your profile here.</p>

        <h3>Project Progress</h3>
        <p>Project progress: XX%</p>
        <div class="message-container" id="messageContainer">
            <a href="view_proposal.php?email=<?php echo $email; ?>" class="red-dot">View proposal</a>
            <!--<p><a href="view_messages.php?email=<?php echo $email; ?>" class="red-dot">View Message</a></p>-->
        </div>
    </div>
</div>

</body>

</html>
