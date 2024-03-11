
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
</head>
<body>
    <h2>Create a New Task</h2>
    <a href="dashboard.php">Back to Dashboard</a>
    
    <form method="post" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required>

        <button type="submit">Create Task</button>
    </form>
</body>
</html>
