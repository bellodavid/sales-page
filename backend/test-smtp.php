<?php
// Load environment variables
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        die("Error: .env file not found at $filePath");
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $_ENV[$name] = $value;
    }
}

// Load environment variables
loadEnv('.env');

// Simple SMTP test using PHP's mail() function first
echo "<h2>🧪 SMTP Configuration Test</h2>";

// Test 1: Basic PHP mail() function
echo "<h3>Test 1: Basic PHP Mail Function</h3>";
$to = $_ENV['NOTIFICATION_EMAIL'] ?? 'test@example.com';
$subject = "SMTP Test - " . date('Y-m-d H:i:s');
$message = "This is a test email to verify SMTP configuration is working.";
$headers = "From: " . ($_ENV['SMTP_USERNAME'] ?? 'noreply@example.com');

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Basic mail() function test: SUCCESS<br>";
} else {
    echo "❌ Basic mail() function test: FAILED<br>";
}

// Test 2: PHPMailer SMTP test
echo "<h3>Test 2: PHPMailer SMTP Test</h3>";

// Check if PHPMailer is available
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        
        // Enable verbose debug output
        $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
        $mail->Debugoutput = 'html';
        
        // Email content
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'SMTP Test');
        $mail->addAddress($_ENV['NOTIFICATION_EMAIL']);
        $mail->Subject = 'PHPMailer SMTP Test - ' . date('Y-m-d H:i:s');
        $mail->Body = 'This is a test email sent via PHPMailer to verify SMTP configuration.';
        
        $mail->send();
        echo "<br>✅ PHPMailer SMTP test: SUCCESS - Email sent successfully!<br>";
        
    } catch (Exception $e) {
        echo "<br>❌ PHPMailer SMTP test: FAILED<br>";
        echo "Error: {$mail->ErrorInfo}<br>";
    }
} else {
    echo "❌ PHPMailer not installed. Run 'composer install' first.<br>";
}

// Display current configuration (without sensitive data)
echo "<h3>📋 Current SMTP Configuration:</h3>";
echo "<ul>";
echo "<li>SMTP Host: " . ($_ENV['SMTP_HOST'] ?? 'Not set') . "</li>";
echo "<li>SMTP Port: " . ($_ENV['SMTP_PORT'] ?? 'Not set') . "</li>";
echo "<li>SMTP Username: " . ($_ENV['SMTP_USERNAME'] ?? 'Not set') . "</li>";
echo "<li>SMTP Password: " . (isset($_ENV['SMTP_PASSWORD']) ? '••••••••' : 'Not set') . "</li>";
echo "<li>From Email: " . ($_ENV['FROM_EMAIL'] ?? 'Not set') . "</li>";
echo "<li>Notification Email: " . ($_ENV['NOTIFICATION_EMAIL'] ?? 'Not set') . "</li>";
echo "</ul>";

echo "<h3>💡 Tips:</h3>";
echo "<ul>";
echo "<li>If both tests fail, check your SMTP credentials</li>";
echo "<li>If basic mail() works but PHPMailer fails, check SMTP settings</li>";
echo "<li>Check your hosting provider's SMTP documentation</li>";
echo "<li>Some hosts require specific SMTP settings</li>";
echo "</ul>";
?>
