<?php
require_once '../config.php';
require_once '../auth.php';

requireAdmin();

// Handle Delete
if (isset($_GET['delete'])) {
    $taskId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    header("Location: all_tasks.php");
    exit();
}

$tasks = $pdo->query("
    SELECT t.*, u.username as assigned_to 
    FROM tasks t 
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tasks - PAKKAdesK</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-layer-group"></i> PAKKAdesK
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="users.php" class="nav-item"><i class="fa-solid fa-users"></i> Manage Users</a>
                <a href="create_task.php" class="nav-item"><i class="fa-solid fa-plus-circle"></i> Create Task</a>
                <a href="all_tasks.php" class="nav-item active"><i class="fa-solid fa-list-check"></i> All Tasks</a>
                <a href="../logout.php" class="nav-item" style="margin-top: auto; color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="user-dropdown">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</span>
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                </div>
            </header>

            <div class="content-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h1 class="page-title" style="margin-bottom: 0;">All Tasks</h1>
                    <a href="create_task.php" class="btn btn-primary"><i class="fa-solid fa-plus mr-2" style="margin-right:8px;"></i> New Task</a>
                </div>

                <div class="table-wrapper animate-fade-in">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task Details</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tasks) > 0): ?>
                                <?php foreach($tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($task['title']); ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($task['description']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($task['assigned_to']): ?>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div style="width:24px; height:24px; border-radius:50%; background:var(--background); display:flex; align-items:center; justify-content:center; font-weight:600; font-size: 0.7rem;">
                                                        <?php echo strtoupper(substr($task['assigned_to'], 0, 1)); ?>
                                                    </div>
                                                    <?php echo htmlspecialchars($task['assigned_to']); ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="color:var(--text-muted)">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge badge-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span></td>
                                        <td><span class="badge badge-<?php echo $task['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($task['due_date'])); ?></td>
                                        <td style="text-align: right;">
                                            <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-danger" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to delete this task?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">No tasks found. Create one.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
