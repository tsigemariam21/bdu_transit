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
        $stmt = $pdo->prepare("INSERT INTO schedules (bus_id, route_id, departure_time, arrival_time, operating_days) VALUES (?, ?, ?, ?, ?)");
        // arrival time is optional/estimated
        $arrival = !empty($_POST['arrival_time']) ? $_POST['arrival_time'] : null;
        $stmt->execute([$_POST['bus_id'], $_POST['route_id'], $_POST['departure_time'], $arrival, $_POST['operating_days']]);
        $message = "Schedule created successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM schedules WHERE schedule_id = ?")->execute([$_GET['id']]);
    header("Location: schedules.php");
    exit;
}

// Fetch Lists for Dropdowns
$buses = $pdo->query("SELECT * FROM buses WHERE status != 'inactive'")->fetchAll();
$routes = $pdo->query("SELECT * FROM routes")->fetchAll();

// Fetch Schedules
$schedules_sql = "SELECT s.*, b.bus_number, r.route_name 
                  FROM schedules s 
                  JOIN buses b ON s.bus_id = b.bus_id 
                  JOIN routes r ON s.route_id = r.route_id 
                  ORDER BY r.route_name, s.departure_time";
$schedules = $pdo->query($schedules_sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Schedules - Admin</title>
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
        <h2>Manage Schedules</h2>
        <?php if ($message): ?>
            <div
                style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <h3>Add New Schedule</h3>
            <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <input type="hidden" name="action" value="add">

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Route</label>
                    <select name="route_id" class="form-control" required>
                        <option value="">-- Select Route --</option>
                        <?php foreach ($routes as $r): ?>
                            <option value="<?php echo $r['route_id']; ?>"><?php echo htmlspecialchars($r['route_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Bus</label>
                    <select name="bus_id" class="form-control" required>
                        <option value="">-- Select Bus --</option>
                        <?php foreach ($buses as $b): ?>
                            <option value="<?php echo $b['bus_id']; ?>"><?php echo htmlspecialchars($b['bus_number']); ?>
                                (<?php echo htmlspecialchars($b['status']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Departure Time</label>
                    <input type="time" name="departure_time" class="form-control" required>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Arrival Time (Est.)</label>
                    <input type="time" name="arrival_time" class="form-control">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Operating Days</label>
                    <input type="text" name="operating_days" class="form-control" value="Mon,Tue,Wed,Thu,Fri"
                        placeholder="e.g. Mon,Tue,Fri">
                </div>

                <div style="grid-column: span 2;">
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Bus</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Days</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['route_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['bus_number']); ?></td>
                            <td><?php echo date('H:i', strtotime($row['departure_time'])); ?></td>
                            <td><?php echo $row['arrival_time'] ? date('H:i', strtotime($row['arrival_time'])) : '-'; ?>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($row['operating_days']); ?></td>
                            <td>
                                <a href="?action=delete&id=<?php echo $row['schedule_id']; ?>"
                                    style="color: var(--danger-color);" onclick="return confirm('Remove schedule?');"><i
                                        class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>