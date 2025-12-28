<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: routes.php");
    exit;
}

$route_id = $_GET['id'];
$route = $pdo->prepare("SELECT * FROM routes WHERE route_id = ?");
$route->execute([$route_id]);
$route_data = $route->fetch();

if (!$route_data) {
    die("Route not found.");
}

$message = '';

// Handle Add Stop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO pickup_points (route_id, location_name, sequence_order) VALUES (?, ?, ?)");
    $stmt->execute([$route_id, $_POST['location_name'], $_POST['sequence_order']]);
}

// Handle Delete Stop
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['stop_id'])) {
    $pdo->prepare("DELETE FROM pickup_points WHERE point_id = ?")->execute([$_GET['stop_id']]);
    header("Location: route_stops.php?id=" . $route_id);
    exit;
}

// Fetch Stops
$stops = $pdo->prepare("SELECT * FROM pickup_points WHERE route_id = ? ORDER BY sequence_order ASC");
$stops->execute([$route_id]);
$all_stops = $stops->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Stops - <?php echo htmlspecialchars($route_data['route_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container {
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .stop-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: white;
            border-bottom: 1px solid #eee;
        }

        .stop-item:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body style="background: #f8fafc;">
    <nav class="navbar glass">
        <a href="routes.php" class="logo">&larr; Back to Routes</a>
    </nav>

    <div class="admin-container">
        <header style="margin-bottom: 2rem;">
            <h2>Stops for: <?php echo htmlspecialchars($route_data['route_name']); ?></h2>
            <p style="color: var(--text-muted);">Define the sequence of pickup/drop-off points.</p>
        </header>

        <div class="glass"
            style="background: white; border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem;">
            <h3>Add Stop</h3>
            <form method="POST" style="display: flex; gap: 1rem; margin-top: 1rem;">
                <input type="hidden" name="action" value="add">
                <input type="number" name="sequence_order" class="form-control" placeholder="Order #"
                    style="width: 100px;" required value="<?php echo count($all_stops) + 1; ?>">
                <input type="text" name="location_name" class="form-control" placeholder="Location Name" required
                    style="flex: 1;">
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>

        <div class="glass" style="background: white; border-radius: var(--radius-md); overflow: hidden;">
            <?php foreach ($all_stops as $row): ?>
                <div class="stop-item">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span class="badge badge-primary"><?php echo $row['sequence_order']; ?></span>
                        <strong><?php echo htmlspecialchars($row['location_name']); ?></strong>
                    </div>
                    <a href="?id=<?php echo $route_id; ?>&action=delete&stop_id=<?php echo $row['point_id']; ?>"
                        style="color: var(--danger-color);">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($all_stops)): ?>
                <div style="padding: 2rem; text-align: center; color: var(--text-muted);">No stops added yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>