<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assume the form is submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming the email is passed as a hidden input field in the form
    $email = $_POST['email'];

    // Retrieve emp_id based on email
    $sqlEmpId = "SELECT id FROM employee WHERE email = '$email'";
    $resultEmpId = $conn->query($sqlEmpId);

    if ($resultEmpId->num_rows > 0) {
        // Fetch emp_id
        $rowEmpId = $resultEmpId->fetch_assoc();
        $empid = $rowEmpId['id'];

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $reason = $_POST['reason'];

        // Validate input
        if ($start_date >= $end_date) {
            echo '<script>alert("End date must be after start date.")</script>';
        } else {
            // Insert the leave request into the database
            $insert_query = "INSERT INTO leave_requests (empid, start_date, end_date, reason) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isss", $empid, $start_date, $end_date, $reason);
            if ($stmt->execute()) {
                echo '<script>alert("Leave request submitted successfully.")</script>';
            } else {
                echo "Error submitting leave request: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        echo "Employee ID not found.";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leave Management</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Submit Leave Request</h2>
    <form method="post" action="">
        <!-- Assume the email is passed as a hidden input field in the form -->
        <input type="hidden" id="email" name="email" value="<?php echo $_GET['email']; ?>">

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required><br>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" rows="4" cols="50" required></textarea><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
