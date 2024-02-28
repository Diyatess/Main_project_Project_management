<?php
session_start();
include('../conn.php');

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "Email not provided in the URL.";
    exit;
}

// Function to get the request ID based on email
function getRequestIdByEmail($conn, $email)
{
    $sql = "SELECT request_id FROM project_requests WHERE client_email = ? AND status = 'Pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($requestId);
    $stmt->fetch();
    $stmt->close();

    return $requestId;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request ID based on the provided email
    $requestId = getRequestIdByEmail($conn, $email);

    if ($requestId) {
        // Check if the approval radio button is selected
        if (isset($_POST['approvalStatus']) && $_POST['approvalStatus'] === 'approve') {
            // Perform the status update in the database for approval
            $updateSql = "UPDATE project_requests SET status = 'Approved' WHERE request_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('i', $requestId);

            if ($updateStmt->execute()) {
                // The status has been updated successfully for approval
                echo '<script>alert("Proposal approval confirmed! Status updated.");</script>';
                echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
            } else {
                // Error in updating the status for approval
                echo '<script>alert("Error updating the status for approval.");</script>';
                echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';
            }

            $updateStmt->close();
        } elseif (isset($_POST['approvalStatus']) && $_POST['approvalStatus'] === 'reject') {
            // Perform the status update in the database for rejection
            $updateSql = "UPDATE project_requests SET status = 'Rejected' WHERE request_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('i', $requestId);

            if ($updateStmt->execute()) {
                // The status has been updated successfully for rejection
                echo '<script>alert("Proposal rejected! Status updated.");</script>';
                echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';

            } else {
                // Error in updating the status for rejection
                echo '<script>alert("Error updating the status for rejection.");</script>';
                echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';

            }

            $updateStmt->close();
        } else {
            // Neither approval nor rejection is selected
            echo '<script>alert("Please select either Approval or Rejection.");</script>';
            echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';

        }
    } else {
        // Request ID not found for the provided email
        echo '<script>alert("Already Approved");</script>';
        echo '<script>window.location.href = "client_dashboard.php?email=' . $email . '";</script>';

    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Client Proposals</title>
    <link rel="stylesheet" href="st.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .proposal {
            margin: 10px 0;
        }

        .proposal h2 {
            font-size: 20px;
            color: #333;
        }

        .status {
            font-weight: bold;
            color: green;
        }

        .download-link {
            color: #0074d9;
            text-decoration: none;
            cursor: pointer;
        }

        /* Style the sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 76px;
            left: -250px;
            background-color: #333;
            overflow-x: hidden;
            transition: 0.5s;
            text-align: left;
            padding-top: 60px;
            color: #fff;
        }

        .sidebar a {
            padding: 8px 16px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
            margin: 15px 0;
        }

        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }

        .openbtn {
            font-size: 30px;
            cursor: pointer;
            position: fixed;
            z-index: 1;
            top: 10px;
            left: 10px;
            color: #00d2fc;
        }

        .icon {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Add a background color for the links */
        .sidebar a {
            background-color: #333;
        }

        /* On hover, the background color and text color change */
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
        }

        /*main */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
           
            background-color: #0056b3;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
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
            /* Center the heading text */
        }

        a {
            text-decoration: none;
            color: #0074D9;
        }

        a.navbar-brand {
            color: black;
            /* Set the text color to black */
            text-decoration: none;
            /* Remove the underline */
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

        .radio-group {
            display: flex;
            /* Adjust the gap as needed */
        }

        .radio-group label {
            margin-right: 5px;
        }
    </style>
    <script>
        let sidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            if (sidebarOpen) {
                sidebar.style.left = "-250px";
            } else {
                sidebar.style.left = "0";
            }
            sidebarOpen = !sidebarOpen;
        }

        function confirmDownload(requestId) {
            const tempIframe = document.createElement('iframe');
            tempIframe.style.display = 'none';

            // Set the iframe source to the download URL
            tempIframe.src = 'download_proposal.php?request_id=' + requestId + '&email=<?php echo $email; ?>';

            // Append the iframe to the document
            document.body.appendChild(tempIframe);

            // Add the confirmation form
            addConfirmationForm(requestId);

            // After a short delay, show a confirmation alert
            setTimeout(function () {
                // Remove the temporary iframe
                document.body.removeChild(tempIframe);

                // Show the confirmation alert
                alert("Proposal download complete");
            }, 1000); // Adjust the delay as needed
        }

        function addConfirmationForm(requestId) {
            // Add the confirmation form dynamically
            const confirmationForm = document.createElement('form');
            confirmationForm.method = 'post';

            const approvalLabel = document.createElement('label');
            approvalLabel.setAttribute('for', 'approvalRadio');
            approvalLabel.textContent = 'Approval';

            const approvalRadio = document.createElement('input');
            approvalRadio.type = 'radio';
            approvalRadio.id = 'approvalRadio';
            approvalRadio.name = 'approvalStatus';
            approvalRadio.value = 'approve';
            approvalRadio.required = true;

            const rejectionLabel = document.createElement('label');
            rejectionLabel.setAttribute('for', 'rejectionRadio');
            rejectionLabel.textContent = 'Rejection';

            const rejectionRadio = document.createElement('input');
            rejectionRadio.type = 'radio';
            rejectionRadio.id = 'rejectionRadio';
            rejectionRadio.name = 'approvalStatus';
            rejectionRadio.value = 'reject';
            rejectionRadio.required = true;

            const confirmationSubmit = document.createElement('input');
            confirmationSubmit.type = 'submit';
            confirmationSubmit.value = 'Submit';

            // Append elements to the form
            confirmationForm.appendChild(approvalLabel);
            confirmationForm.appendChild(approvalRadio);
            confirmationForm.appendChild(rejectionLabel);
            confirmationForm.appendChild(rejectionRadio);
            confirmationForm.appendChild(confirmationSubmit);

            // Append the form to the document
            document.body.appendChild(confirmationForm);
        }

        function showConfirmation() {
            const approvalRadio = document.getElementById('approvalRadio');
            const rejectionRadio = document.getElementById('rejectionRadio');

            if (approvalRadio.checked) {
                const c = confirm("Are you Confirming the proposal?");
                if (c) {
                    alert("Proposal approval confirmed!");
                    // Handle approval logic here
                } else {
                    alert("You chose not to approve the proposal.");
                }
            } else if (rejectionRadio.checked) {
                const c = confirm("Are you Rejecting the proposal?");
                if (c) {
                    alert("Proposal rejected!");
                    // Handle rejection logic here
                } else {
                    alert("You chose not to reject the proposal.");
                }
            } else {
                alert("Please select either Approval or Rejection.");
            }
        }

    </script>
</head>

<body>
    <header>
    <button onclick="window.location.href='client_dashboard.php?email=<?php echo $email; ?>'" type="button" style="float: right;">Back</button>
        <a class="navbar-brand" href="../index.php" style="float: left;">
            <img src="../images/logo.png" alt="" />
            <span> TaskMasters Hub</span>
        </a>
        <!-- Logout button -->
        <h2 style="color: #fff;padding: 26px;">View Proposal</h2>
    </header>
    <div id="mySidebar" class="sidebar">
        <a href="client_dashboard.php?email=<?php echo $email; ?>">Dashboard</a>
        <a href="view_message.php?email=<?php echo $email; ?>">Messages</a>
        <a href="sugg.html">Suggestions/Add-ons</a>

        <a href="client_update.php?email=<?php echo $email; ?>">Update Profile</a>
        <a href="add_project.php?email=<?php echo $email; ?>">Add Project</a>

    </div>

    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    <div class="container">
        <?php
        if (isset($_GET['email'])) {
            $clientEmail = $_GET['email'];

            $sql = "SELECT request_id, project_name FROM project_requests WHERE client_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $clientEmail);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $requestId = $row['request_id'];
                    $projectTitle = $row['project_name'];

                    echo "<div class='proposal'>";
                    echo "<h2>Proposal: $projectTitle</h2>";
                    echo "<p>";
                    echo "<span onclick='confirmDownload($requestId)' class='download-link'>Download Proposal</span>";
                    echo "</p>";

                    // Add the confirmation form with radio buttons
                    echo "<form method='post'>";
                    echo "<div class='radio-group'>";

                    echo "<label for='approvalRadio'>Approval</label>";
                    echo "<input type='radio' id='approvalRadio' name='approvalStatus' value='approve' required>";

                    echo "<label for='rejectionRadio'>Rejection</label>";
                    echo "<input type='radio' id='rejectionRadio' name='approvalStatus' value='reject' required>";

                    echo "</div>";
                    echo "<input type='submit' value='Submit'>";
                    echo "</form>";

                    echo "</div>";
                }
            } else {
                echo "<p>No proposals found for this client.</p>";
            }
        } else {
            echo "<p>Client email not provided.</p>";
        }
        ?>
    </div>
</body>

</html>
