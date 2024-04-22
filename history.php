<?php
include 'db.php'; // Include database connection

// Pagination setup
$limit = 100; // Number of tasks per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate the offset

// Fetch tasks with limit, offset, and grouped by date
$sql = "SELECT DATE_FORMAT(created_at, '%d %M, %Y') AS task_date, id, task, status FROM tasks ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Calculate total pages
$totalSql = "SELECT COUNT(id) AS total FROM tasks";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

// Group tasks by date
$tasksByDate = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['task_date'];
    unset($row['task_date']);
    $tasksByDate[$date][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;700&display=swap" rel="stylesheet">
    <title>Task History</title>
</head>
<body>
<div class="container">
    <h2>Task History</h2>
    <center><a href="index.php" class="home-button">Back to Task Manager</a></center>
    <?php foreach ($tasksByDate as $date => $tasks): ?>
        <div class="date">   <?= $date; ?></div>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['status'] ? 'completed' : 'pending'; ?>">
                    <?= htmlspecialchars($task['task']); ?> - <?= $task['status'] ? 'Completed' : 'Pending'; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
