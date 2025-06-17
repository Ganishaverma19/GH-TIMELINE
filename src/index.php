<?php
session_start();
require_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $code = generateVerificationCode();
        $_SESSION['email'] = $email;
        $_SESSION['verification_code'] = $code;
        sendVerificationEmail($email, $code);
        echo "<p>Verification code sent to $email</p>";
    }

    if (isset($_POST['verification_code'])) {
        $enteredCode = $_POST['verification_code'];
        if ($enteredCode === $_SESSION['verification_code']) {
            registerEmail($_SESSION['email']);
            echo "<p>Email verified and registered!</p>";
        } else {
            echo "<p>Incorrect verification code.</p>";
        }
    }
}
?>

<h2>Register for GitHub Timeline Updates</h2>
<form method="POST">
    <input type="email" name="email" required>
    <button id="submit-email">Submit</button>
</form>

<form method="POST">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>
