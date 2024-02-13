<?php
// Include your database connection code here
include('../conn.php');

// Initialize variables
$task = $employee = $completionDate = $projectId = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data
    $task = isset($_POST['task']) ? htmlspecialchars($_POST['task']) : '';
    $employee = isset($_POST['employee']) ? intval($_POST['employee']) : 0;
    $completionDate = isset($_POST['completion_date']) ? htmlspecialchars($_POST['completion_date']) : '';
    $projectId = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;

    // Set assignment date to the current date
    $assignment_date = date('Y-m-d');

    // Set default status as 'Pending'
    $status = 'Pending';

     // Insert the task assignment into the 'tasks' table
     $sql = "INSERT INTO task (task_description, assigned_to, assignment_date, completion_date, status, request_id)
     VALUES ('$task', $employee, '$assignment_date', '$completionDate', '$status', $projectId)";

        if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Task assigned successfully!");</script>';
        } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        }    

    // Close the database connection
    //$conn->close();
}
// Fetch projects from the 'project_request' table
$sqlProjects = "SELECT * FROM project_requests WHERE status = 'Approved'";
$resultProjects = $conn->query($sqlProjects);
$projects = [];

if ($resultProjects->num_rows > 0) {
while ($rowProject = $resultProjects->fetch_assoc()) {
    $projects[] = $rowProject;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<head>
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
</script> 
</head>
<body>
<header>
    <div class="openbtn" onclick="toggleSidebar()">&#9776;</div>
    <button onclick="window.location.href='tdashboard.php'" type="button" style="float: right;">Back</button>
    <h1>Task Allocation</h1>
</header>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
    <a href="add_task.php">
        <span class="icon">&#10010;</span> Add Task
    </a>
    <a href="view_status.php">
        <span class="icon">&#128196;</span> View Status
    </a>
    <a href="schedule_meeting.php">
        <span class="icon">&#9201;</span> Schedule Meeting
    </a>
    <a href="report.php">
        <span class="icon">&#128221;</span> Monitor Daily Progress
    </a>
    <a href="view_projects.php">
        <span class="icon">&#128213;</span> View Approved/Denied Projects
    </a>
    <a href="functional.php">
        <span class="icon">📏</span> Calculate Functional Point
    </a>
</div>

<div class="main-content">
    <form action="" method="post">
        <h2 style="color: #333; text-align: center;">Assign Task</h2>
        <label for="task" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Task:</label>
        <input type="text" name="task" value="<?php echo $task; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">

        <label for="employee" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Assign to Employee:</label>
        <select name="employee" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
            <?php
            // Fetch employees with designations from the database
            $sql = "SELECT e.id, e.fname, d.desig_type
                    FROM employee e
                    INNER JOIN designation d ON e.desig_id = d.desig_id";
            $result = $conn->query($sql);

            // Display employees in dropdown
            while ($row = $result->fetch_assoc()) {
                $selected = ($row['id'] == $employee) ? 'selected' : '';
                echo "<option value='" . $row['id'] . "' $selected>" . $row['fname'] . " - " . $row['desig_type'] . "</option>";
            }
            ?>
        </select>

        <label for="completion_date" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Completion Date:</label>
        <input type="date" name="completion_date" value="<?php echo $completionDate; ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">

        <label for="project_id" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Project:</label>
        <select name="project_id" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
            <?php
            // Display projects in dropdown
            foreach ($projects as $project) {
                echo "<option value='" . $project['request_id'] . "'>" . $project['project_name'] . "</option>";
            }
            ?>
        </select>

        <button type="submit" style="background-color: #0074D9; color: #fff; cursor: pointer; width: 100%; padding: 8px; border: none; border-radius: 4px; font-size: 14px;">Assign Task</button>
    </form>
</div>
</body>
</html>
