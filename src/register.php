<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $file = fopen("registered_emails.txt", "a");
        fwrite($file, $email . "\n");
        fclose($file);
        echo "Email registered successfully.";
    } else {
        echo "Invalid email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register Email</title></head>
<body>
    <form method="POST">
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Register</button>
    </form>
</body>
</html>
