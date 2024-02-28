<?php
session_start();
include('../conn.php'); // Include your database connection code

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"]) && isset($_POST["sender_id"])) {
    $message = $_POST["message"];
    $request_id = $_POST["sender_id"];

    // Prepare and execute an SQL INSERT statement to store the message
    $sql = "INSERT INTO messages (sender_id, recipient_id, message_text) 
        VALUES ((SELECT id FROM client WHERE email = 
        (SELECT client_email FROM project_requests WHERE sender_id = ?)), '1', ?)";

    $team_lead_user_id = '1';

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $sender_id, $team_lead_user_id, $message);

        if ($stmt->execute()) {
            // Message sent successfully, redirect to a success page
            header("Location: client_dashboard.php");
            exit;
        } else {
            // Handle the error (e.g., display an error message)
            echo "Error: " . $stmt->error;
        }
    } else {
        // Handle the database connection error
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>

<script>
    // Check if the URL contains a success parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        // Display a success alert
        alert("Message sent successfully!");
    }
</script>

