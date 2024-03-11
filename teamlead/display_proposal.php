<?php
// Set the appropriate content type and headers for a Word document
header("Content-type: application/msword");
header("Content-Disposition: attachment; filename=proposal.doc");

// Start a session (if not already started)
session_start();

// Check if the proposal content is available in the session
if (isset($_SESSION['proposalContent'])) {
    $proposalContent = $_SESSION['proposalContent'];

    // Use output buffering to capture the content
    ob_start();

    // Output the proposal content
    echo $proposalContent;

    // Get the content and clean the output buffer
    $content = ob_get_clean();

    // Send appropriate headers
    header("Content-Length: " . strlen($content));

    // Output the cleaned content
    echo $content;
} else {
    // Handle the case where the content is not available
    // You can redirect or display an error message here
    echo "No proposal content found.";
}
?>