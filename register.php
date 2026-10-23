
<?php
session_start();
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8) {
        $error = 'Enter your name, a valid email, and a password of at least 8 characters.';
    } else {
        try {
            $pdo->beginTransaction();

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, email, password_hash)
                 VALUES (?, ?, ?)'
            );
            $stmt->execute([$fullName, $email, $hash]);

            $userId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                'INSERT INTO student_profiles (user_id)
                 VALUES (?)'
            );
            $stmt->execute([$userId]);

            $pdo->commit();

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($e->getCode() === '23000') {
                $error = 'That email address may already be registered.';
            } else {
                error_log($e->getMessage());
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | PC Finder</title>

    <link rel="stylesheet" href="assets/register.css">
</head>

<body>

    <main class="register-container">

        <section class="welcome-panel">
            <h1>Your Future Starts Here! 🎓</h1>

            <p>
                Join Program/Course Finder and start exploring college
                programs, tuition fees, and career opportunities
                that match your goals.
            </p>

            <ul>
                <li>✓ Discover college programs</li>
                <li>✓ Compare tuition fees</li>
                <li>✓ Explore career opportunities</li>
            </ul>
        </section>

        <section class="form-panel">

            <a href="index.html" class="back-link">Back to Home</a>

            <h2>Create Account</h2>
            <p class="subtitle">
                Enter your details to get started.
            </p>

            
<form id="registerForm" action="register.php" method="POST">
    <label for="fullname">Full Name</label>
    <input
        type="text"
        id="fullname"
        name="full_name"
        placeholder="Enter your full name"
        autocomplete="name"
        required
    >

    <label for="email">Email Address</label>
    <input
        type="email"
        id="email"
        name="email"
        placeholder="you@example.com"
        autocomplete="email"
        required
    >

    <label for="password">Password</label>
    <input
        type="password"
        id="password"
        name="password"
        placeholder="Create a password"
        autocomplete="new-password"
        minlength="8"
        required
    >

    <label for="confirmPassword">Confirm Password</label>
    <input
        type="password"
        id="confirmPassword"
        name="confirm_password"
        placeholder="Enter your password again"
        autocomplete="new-password"
        minlength="8"
        required
    >

    <button type="submit" class="create-btn">
        Create My Account
    </button>
</form>


            <p class="login-text">
                Already have an account?
                <a href="login.php">Log In</a>
            </p>

            

        </section>

        
    </main>

    

</body>
</html>

