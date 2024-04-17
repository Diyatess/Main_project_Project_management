<?php
session_start();
include('../conn.php');

// Check if the email is provided in the URL
if (isset($_GET['email'])) {
    $client_email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get suggestion from the form
    $suggestion = $_POST['suggestion'];

    // Check if the client's email already exists in the client table
    $checkClientSql = "SELECT * FROM client WHERE email = '$client_email'";
    $result = $conn->query($checkClientSql);

    if ($result->num_rows > 0) {
        // Client email exists, insert into the suggestions table
        $insertSql = "INSERT INTO suggestions (client_email, suggestion) VALUES ('$client_email', '$suggestion')";
        if ($conn->query($insertSql) === TRUE) {
            echo "Suggestion added successfully";
        } else {
            echo "Error inserting suggestion: " . $conn->error;
        }
    } else {
        // Client email does not exist, handle the error
        echo "Client email does not exist";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Suggestion/Add-on</title>
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
      /* Style the sidebar */
      .sidebar {
       height: 100%;
       width: 250px;
       position: fixed;
       top: 122px;
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
</head>
<body>
<header>
    <button onclick="logout()" type="button" style="float: right;">Logout</button>
        <a class="navbar-brand" href="../index.php" style="float: left;">
                <img src="../images/logo.png" alt="" />
                <span> TaskMasters Hub</span>
            </a>
            <h2 style="color: #fff;padding: 26px;"></h2>Add Suggestion/Add-on
    </header>
    <div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $client_email; ?>">Dashboard</a>
        <!--<a href="view_messages.php?email=<?php echo $client_email; ?>" class="red-dot">View Message</a>-->
        <a href="sugg.php?email=<?php echo $client_email; ?>">Suggestions/Add-ons</a>
        <a href="viewmeeting.php?email=<?php echo $client_email; ?>">View Meeting</a>
        <a href="client_update.php?email=<?php echo $client_email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $client_email; ?>">Add Project</a>
        <a href="payment.php?email=<?php echo $client_email; ?>">Make Payment</a>
        
    </div>
    <div class="container">
        <form  method="post">
            <label for="suggestion">Your Suggestion/Add-on:</label><br>
            <textarea id="suggestion" name="suggestion" rows="4" cols="50" required></textarea><br>
            <input type="hidden" name="email" value="<?php echo $client_email; ?>">
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
