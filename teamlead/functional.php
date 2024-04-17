<?php
session_start();

include('../conn.php');
// Function to calculate Total Months
function calculateTotalMonths($functionPoints, $teamSize, $averageProductivity)
{
    if ($teamSize != 0 && $averageProductivity != 0) {
        $totalMonths = $functionPoints / ($teamSize * $averageProductivity);
        return $totalMonths;
    } else {
        return "Invalid input. Team size and average productivity must be non-zero.";
    }
}

// Function to calculate Total Cost
function calculateTotalCost($hourlyRate, $effort, $totalMonths, $exchangeRate)
{
    return $hourlyRate * $effort * $totalMonths * $exchangeRate;
}

// Function to calculate Functional Points
function calculateFunctionalPoints($ei, $eo, $eq)
{
    $totalEi = count(explode(",", $ei));
    $totalEo = count(explode(",", $eo));
    $totalEq = count(explode(",", $eq));

    return $totalEi + $totalEo + $totalEq;
}


// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $teamSize = $_POST["teamSize"];
    $averageProductivity = $_POST["averageProductivity"];
    $hourlyRate = $_POST["hourlyRate"];
    $effort = 5.2;
    $externalInputs = $_POST["externalInputs"];
    $externalOutputs = $_POST["externalOutputs"];
    $externalInquiries = $_POST["externalInquiries"];

    // Set the exchange rate (1 USD to INR)
    $exchangeRate = 75.0; // Replace with the actual exchange rate

    // Calculate Functional Points
    $functionalPoints = calculateFunctionalPoints($externalInputs, $externalOutputs, $externalInquiries);

    // Calculate total months
    $totalMonths = calculateTotalMonths($functionalPoints, $teamSize, $averageProductivity);

    // Calculate total cost in Indian Rupees
    $totalCostINR = calculateTotalCost($hourlyRate, $effort, $totalMonths, $exchangeRate);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $postdata = array(
        "model" => "gpt-3.5-turbo",
        "messages" => [
            array(
                "role" => "system",
                "content" => "create a timeline and cost estimation for software proposal if total months is " . round($totalMonths) . " and total cost is ₹" . number_format($totalCostINR, 2) . " Indian rupees"
            ),

        ],
        "temperature" => 0.7, // Adjust temperature for creativity
        "max_tokens" => 1500, // Limit the proposal length
    );
    $postdata = json_encode($postdata);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Bearer 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'; // Replace with your OpenAI API key

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }
    curl_close($ch);
    $result = json_decode($result, true);

    // Extract the proposal content from the response
    $proposalContent = $result['choices'][0]['message']['content'];

    // Store the proposal content in a session variable
    $_SESSION['proposalContent'] = $proposalContent;

    // Redirect to the second page to display the content
    header('Location: cost_generate.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Total Months</title>
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

    h1 {
        font-size: 32px;
        color: #fff;
        text-align: center;
        margin-bottom: 20px;
    }

    form {
        padding: 20px;
        border-radius: 8px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
    }

    input[type="text"],
    input[type="time"],
    input[type="number"],
    input[type="date"] {
        width: calc(100% - 24px);
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    button[type="submit"] {
        background-color: #0074D9;
        color: #fff;
        border: none;
        padding: 7px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #0056a7;
    }

    /* Sidebar styles */
    .sidebar {
    height: 100%;
    width: 250px;
    position: fixed;
    top: 76px;
    left: 0;
    background-color: #333;
    overflow-x: hidden;
    transition: 0.5s;
    text-align: left;
    padding-top: 60px;
    color: #fff;
    z-index: 1;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add a box shadow for depth */
}

/* Sidebar links */
.sidebar a {
    padding: 9px 9px;
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
    max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* On hover, the background color and text color change */
        .sidebar a:hover {
            background-color: #00D2FC;
            color: #fff;
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
        button{
            block-size: 27px;
            background: white;
            border: black;
            font-style: revert-layer;
        }

    .icon {
        margin-right: 10px;
        font-size: 20px;
    }

    /* Back button style */
    .back-button {
        background-color: #555;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .back-button:hover {
        background-color: #777;
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
 <button onclick="logout()" type="button" style="float: right;">Logout</button>        <h1>Calculate Total Months</h1>
    </header>
   <!-- Sidebar -->
   <div id="mySidebar" class="sidebar">
    <a href="tdashboard.php">   
        <span class="dashboard-icon">📊</span>Dashboard
    </a>
    <a href="add_task.php">
        <span class="icon">&#10010;</span>Add Task
    </a>
    <a href="view_status.php">
        <span class="icon">&#128196;</span>View Status
    </a>
    <a href="select_project.php">
            <span class="icon">&#9201;</span>Daily Standup Meeting
    </a>
    <a href="client_meeting.php">
            <span class="icon">&#9201;</span>Sprint Review
    </a>
    <a href="report.php">
        <span class="icon">&#128221;</span>Monitor Daily Progress
    </a>
    <a href="view_projects.php">
        <span class="icon">&#128213;</span>View Approved/Denied Projects
    </a>
    <a href="functional.php">
        <span class="icon">📏</span>Calculate Functional Point
    </a>
    <a href="view_leave.php">
        <span class="icon"></span>View Leave
    </a>
    <a href="salary.php">
        <span class="icon"></span>Salary
    </a>
    <a href="deploy.php">
        <span class="icon"></span>Deploy Project
    </a>
    </div>
    <div class="container">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return validateForm()">
    <label for="externalInputs">External Inputs (comma-separated):</label>
    <input type="text" name="externalInputs" id="externalInputs" placeholder="e.g., input1, input2" required>
    <div id="externalInputsError" class="error-message"></div>

    <label for="externalOutputs">External Outputs (comma-separated):</label>
    <input type="text" name="externalOutputs" id="externalOutputs" placeholder="e.g., output1, output2" required>
    <div id="externalOutputsError" class="error-message"></div>

    <label for="externalInquiries">External Inquiries (comma-separated):</label>
    <input type="text" name="externalInquiries" id="externalInquiries" placeholder="e.g., inquiry1, inquiry2" required>
    <div id="externalInquiriesError" class="error-message"></div>

    <label for="teamSize">Team Size:</label>
    <input type="number" name="teamSize" id="teamSize" placeholder="e.g., 3" required>
    <div id="teamSizeError" class="error-message"></div>

    <label for="averageProductivity">Average Productivity:</label>
    <input type="number" step="0.01" name="averageProductivity" id="averageProductivity" placeholder="e.g., 0.08" required>
    <div id="averageProductivityError" class="error-message"></div>

    <label for="hourlyRate">Hourly Rate ($):</label>
    <input type="number" step="0.01" name="hourlyRate" id="hourlyRate" placeholder="e.g., 100.00" required>
    <div id="hourlyRateError" class="error-message"></div>

    <button type="submit">Calculate</button>
</form>


<?php
// Display the result if available
if (isset($totalMonths) && isset($totalCostINR) && isset($functionalPoints)) {
    echo "<h2>Calculation Result</h2>";
    echo "<p>Total Months: " . round($totalMonths) . "</p>"; // Round to the nearest integer
    echo "<p>Total Cost: ₹" . number_format($totalCostINR, 2) . "</p>";
}
?>

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

    function updateSelectedEmployeeId() {
        const checkboxes = document.querySelectorAll('input[name="employees[]"]');
        const selectedEmployeeId = [...checkboxes].filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        document.getElementById('selected_employee_id').value = selectedEmployeeId.join(',');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="employees[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedEmployeeId);
        });
    });

    function validateForm() {
        const externalInputs = document.getElementById("externalInputs").value;
        const externalOutputs = document.getElementById("externalOutputs").value;
        const externalInquiries = document.getElementById("externalInquiries").value;

        const externalInputsError = document.getElementById("externalInputsError");
        const externalOutputsError = document.getElementById("externalOutputsError");
        const externalInquiriesError = document.getElementById("externalInquiriesError");

        const isWord = /^[A-Za-z\s,]+$/;

        // Validate External Inputs
        if (!isWord.test(externalInputs)) {
            externalInputsError.innerHTML = "Please enter valid words separated by commas";
            return false;
        } else {
            externalInputsError.innerHTML = "";
        }

        // Validate External Outputs
        if (!isWord.test(externalOutputs)) {
            externalOutputsError.innerHTML = "Please enter valid words separated by commas";
            return false;
        } else {
            externalOutputsError.innerHTML = "";
        }

        // Validate External Inquiries
        if (!isWord.test(externalInquiries)) {
            externalInquiriesError.innerHTML = "Please enter valid words separated by commas";
            return false;
        } else {
            externalInquiriesError.innerHTML = "";
        }

        return true;
    }
</script>
</body>
</html>
