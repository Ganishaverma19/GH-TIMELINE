<?php

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        file_put_contents($file, '');
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!in_array($email, $emails)) {
        $code = generateVerificationCode();
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
        sendVerificationEmail($email, $code);
        file_put_contents(__DIR__ . "/codes/$email.txt", $code); // Save code for verification
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    mail($email, $subject, $message, $headers);
}

function sendUnsubscribeEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    // You can replace this mock with real fetch logic using file_get_contents() or cURL
    $mockData = [
        ['event' => 'Push', 'user' => 'testuser'],
        ['event' => 'Fork', 'user' => 'devganisha'],
    ];
    return $mockData;
}

function formatGitHubData($data) {
    $html = "<h2>GitHub Timeline Updates</h2><table border='1'><tr><th>Event</th><th>User</th></tr>";
    foreach ($data as $item) {
        $html .= "<tr><td>{$item['event']}</td><td>{$item['user']}</td></tr>";
    }
    $html .= "</table>";
    return $html;
}

function getRegisteredEmails() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function sendGitHubUpdatesToSubscribers() {
    $emails = getRegisteredEmails();
    $data = fetchGitHubTimeline();
    $html = formatGitHubData($data);

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost/GH-TIMELINE/src/unsubscribe.php?email=" . urlencode($email);
        $message = $html . "<p><a href='$unsubscribeLink' id='unsubscribe-button'>Unsubscribe</a></p>";
        sendEmail($email, "Latest GitHub Updates", $message);
    }
}

function sendEmail($to, $subject, $message) {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    mail($to, $subject, $message, $headers);
}

?>
