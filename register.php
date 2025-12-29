<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db_connect.php';

$page_title = "Join BDU Transit";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role']; // staff or student

    if (empty($username) || empty($password) || empty($full_name)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name, $role])) {
                $success = "Registration successful! You can now <a href='login.php' style='color:inherit; font-weight:800; text-decoration:underline;'>login here</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

require_once 'includes/header.php';
?>
    <style>
        .auth-wrapper {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            margin-top: 60px;
        }

        .auth-card {
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .auth-header h2 {
            font-family: 'Outfit';
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            background: rgba(255,255,255,0.5);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
        }

        .auth-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
        }

        .alert {
            padding: 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.05);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.05);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-card glass animate-fade">
            <div class="auth-header">
                <h2>Join BDU Transit</h2>
                <p>Create your smart mobility account today</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Abebe Bikila" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
                </div>
                <div class="form-group">
                    <label>Who are you?</label>
                    <select name="role" class="form-control" required style="font-weight: 600;">
                        <option value="student">Student</option>
                        <option value="staff">Staff Member</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 800; margin-top: 1rem; box-shadow: var(--shadow-primary);">
                    Create My Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>
