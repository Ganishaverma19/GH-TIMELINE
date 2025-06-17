<?php
// verify.php

// Path to the verification file (key:token, value:email)
$tokenFile = __DIR__ . '/pending_verifications.txt';
// Path to the registered users file
$registeredFile = __DIR__ . '/registered_emails.txt';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    $pending = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $found = false;

    foreach ($pending as $index => $line) {
        list($savedToken, $email) = explode(':', $line);
        if ($savedToken === $token) {
            // Save the email to registered_emails.txt
            file_put_contents($registeredFile, $email . PHP_EOL, FILE_APPEND | LOCK_EX);

            // Remove this entry from pending_verifications.txt
            unset($pending[$index]);
            file_put_contents($tokenFile, implode(PHP_EOL, $pending) . PHP_EOL);

            echo "Email <strong>$email</strong> has been verified successfully!";
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "Invalid or expired verification link.";
    }
} else {
    echo "No token provided.";
}
?>
