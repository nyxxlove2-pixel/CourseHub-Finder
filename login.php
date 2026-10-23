


<?php
session_start();
require_once __DIR__ . '/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT user_id, full_name, email, password_hash
         FROM users
         WHERE email = ? AND status = 'active'
         LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Program/Course Finder</title>


<link rel="stylesheet" href="assets/login.css">
</head>

<body>

    <div class="login-container">

        <div class="welcome-panel">
            <div class="welcome-icon">🎓</div>
            <h1>Welcome Back!</h1>
            <p>
                Continue your journey with .
                Discover academic programs that match your interests,
                skills, and future career goals.
            </p>
        </div>

        <div class="form-panel">
            <h2>Log In</h2>
            <p class="subtitle">Enter your account details to continue.</p>

            
<form id="loginForm" action="login.php" method="POST">
    <label for="email">Email Address</label>
    <input
        type="email"
        id="email"
        name="email"
        placeholder="Enter your email"
        required
    >

    <label for="password">Password</label>
    <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter your password"
        required
    >

    <?php if ($error !== ''): ?>
        <p class="error-message">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <button type="submit" class="login-button">
        Log In
    </button>
</form>


            <p class="register-link">
                Don't have an account?
                <a href="register.php">Create Account</a>
            </p>

            <a href="index.html" class="back-link">
                 Back to Home
            </a>
        </div>

    </div>

    
</body>
</html>