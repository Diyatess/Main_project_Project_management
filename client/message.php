<?php
session_start();

// Check if the user is authenticated as a team lead


include('../conn.php'); // Include your database connection code

// Extract request_id from the URL
if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Fetch associated request details and client information from the database
    $sql = "SELECT pr.project_name, pr.project_description, c.cname, c.email 
            FROM project_requests pr
            JOIN client c ON pr.client_email = c.email
            WHERE pr.request_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($projectName, $projectDescription, $clientName, $clientEmail);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Handle the database connection error
        echo "Error: " . $conn->error;
    }
} else {
    // Handle the case where request_id is not provided
    echo "Request ID is missing.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
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
            text-align: center;
        }
        form {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 24px;
            margin: 20px 0;
        }
        p {
            margin: 10px 0;
        }
        strong {
            font-weight: bold;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }
        input[type="submit"] {
            background-color: #0074D9;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056a7;
        }
    </style>
</head>
<body>
    <header>
        <h1>Send a Message to Client</h1>
    </header>
    <div class="container">
        <form action="send_message.php" method="post">
            <h2>Client Information</h2>
            <p><strong>Client Name:</strong> <?php echo $clientName; ?></p>
            <p><strong>Client Email:</strong> <?php echo $clientEmail; ?></p>
            <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
            <label for="message">Message:</label>
            <textarea name="message" rows="4" cols="50" required></textarea>
            <br>
            <input type="submit" value="Send Message">
        </form>
    </div>
</body>
</html>
