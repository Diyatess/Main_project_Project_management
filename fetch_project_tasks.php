<?php
// Include your database connection code here
include('../conn.php');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected project from the request
$selectedProject = $_GET['project'];
// Fetch the project ID from the project_requests table
$sqlProjectId = "SELECT request_id FROM project_requests WHERE project_name = '$selectedProject'";
$resultProjectId = $conn->query($sqlProjectId);

if ($resultProjectId) {
    if ($resultProjectId->num_rows > 0) {
        $row = $resultProjectId->fetch_assoc();
        $project_id = $row['request_id'];

        // Fetch tasks for the selected project with employee names and designations
        $sqlTasks = "SELECT t.*, e.fname AS assigned_to_name, d.desig_type
        FROM task t
        INNER JOIN employee e ON t.assigned_to = e.id
        INNER JOIN designation d ON e.desig_id = d.desig_id
        WHERE request_id = '$project_id'
        ";
        $resultTasks = $conn->query($sqlTasks);

        if ($resultTasks) {
            $tasks = [];
            $completedTasks = 0;
            while ($rowTask = $resultTasks->fetch_assoc()) {
                $tasks[] = $rowTask;
                if ($rowTask['status'] == 'Completed') {
                    $completedTasks++;
                }
            }
            $completionPercentage = ($completedTasks / count($tasks)) * 100;
        } else {
            echo "Error fetching tasks: " . $conn->error;
            $tasks = [];
        }
    } else {
        echo "Project ID not found for project: $selectedProject";
        $tasks = [];
    }
} else {
    echo "Error fetching project ID: " . $conn->error;
    $tasks = [];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks for Project</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Tasks for Project: <?php echo $selectedProject; ?></h2>
    <p style="color: green; "><b>Completion Percentage:</b> <?php echo number_format($completionPercentage, 2); ?>%</p>
    <table>
        <tr>
            <th>Task Description</th>
            <th>Assigned To</th>
            <th>Designation</th>
            <th>Assignment Date</th>
            <th>Completion Date</th>
            <th>Status</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo $task['task_description']; ?></td>
                <td><?php echo $task['assigned_to_name']; ?></td>
                <td><?php echo $task['desig_type']; ?></td>
                <td><?php echo $task['assignment_date']; ?></td>
                <td style="color: red;"><?php echo $task['completion_date']; ?></td>
                <td><?php echo $task['status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
