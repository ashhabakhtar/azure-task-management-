<?php
require_once '../config.php';
require_once '../auth.php';

requireAdmin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $password]);
            $message = 'User created successfully.';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error creating user. Username or email might already exist.';
            $messageType = 'danger';
        }
    }
}

$users = $pdo->query("SELECT id, username, email, role, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - PAKKAdesK</title>
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
                <a href="users.php" class="nav-item active"><i class="fa-solid fa-users"></i> Manage Users</a>
                <a href="create_task.php" class="nav-item"><i class="fa-solid fa-plus-circle"></i> Create Task</a>
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
                <h1 class="page-title">Manage Users</h1>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> animate-fade-in"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="stats-grid animate-fade-in">
                    <!-- Create User Card -->
                    <div class="stat-card" style="grid-column: span 1;">
                        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Add New User</h2>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="create">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Create User</button>
                        </form>
                    </div>

                    <!-- User List -->
                    <div class="table-wrapper" style="grid-column: span 2; margin-bottom: 0;">
                        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                            <h2 style="font-size: 1.125rem; font-weight: 600;">Registered Users</h2>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach($users as $user): ?>
                                        <tr>
                                            <td>#<?php echo $user['id']; ?></td>
                                            <td style="font-weight: 500; font-size: 1rem;">
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <div style="width:30px; height:30px; border-radius:50%; background:var(--background); display:flex; align-items:center; justify-content:center; font-weight:600; font-size: 0.8rem;">
                                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                    </div>
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
