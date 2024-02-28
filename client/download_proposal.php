<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection code here
include('../conn.php');

// Fetch the request ID from the URL
if (isset($_GET['request_id'])) {
    $requestId = $_GET['request_id'];

    // Fetch file details from the database
    $sql = "SELECT ca.file_name, ca.file_path FROM client_attachments ca JOIN client c ON ca.client_id = c.id JOIN project_requests pr ON c.email = pr.client_email WHERE pr.request_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $requestId);
        $stmt->execute();
        $stmt->bind_result($fileName, $filePath);
        $stmt->fetch();
        $stmt->close();

        // Check if file details are found
        if ($fileName && $filePath) {
            // Set the appropriate headers for file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
    
            // Read the file and output it for download
            readfile($filePath);
    
            // Display a confirmation message
            echo "<script>alert('Thank you for approving the proposal!');</script>";
    
            // Redirect to the view_proposal.php page
            echo "<script>window.location.href = 'view_proposal.php?email=$email';</script>";
            exit;
        } else {
            echo "File details not found in the database.";
            exit;
        }
    } else {
        echo "Invalid request.";
        exit;
    }
}
?>