<?php
session_start();
include('../conn.php');


if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
}

// Fetch messages from the "messages" table based on the provided email
$sql = "SELECT m.message_text 
        FROM messages AS m
        INNER JOIN client AS c ON m.recipient_id = c.id
        WHERE c.email = '$email'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Messages found, display them
    $messages = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row['message_text'];
    }
} else {
    $messages = array("No messages found.");
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="st.css">
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Add or modify styles as needed */
        .main-content {
            max-width: 1200px; /* Adjust the maximum width as needed */
            margin-left: 317px;
            margin-right: 220px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

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

        h1 {
            font-size: 32px;
            color: #fff;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
           
            background-color: #0056b3;
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
        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 24px;
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
    </script>
</head>

<body>
    
    <header>
    <a class="navbar-brand" href="../index.php" style="float: left;">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span>
        </a>
    <button onclick="window.location.href='client_dashboard.php?email=<?php echo $email; ?>'" type="button" style="float: right;">Back</button>
        <h1>Messages</h1>
    </header>
    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <a href="view_message.php?email=<?php echo $email; ?>">Messages</a>
        <a href="sugg.html">Suggestions/Add-ons</a>

        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>
        
    </div>
    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    
    <div class="main-content">
        <h3>Messages</h3>
        <div class="message-container">
            <?php
            $isSender = true; // To alternate between sender and receiver styles

            foreach ($messages as $message) {
                // Toggle between sender and receiver styles
                $messageClass = $isSender ? 'sender' : 'receiver';

                echo "<div class='message $messageClass'>$message</div>";

                // Toggle the sender/receiver for the next message
                $isSender = !$isSender;
            }
            ?>
        </div>
    </div>
<script src="scripts.js"></script> <!-- Include JavaScript for interactive features -->
</body>
</html>
