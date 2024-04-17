<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
}

// Assuming your tables are named 'employee' and 'designation'
$sqlUserInfo = "SELECT e.*, d.desig_type FROM employee e
                JOIN designation d ON e.desig_id = d.desig_id
                WHERE e.email = '$email'";
$resultUserInfo = $conn->query($sqlUserInfo);

if ($resultUserInfo->num_rows > 0) {
    $rowUserInfo = $resultUserInfo->fetch_assoc();
    $fname = $rowUserInfo['fname'];
    $lname = $rowUserInfo['lname'];
    $desig_name = $rowUserInfo['desig_type'];
    $email = $rowUserInfo['email'];
    // Add more details as needed
} else {
    $fname = "Unknown";
    $lname = "";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            content: "👤";
            font-size: 50px;
            line-height: 100px;
            text-align: center;
            display: block;
        }

        .profile-details {
            color: #333;
        }

        h2, h3 {
            margin: 0;
        }

        p {
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: #fff;
        }

        .edit-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50; /* Green color */
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Display user information -->
        <div class="profile-info">
            <div class="profile-picture"></div>
            <div class="profile-details">
                <h2>Profile Information</h2>
                <h3><?php echo $fname . " " . $lname; ?></h3>
                <p>Email: <?php echo $email; ?></p>
                <p>Designation: <?php echo $desig_name; ?></p>
                <!-- Add more details here -->
                <a href="edit_profile.php?email=<?php echo $email; ?>" class="edit-btn">Edit Profile</a>
            </div>
        </div>
    </div>
</body>
</html>
