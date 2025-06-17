<?php
include "function.php";

// Read registered emails
$emails = getRegisteredEmails();

if (empty($emails)) {
    exit("No registered emails.\n");
}

// Fetch GitHub timeline content (replace with real API content if desired)
$content = "Check out the latest updates on GitHub Timeline!";

// Send email to each verified user
foreach ($emails as $email) {
    sendEmail($email, "GitHub Timeline Update", $content);
}

echo "Emails sent.\n";
?>
