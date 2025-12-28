<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once '../config/db_connect.php';

$message = '';

// Handle Add / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $stmt = $pdo->prepare("INSERT INTO routes (route_name, description) VALUES (?, ?)");
        $stmt->execute([$_POST['route_name'], $_POST['description']]);
        $message = "Route added successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM routes WHERE route_id = ?")->execute([$_GET['id']]);
    header("Location: routes.php");
    exit;
}

$routes = $pdo->query("SELECT * FROM routes ORDER BY route_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Routes - Admin</title>
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
        <h2>Manage Routes</h2>
        <?php if ($message): ?>
            <div
                style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <h3>Add New Route</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div style="margin-bottom: 1rem;">
                    <label>Route Name</label>
                    <input type="text" name="route_name" class="form-control" required
                        placeholder="e.g. Campus to Piazza">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Route</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Route Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routes as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['route_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="route_stops.php?id=<?php echo $row['route_id']; ?>" class="btn-outline"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    <i class="fas fa-map-marker-alt"></i> Manage Stops
                                </a>
                                <a href="?action=delete&id=<?php echo $row['route_id']; ?>" class="btn-outline"
                                    style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--danger-color); color: var(--danger-color);"
                                    onclick="return confirm('Delete this route?');">
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