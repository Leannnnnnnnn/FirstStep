<?php
// Test if PHPMailer is installed
$composer_phpmailer = __DIR__ . '/vendor/autoload.php';
$manual_phpmailer = __DIR__ . '/PHPMailer/src/PHPMailer.php';

echo "<h2>PHPMailer Installation Check</h2>";

if (file_exists($composer_phpmailer)) {
    echo "✅ PHPMailer found (Composer)<br>";
    echo "Path: " . $composer_phpmailer;
} elseif (file_exists($manual_phpmailer)) {
    echo "✅ PHPMailer found (Manual)<br>";
    echo "Path: " . $manual_phpmailer;
} else {
    echo "❌ PHPMailer NOT found<br>";
    echo "Checked paths:<br>";
    echo "1. " . $composer_phpmailer . "<br>";
    echo "2. " . $manual_phpmailer . "<br>";
}
?>