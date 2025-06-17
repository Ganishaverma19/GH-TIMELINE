<?php
session_start();
require_once 'function.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = $code;
            sendUnsubscribeEmail($email, $code);
            $message = "Verification code sent to $email";
        } else {
            $message = "Invalid email address.";
        }
    }

    if (isset($_POST['unsubscribe_verification_code'])) {
        $enteredCode = trim($_POST['unsubscribe_verification_code']);
        if (isset($_SESSION['unsubscribe_code']) && $enteredCode === $_SESSION['unsubscribe_code']) {
            unsubscribeEmail($_SESSION['unsubscribe_email']);
            $message = "You have been unsubscribed.";
            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<h2>Unsubscribe from GitHub Updates</h2>
<?php if ($message) echo "<p>$message</p>"; ?>

<?php if (!isset($_SESSION['unsubscribe_code'])): ?>
<form method="POST">
    <input type="email" name="unsubscribe_email" required placeholder="Your email">
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>
<?php endif; ?>

<?php if (isset($_SESSION['unsubscribe_code'])): ?>
<form method="POST">
    <input type="text" name="unsubscribe_verification_code" placeholder="Verification code" required>
    <button id="verify-unsubscribe">Verify</button>
</form>
<?php endif; ?>