<?php
session_start();
include('../conn.php');

// Get the email from the URL
if(isset($_GET["email"])) {
    $email = $_GET["email"];
} else {
    echo "Email not provided in the URL.";
    exit; // Exit if email is not provided
}

// Fetch project ID from project_requests table
$sql_project_id = "SELECT request_id FROM project_requests WHERE client_email = ?";
if ($stmt_project_id = $conn->prepare($sql_project_id)) {
    $stmt_project_id->bind_param("s", $email);
    $stmt_project_id->execute();
    $stmt_project_id->bind_result($project_id);
    $stmt_project_id->fetch();
    $stmt_project_id->close();

    if (!$project_id) {
        echo "Project ID not found for the provided email.";
        exit;
    }
} else {
    echo "Error fetching project ID: " . $conn->error;
    exit;
}

// Store the project ID in session
$_SESSION["project_id"] = $project_id;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comments"]) && isset($_POST["rating"])) {
    $comments = $_POST["comments"];
    $rating = $_POST["rating"];

    // Insert feedback into the `feedback_data` table
    $sql_insert_feedback = "INSERT INTO feedback_data (project_id, comments, rating, status) VALUES (?, ?, ?,1)";

    if ($stmt_insert_feedback = $conn->prepare($sql_insert_feedback)) {
        $stmt_insert_feedback->bind_param("isi", $_SESSION["project_id"], $comments, $rating);

        if ($stmt_insert_feedback->execute()) {
            // Feedback inserted successfully
            echo '<script>alert("Feedback submitted successfully!");</script>';
            echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
            exit; // Exit to prevent further execution
        } else {
            // Handle the error in inserting feedback
            echo "Error inserting feedback: " . $stmt_insert_feedback->error;
        }
    } else {
        // Handle the database connection error for feedback insert operation
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
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
    /* Style for labels */
    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    /* Style for text area */
    textarea {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        resize: vertical;
    }

    /* Style for number input */
    input[type="number"] {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box; /* Ensure padding is included in width */
    }

    /* Style for submit button */
    input[type="submit"] {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
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
        <h2>Feedback</h2>
    </header>
    <div class="container">
    <form action="" method="post">
    <label for="comments">Comments:</label>
    <textarea name="comments" id="comments" cols="30" rows="5" required></textarea><br><br>
    <label for="rating">Rating (1-5):</label>
    <input type="number" name="rating" id="rating" min="1" max="5" required><br><br>
    <input type="submit" value="Submit Feedback">
</form>
   </div>
    <?php
    // Close the connection after all operations are completed
    $conn->close();
    ?>
    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <!--<a href="view_messages.php?email=<?php echo $email; ?>" class="red-dot">View Message</a>-->
        <a href="sugg.php?email=<?php echo $email; ?>">Suggestions/Add-ons</a>
        <a href="viewmeeting.php?email=<?php echo $email; ?>">View Meeting</a>
        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>
        <a href="payment.php?email=<?php echo $email; ?>">Make Payment</a>
        
    </div>
</body>
</html>
