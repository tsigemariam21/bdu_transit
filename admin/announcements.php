<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once '../config/db_connect.php';

$message = '';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, message, type, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['message'], $_POST['type'], $_SESSION['user_id']]);
        $message = "Announcement posted successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Delete/Toggle
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'delete') {
        $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'toggle') {
        $pdo->prepare("UPDATE announcements SET is_active = NOT is_active WHERE announcement_id = ?")->execute([$id]);
    }
    header("Location: announcements.php");
    exit;
}

// Fetch All
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY date_posted DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Announcements - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            padding: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-main);
            margin-bottom: 2rem;
        }
    </style>
</head>

<body style="background: #f8fafc;">

    <nav class="navbar glass" style="position: sticky;">
        <a href="dashboard.php" class="logo">BDU Admin</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="../index.php" target="_blank">View Site_&nearr;</a>
            <a href="logout.php" style="color: var(--danger-color);">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2 style="margin-bottom: 2rem;">Manage Announcements</h2>

        <?php if ($message): ?>
            <div
                style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3 style="margin-bottom: 1rem;">Post New Announcement</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title</label>
                    <input type="text" name="title" class="form-control" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: var(--radius-md);">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Type</label>
                    <select name="type"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: var(--radius-md);">
                        <option value="info">Info (Blue)</option>
                        <option value="warning">Warning (Orange)</option>
                        <option value="alert">Alert (Red)</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message</label>
                    <textarea name="message" rows="3" class="form-control" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: var(--radius-md);"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Announcement</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $row): ?>
                        <tr>
                            <td><?php echo date('M d, H:i', strtotime($row['date_posted'])); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <span
                                    class="badge badge-<?php echo $row['type'] === 'info' ? 'info' : ($row['type'] === 'warning' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($row['type']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['is_active']): ?>
                                    <span style="color: var(--success-color);"><i class="fas fa-check-circle"></i> Active</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);"><i class="fas fa-times-circle"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?action=toggle&id=<?php echo $row['announcement_id']; ?>" class="btn-outline"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    <?php echo $row['is_active'] ? 'Hide' : 'Show'; ?>
                                </a>
                                <a href="?action=delete&id=<?php echo $row['announcement_id']; ?>" class="btn-outline"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--danger-color); color: var(--danger-color);"
                                    onclick="return confirm('Delete this?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>