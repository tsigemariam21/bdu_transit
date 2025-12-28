<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once '../config/db_connect.php';

$message = '';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO buses (bus_number, driver_name, status) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['bus_number'], $_POST['driver_name'], $_POST['status']]);
            $message = "Bus added successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        try {
            $stmt = $pdo->prepare("UPDATE buses SET driver_name = ?, status = ? WHERE bus_id = ?");
            $stmt->execute([$_POST['driver_name'], $_POST['status'], $_POST['bus_id']]);
            $message = "Bus updated successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM buses WHERE bus_id = ?")->execute([$_GET['id']]);
    header("Location: buses.php");
    exit;
}

// Fetch All
$buses = $pdo->query("SELECT * FROM buses ORDER BY bus_number ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Buses - Admin</title>
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
        <h2>Manage Buses</h2>

        <?php if ($message): ?>
            <div
                style="background: rgba(16, 185, 129, 0.1); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add Form -->
        <div class="form-card">
            <h3 style="margin-bottom: 1rem;">Add New Bus</h3>
            <form method="POST" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="action" value="add">
                <div style="flex: 1; min-width: 200px;">
                    <label>Bus Number</label>
                    <input type="text" name="bus_number" class="form-control" required placeholder="e.g. BDU-105">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label>Driver Name</label>
                    <input type="text" name="driver_name" class="form-control" placeholder="Optional">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Bus</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Bus Number</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buses as $row): ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="bus_id" value="<?php echo $row['bus_id']; ?>">
                                <td><strong><?php echo htmlspecialchars($row['bus_number']); ?></strong></td>
                                <td>
                                    <input type="text" name="driver_name"
                                        value="<?php echo htmlspecialchars($row['driver_name']); ?>" class="form-control"
                                        style="padding: 0.25rem;">
                                </td>
                                <td>
                                    <select name="status" class="form-control" style="padding: 0.25rem;">
                                        <option value="active" <?php if ($row['status'] == 'active')
                                            echo 'selected'; ?>>Active
                                        </option>
                                        <option value="maintenance" <?php if ($row['status'] == 'maintenance')
                                            echo 'selected'; ?>>Maintenance</option>
                                        <option value="inactive" <?php if ($row['status'] == 'inactive')
                                            echo 'selected'; ?>>
                                            Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" class="btn-outline"
                                        style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Save</button>
                                    <a href="?action=delete&id=<?php echo $row['bus_id']; ?>" class="btn-outline"
                                        style="padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--danger-color); color: var(--danger-color);"
                                        onclick="return confirm('Delete this bus?');">
                                        Delete
                                    </a>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>