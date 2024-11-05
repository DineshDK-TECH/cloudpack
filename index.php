<?php
// Include the database connection
include('../includes/db.php');

// Add Task (POST Request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_name'])) {
    if (!empty($_POST['task_name'])) {
        $task_name = $_POST['task_name'];
        $query = "INSERT INTO tasks (title) VALUES (:task_name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':task_name', $task_name);
        $stmt->execute();
        header("Location: index.php");
        exit();
    } else {
        echo "Task name is required!";
    }
}

// Edit Task (POST Request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_task_name'])) {
    if (!empty($_POST['edit_task_name']) && isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        $task_name = $_POST['edit_task_name'];
        $query = "UPDATE tasks SET title = :task_name WHERE id = :task_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':task_name', $task_name);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    } else {
        echo "Task name is required!";
    }
}

// Delete Task (GET Request)
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    // Prepare delete query
    $deleteQuery = "DELETE FROM tasks WHERE id = :id";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $task_id);
    // Execute delete statement
    if ($deleteStmt->execute()) {
        // Redirect after successful deletion
        header("Location: index.php");
        exit();
    } else {
        echo "Failed to delete the task.";
    }
}

// Mark Task as Completed (GET Request)
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    // Prepare update query to mark the task as completed
    $updateQuery = "UPDATE tasks SET completed = 1 WHERE id = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':id', $task_id);
    // Execute update statement
    if ($updateStmt->execute()) {
        // Redirect after marking as completed
        header("Location: index.php");
        exit();
    } else {
        echo "Failed to update task completion.";
    }
}

// Fetch all tasks (including created_at, updated_at, and completed columns)
$query = "SELECT id, title, created_at, updated_at, completed FROM tasks ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Link to CSS file -->
</head>
<body>

    <h1>Task Manager</h1>

    <!-- Task Form for Adding a New Task -->
    <form action="index.php" method="POST">
        <label for="task_name">Task Name</label>
        <input type="text" name="task_name" id="task_name" required>
        <button type="submit">Add Task</button>
    </form>

    <!-- Task List -->
    <h2>Task List</h2>
    <?php if (count($tasks) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Task Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                    <td><?php echo $task['created_at']; ?></td>
                    <td><?php echo isset($task['updated_at']) ? $task['updated_at'] : 'Not updated yet'; ?></td>
                    <td>
                        <!-- Delete Link -->
                        <a href="index.php?delete=<?php echo $task['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                        | 
                        <!-- Edit Link -->
                        <a href="index.php?edit=<?php echo $task['id']; ?>">Edit</a>
                        | 
                        <!-- Mark as Completed -->
                        <?php if (isset($task['completed']) && $task['completed'] == 0): ?>
                            <a href="index.php?complete=<?php echo $task['id']; ?>" 
                               onclick="return confirm('Are you sure you want to mark this task as completed?');">Mark as Completed</a>
                        <?php elseif (isset($task['completed']) && $task['completed'] == 1): ?>
                            <span>Completed</span>
                        <?php else: ?>
                            <span>Status unknown</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tasks found. Add a new task!</p>
    <?php endif; ?>

    <!-- Edit Task Modal -->
    <?php if (isset($_GET['edit'])): ?>
        <?php 
        $task_id = $_GET['edit'];
        $query = "SELECT * FROM tasks WHERE id = :task_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form action="index.php" method="POST">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <label for="edit_task_name">Edit Task Name</label>
            <input type="text" name="edit_task_name" id="edit_task_name" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            <button type="submit">Update Task</button>
        </form>
    <?php endif; ?>

</body>
</html>
