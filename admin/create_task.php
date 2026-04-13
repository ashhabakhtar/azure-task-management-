<?php
require_once '../config.php';
require_once '../auth.php';

requireAdmin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_POST['user_id'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    try {
        // Create task
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, user_id, status, priority, due_date, created_by) VALUES (?, ?, ?, 'pending', ?, ?, ?)");
        $stmt->execute([$title, $description, $user_id, $priority, $due_date, $_SESSION['user_id']]);
        
        // Notify user
        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $msg = "You have been assigned a new task: " . $title;
        $notifStmt->execute([$user_id, $msg]);

        $message = 'Task created and assigned successfully.';
        $messageType = 'success';
    } catch(PDOException $e) {
        $message = 'Error creating task. Please try again.';
        $messageType = 'danger';
    }
}

$users = $pdo->query("SELECT id, username FROM users WHERE role = 'user' ORDER BY username ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task - PAKKAdesK</title>
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
                <a href="create_task.php" class="nav-item active"><i class="fa-solid fa-plus-circle"></i> Create Task</a>
                <a href="all_tasks.php" class="nav-item"><i class="fa-solid fa-list-check"></i> All Tasks</a>
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
                <h1 class="page-title">Create New Task</h1>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> animate-fade-in"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="stat-card animate-fade-in" style="max-width: 600px;">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Task Title</label>
                            <input type="text" name="title" class="form-control" placeholder="E.g. Update Website Homepage" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Detailed description of the task requirements..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Assign To</label>
                            <select name="user_id" class="form-control" required style="cursor: pointer; appearance: auto;">
                                <option value="" disabled selected>Select a user</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-control" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1rem;"><i class="fa-solid fa-paper-plane mr-2" style="margin-right: 8px;"></i> Assign Task</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
