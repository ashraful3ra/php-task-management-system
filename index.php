<?php
include 'db.php';

// Insert a new task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    $task = $_POST['task'];
    $sql = "INSERT INTO tasks (task, status) VALUES (?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $task);
    $stmt->execute();
}

// Update task status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'] == 0 ? 1 : 0;
    $sql = "UPDATE tasks SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $task_id);
    $stmt->execute();
    header("Location: index.php"); // Redirect to avoid form resubmission
}

// Fetch tasks for the current day
$currentDate = date("Y-m-d");
$sqlPending = "SELECT id, task FROM tasks WHERE status=0 AND DATE(created_at) = ?";
$stmtPending = $conn->prepare($sqlPending);
$stmtPending->bind_param("s", $currentDate);
$stmtPending->execute();
$resultPending = $stmtPending->get_result();

$sqlCompleted = "SELECT id, task FROM tasks WHERE status=1 AND DATE(created_at) = ?";
$stmtCompleted = $conn->prepare($sqlCompleted);
$stmtCompleted->bind_param("s", $currentDate);
$stmtCompleted->execute();
$resultCompleted = $stmtCompleted->get_result();

$sqlAll = "SELECT id, task, status FROM tasks WHERE DATE(created_at) = ?";
$stmtAll = $conn->prepare($sqlAll);
$stmtAll->bind_param("s", $currentDate);
$stmtAll->execute();
$resultAll = $stmtAll->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap" rel="stylesheet">
    <title>Task Manager</title>
</head>
<body onload="startTime()">
<div class="container">
    <h2>Task Manager</h2>
    <form action="" method="POST">
        <input type="text" name="task" placeholder="Enter a new task" required>
        <button type="submit">Add Task</button>
    </form>
    <a href="history.php" class="history-link">Task History</a>
    <div class="task-columns">
        <div class="task-column">
            <h3>Pending Tasks</h3>
            <ul>
                <?php while ($row = $resultPending->fetch_assoc()): ?>
                    <li class="pending">
                        <?= htmlspecialchars($row['task']); ?>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?= $row['id']; ?>">
                            <input type="hidden" name="status" value="0">
                            <button type="submit" name="update_status" class="icon-button">&#x2714;</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="task-column">
            <h3>Completed Tasks</h3>
            <ul>
            <?php while ($row = $resultCompleted->fetch_assoc()): ?>
                    <li class="completed">
                        <?= htmlspecialchars($row['task']); ?>
                        <form action="" method="post" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?= $row['id']; ?>">
                            <input type="hidden" name="status" value="1">
                            <button type="submit" name="update_status" class="icon-button">&#x2716;</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="task-column">
            <h3>All Tasks</h3>
            <ul>
                <?php while ($row = $resultAll->fetch_assoc()): ?>
                    <li class="<?= $row['status'] ? 'completed' : 'pending'; ?>">
                        <?= htmlspecialchars($row['task']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
