<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$CONFIG = [
    'smtp_host' => 'smtp.gmail.com', // Your SMTP server
    'smtp_port' => 587,
    'smtp_username' => 'your-email@gmail.com', // Your email
    'smtp_password' => 'your-app-password', // Your app password
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'DBMansion Labs',
    'book_url' => 'https://your-site.com/downloads/invisible-workforce.pdf',
    'csv_file' => 'subscribers.csv' // File to store emails
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

try {
    // Save email to CSV file
    saveEmailToCSV($email, $CONFIG['csv_file']);
    
    // Send welcome email
    sendWelcomeEmail($email, $CONFIG);
    
    echo json_encode([
        'success' => true,
        'message' => 'Email sent successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process subscription: ' . $e->getMessage()]);
}

function saveEmailToCSV($email, $csvFile) {
    $data = [
        date('Y-m-d H:i:s'),
        $email,
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $fp = fopen($csvFile, 'a');
    if ($fp === false) {
        throw new Exception('Could not open CSV file');
    }
    
    // Add header if file is empty
    if (filesize($csvFile) === 0) {
        fputcsv($fp, ['timestamp', 'email', 'user_agent', 'ip_address']);
    }
    
    fputcsv($fp, $data);
    fclose($fp);
}

function sendWelcomeEmail($email, $config) {
    require_once 'vendor/autoload.php'; // Composer autoload for PHPMailer
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['smtp_port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '📖 Your Free Copy of "The Invisible Workforce" is Here!';
        $mail->Body = getEmailTemplate($config['book_url']);
        
        $mail->send();
        
    } catch (Exception $e) {
        throw new Exception('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
}

function getEmailTemplate($bookUrl) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: "Poppins", Arial, sans-serif; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: linear-gradient(135deg, #111, #333); color: white; padding: 30px; text-align: center; }
            .content { background: white; padding: 30px; line-height: 1.6; }
            .button { 
                display: inline-block; 
                background: #2aff9f; 
                color: #111; 
                padding: 15px 30px; 
                text-decoration: none; 
                border-radius: 50px; 
                font-weight: 600; 
                margin: 20px 0;
            }
            .bonus-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .community-box { background: linear-gradient(45deg, #2aff9f, #00d4aa); color: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 Welcome to The Invisible Workforce!</h1>
                <p>Your AI transformation starts now</p>
            </div>
            
            <div class="content">
                <h2>Your Free Book is Ready! 📚</h2>
                <p>Hi there!</p>
                <p>Thanks for joining over 12,000+ entrepreneurs who are using AI to scale their businesses effortlessly. Your free copy of "The Invisible Workforce" is just one click away!</p>
                
                <div style="text-align: center;">
                    <a href="' . $bookUrl . '" class="button">📖 DOWNLOAD YOUR BOOK NOW</a>
                </div>
                
                <div class="bonus-box">
                    <h3>🎁 Your Exclusive Bonuses Are Also Ready:</h3>
                    <ul>
                        <li>✅ <strong>AI Tools Checklist</strong> - 50+ tools to automate your business ($47 value)</li>
                        <li>✅ <strong>30-Day Implementation Guide</strong> - Step-by-step action plan ($97 value)</li>
                        <li>✅ <strong>Email Templates Pack</strong> - Ready-to-use automation templates ($47 value)</li>
                        <li>✅ <strong>Weekly AI Strategy Sessions</strong> - Live Q&A with experts (Priceless)</li>
                    </ul>
                </div>
                
                <div class="community-box">
                    <h3>🌎 Welcome to Our Elite Community!</h3>
                    <p>You now have access to our exclusive community of 2,500+ entrepreneurs from 47 countries who are already using AI to scale their businesses 10x faster!</p>
                    <p style="text-align: center;">
                        <a href="#" style="background: white; color: #2aff9f;" class="button">Join Community Now</a>
                    </p>
                </div>
                
                <h3>🚀 What to do next:</h3>
                <ol>
                    <li><strong>Download your book</strong> using the link above</li>
                    <li><strong>Read Chapter 1</strong> to discover the #1 mistake businesses make</li>
                    <li><strong>Join our community</strong> to connect with like-minded entrepreneurs</li>
                    <li><strong>Implement the first AI strategy</strong> within 24 hours</li>
                </ol>
                
                <p>I can\'t wait to see how AI transforms your business! Reply to this email if you have any questions.</p>
                
                <p>To your success,<br>
                <strong>The DBMansion Labs Team</strong></p>
                
                <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                <p style="font-size: 12px; color: #666;">
                    You received this email because you downloaded "The Invisible Workforce" from our website. 
                    If you no longer wish to receive emails, you can <a href="#">unsubscribe here</a>.
                </p>
            </div>
        </div>
    </body>
    </html>';
}
?>
