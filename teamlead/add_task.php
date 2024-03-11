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
// Fetch employees and their designations from the 'employee' and 'designation' tables
$sqlEmployees = "SELECT e.id, e.fname, d.desig_type
                 FROM employee e
                 JOIN designation d ON e.desig_id = d.desig_id";

$resultEmployees = $conn->query($sqlEmployees);
$employees = [];

if ($resultEmployees->num_rows > 0) {
    while ($rowEmployee = $resultEmployees->fetch_assoc()) {
        $employees[] = $rowEmployee;
    }
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
            <span class="icon">&#9201;</span>Sprint Meeting
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
</div>

<div class="main-content">
    <form action="" method="post" onsubmit="validateFormHandler(event)">
        <h2 style="color: #333; text-align: center;">Assign Task</h2>
        <label for="task" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Task:</label>
        <input type="text" name="task" id="task" value="<?php echo $task; ?>" required oninput="validateTask()" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
        <span id="task-error" style="color: red;"></span>

        <label style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Assign to Employee:</label>
        <label>Employees:</label><br>
        <?php foreach ($employees as $employee): ?>
            <div class="checkbox-container">
                <label for="employee_<?php echo $employee['id']; ?>">
                    <?php echo $employee['fname'] . '(' . $employee['desig_type'].')'; ?>
                    <input type="checkbox" name="employee[]" id="employee_<?php echo $employee['id']; ?>" value="<?php echo $employee['id']; ?>">
                    <span class="checkmark"></span>
                </label>
            </div>
        <?php endforeach; ?>
        <label for="completion_date" style="display: block; margin: 10px 0 5px; text-align: left; color: #555;">Completion Date:</label>
        <input type="date" name="completion_date" id="completion_date" min="<?php echo date('Y-m-d'); ?>" required value="<?php echo $completionDate; ?>" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" oninput="validateCompletionDate()">
        <span id="completion_date_error" style="color: red;"></span>
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
<script>
function validateTask() {
    const taskInput = document.getElementById('task');
    const taskValue = taskInput.value.trim();
    const taskError = document.getElementById('task-error');

    // Split the task into words
    const words = taskValue.split(' ');

    // Check if each word has minimum 1 letters and maximum 20 letters
    const validLength = words.every(word => word.length >= 1 && word.length <= 20);

    // Check if there are at least 2 words and at most 10 words
    const validWordCount = words.length >= 2 && words.length <= 10;

    // Check if all characters are letters
    const validCharacters = /^[a-zA-Z\s]*$/.test(taskValue);

    if (validLength && validWordCount && validCharacters) {
        taskError.textContent = '';
        return true;
    } else {
        let errorMessage = 'Invalid task format. Please follow the specified criteria:';
        if (!validLength) {
            errorMessage += '\n- Each word must have between 1 and 20 characters.';
        }
        if (!validWordCount) {
            errorMessage += '\n- There must be between 2 and 10 words.';
        }
        if (!validCharacters) {
            errorMessage += '\n- Only letters and spaces are allowed.';
        }
        taskError.textContent = errorMessage;
        return false;
    }
}

function validateCompletionDate() {
    const completionDateInput = document.getElementById('completion_date');
    const completionDateError = document.getElementById('completion_date_error');
    const completionDate = new Date(completionDateInput.value);
    const today = new Date();

    if (isNaN(completionDate.getTime()) || completionDate <= today) {
        completionDateError.textContent = 'Completion date must be a future date.';
    } else {
        completionDateError.textContent = '';
    }
}

function validateFormHandler(event) {
    if (!validateForm()) {
        event.preventDefault(); // Prevent form submission
    }
}

</script>
</body>
</html>