<?php
// Set the appropriate content type and headers for a Word document
header("Content-type: application/msword");
header("Content-Disposition: attachment; filename=coste-generate.doc");

// Start a session (if not already started)
session_start();

// Check if the proposal content is available in the session
if (isset($_SESSION['proposalContent'])) {
    $proposalContent = $_SESSION['proposalContent'];

    // Strip HTML tags and display the proposal content
    $plainTextContent = strip_tags($proposalContent);
    echo $plainTextContent;
} else {
    // Output a message indicating that the content is not found
    echo "No proposal content found.";
    exit; // Stop further execution
}
?>
