<?php
session_start();
include('../conn.php');

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    echo $email;
} else {
    echo "Email not provided in the URL.";
    exit; // Exit if email is not provided
}

// Get the deployment file path
$sql = "SELECT dd.file_path
        FROM deployment_data dd
        JOIN project_requests pr ON dd.project_id = pr.request_id
        WHERE pr.client_email = ?"; 
echo $sql;


if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path) {
        // Set headers for file download
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . basename($file_path));
        header("Content-Length: " . filesize($file_path));

        // Send the file for download
        readfile($file_path);
        exit;
    } else {
        echo "Deployment file not found.";
        exit;
    }
} else {
    echo "Error preparing SQL statement: " . $conn->error;
    exit;
}

mysqli_close($conn);
?>
