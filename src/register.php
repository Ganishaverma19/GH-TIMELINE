<?php
session_start();
require_once 'function.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $_SESSION['email'] = $email;
        $_SESSION['verification_code'] = $code;
        sendVerificationEmail($email, $code);
        $message = "Verification code sent to $email";
    } else {
        $message = "Invalid email address.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verification_code'])) {
    $enteredCode = trim($_POST['verification_code']);
    if (isset($_SESSION['verification_code']) && $enteredCode === $_SESSION['verification_code']) {
        registerEmail($_SESSION['email']);
        $message = "Email registered successfully.";
        unset($_SESSION['email'], $_SESSION['verification_code']);
    } else {
        $message = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register Email</title></head>
<body>
    <h2>Register for GitHub Timeline Updates</h2>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <?php if (!isset($_SESSION['verification_code'])): ?>
    <form method="POST">
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Register</button>
    </form>
    <?php else: ?>
    <form method="POST">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code">
        <button type="submit">Verify</button>
    </form>
    <?php endif; ?>
</body>