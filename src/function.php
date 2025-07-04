<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
require_once __DIR__ . '/PHPMailer-master/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/PHPMailer-master/src/SMTP.php';

// ...existing code...
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
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    sendEmail($email, $subject, $body);
}

function sendUnsubscribeEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $body = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    sendEmail($email, $subject, $body);
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
        $message = $html . "<p><a href='$unsubscribeLink'>Unsubscribe</a></p>";
        sendEmail($email, "Latest GitHub Updates", $message);
    }
}

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ganishaverma2004@gmail.com';          
        $mail->Password   = 'ajaj bwlk dvwo gnma';            
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('ganishaverma2004@gmail.com', 'GitHub Timeline');  
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Email Error ({$to}): {$mail->ErrorInfo}";
    }
}
?>
