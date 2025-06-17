<?php
session_start();
require_once 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['unsubscribe_email'])) {
        $email = $_POST['unsubscribe_email'];
        $code = generateVerificationCode();
        $_SESSION['unsubscribe_email'] = $email;
        $_SESSION['unsubscribe_code'] = $code;
        sendUnsubscribeEmail($email, $code);
        $message = "<p>Unsubscribe verification code sent to $email</p>";
    }

    if (isset($_POST['unsubscribe_verification_code'])) {
        if (isset($_SESSION['unsubscribe_code'], $_SESSION['unsubscribe_email'])) {
            $enteredCode = $_POST['unsubscribe_verification_code'];
            if ($enteredCode === $_SESSION['unsubscribe_code']) {
                unsubscribeEmail($_SESSION['unsubscribe_email']);
                $message = "<p>Email unsubscribed successfully.</p>";
                unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
            } else {
                $message = "<p>Incorrect code.</p>";
            }
        } else {
            $message = "<p>No unsubscribe request found. Please try again.</p>";
        }
    }
}
?>

<h2>Unsubscribe from GitHub Updates</h2>
<?php if ($message) echo $message; ?>

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
<?php endif; 
?>