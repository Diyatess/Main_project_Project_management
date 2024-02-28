<?php
session_start();
include('../conn.php');

// Check if the email is provided in the URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get project name and description from the form
    $projectname = $_POST['projectname'];
    $description = $_POST['description'];

    // Check if the client's email already exists in the project_requests table
    $checkStatusSql = "SELECT * FROM project_requests WHERE client_email = '$email'";
    $result = $conn->query($checkStatusSql);

    if ($result === false) {
        // Handle query error
        echo "Error in query: " . $conn->error;
    } else {
        // Check if there are any existing project requests for the client
        if ($result->num_rows > 0) {
            echo '<script>alert("Invalid request. Already sent a request.");</script>';
        } else {
            // Insert the project request into the project_requests table
            $insertSql = "INSERT INTO project_requests (project_name, project_description, client_email, send_status) VALUES ('$projectname', '$description', '$email', 'Send')";

            if ($conn->query($insertSql) === TRUE) {
                // Get the auto-generated request_id from the last insert
                $requestId = $conn->insert_id;

                // Update the client's record with the request_id
                $updateSql = "UPDATE client SET request_id = '$requestId' WHERE email = '$email'";

                if ($conn->query($updateSql) === TRUE) {
                    echo '<script>alert("Project request submitted successfully!");</script>';
                    echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
                } else {
                    echo "Error updating client record: " . $conn->error;
                }
            } else {
                echo "Error inserting project request: " . $conn->error;
            }
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Project Request</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}

h2 {
    color: #008cba;
}

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    width: 50%;
}

label {
    font-weight: bold;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 8px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button[type="submit"] {
    background-color: #008cba;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}
.edit-button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
</style>
</head>
<body>
    <div class="container">
        <h2>Project Request</h2>
        <form method="POST">
    <!-- Add a hidden input field to store the email -->
    <input type="hidden" name="email" value="<?php echo $email; ?>">
    
    <label for="projectname">Project Name:</label>
    <input type="text" name="projectname" required>

    <label for="description">Project Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <div class="button-container">
        <button type="submit">Submit Request</button>
        <a href="client_dashboard.php?email=<?php echo $email; ?>" style="float: right;">
            <button class="edit-button" type="button">Back</button>
        </a>
    </div>
</form>


    </div>
</body>
</html>


