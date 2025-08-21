<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception('.env file not found');
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue; // Skip invalid lines
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // Remove quotes and whitespace
        
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Configuration from environment variables
$CONFIG = [
    'smtp_host' => $_ENV['SMTP_HOST'] ?? 'localhost',
    'smtp_port' => (int)($_ENV['SMTP_PORT'] ?? 587),
    'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
    'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
    'from_email' => $_ENV['FROM_EMAIL'] ?? '',
    'from_name' => $_ENV['FROM_NAME'] ?? 'Default Name',
    'book_url' => $_ENV['BOOK_URL'] ?? '',
    'community_url' => $_ENV['COMMUNITY_URL'] ?? '#',
    'csv_file' => $_ENV['CSV_FILE'] ?? 'subscribers.csv'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate and sanitize input data
$firstName = htmlspecialchars(trim($input['firstName'] ?? ''));
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phoneNumber = htmlspecialchars(trim($input['phoneNumber'] ?? ''));
$countryCode = htmlspecialchars(trim($input['countryCode'] ?? ''));
$country = htmlspecialchars(trim($input['country'] ?? ''));
$fullPhone = htmlspecialchars(trim($input['fullPhone'] ?? ''));

// Validation
$errors = [];
if (empty($firstName)) {
    $errors[] = 'First name is required';
}
if (!$email) {
    $errors[] = 'Valid email address is required';
}
if (empty($phoneNumber)) {
    $errors[] = 'Phone number is required';
}
if (empty($countryCode)) {
    $errors[] = 'Country selection is required';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['error' => implode(', ', $errors)]);
    exit;
}

try {
    // Save subscriber data to CSV file
    saveSubscriberToCSV($firstName, $email, $phoneNumber, $countryCode, $country, $fullPhone, $CONFIG['csv_file']);
    
    // Send personalized welcome email
    sendWelcomeEmail($firstName, $email, $CONFIG);
    
    echo json_encode([
        'success' => true,
        'message' => 'Welcome email sent successfully! Check your inbox for your free book.',
        'firstName' => $firstName
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process subscription: ' . $e->getMessage()]);
}

function saveSubscriberToCSV($firstName, $email, $phoneNumber, $countryCode, $country, $fullPhone, $csvFile) {
    $data = [
        date('Y-m-d H:i:s'), // timestamp
        $firstName,
        $email,
        $phoneNumber,
        $countryCode,
        $country,
        $fullPhone,
        'landing-page', // source
        'new', // status
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $fp = fopen($csvFile, 'a');
    if ($fp === false) {
        throw new Exception('Could not open CSV file');
    }
    
    // Add header if file is empty
    if (filesize($csvFile) === 0) {
        fputcsv($fp, [
            'timestamp', 
            'first_name', 
            'email', 
            'phone_number', 
            'country_code', 
            'country', 
            'full_phone', 
            'source', 
            'status',
            'user_agent', 
            'ip_address'
        ]);
    }
    
    fputcsv($fp, $data);
    fclose($fp);
}

function sendWelcomeEmail($firstName, $email, $config) {
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
        $mail->addAddress($email, $firstName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "📖 Hi $firstName! Your Free Copy of \"The Invisible Workforce\" is Here!";
        $mail->Body = getEmailTemplate($firstName, $config['book_url'], $config['community_url']);
        
        $mail->send();
        
    } catch (Exception $e) {
        throw new Exception('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
}

function getEmailTemplate($firstName, $bookUrl, $communityUrl = '#') {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your Free Book is Here!</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: "Poppins", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                background: #f9f9fb; 
            }
            .email-container { 
                max-width: 600px; 
                margin: 0 auto; 
                background: white; 
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                border-radius: 8px;
                overflow: hidden;
            }
            .header { 
                background: linear-gradient(135deg, #111 0%, #333 50%, #111 100%); 
                color: white; 
                padding: 40px 30px; 
                text-align: center; 
                position: relative;
            }
            .header::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            }
            .header h1 { 
                font-size: 28px; 
                font-weight: 700; 
                margin-bottom: 10px; 
                position: relative;
                z-index: 1;
            }
            .header .subtitle { 
                font-size: 16px; 
                opacity: 0.9; 
                position: relative;
                z-index: 1;
            }
            .content { 
                padding: 40px 30px; 
                background: white; 
            }
            .greeting { 
                font-size: 18px; 
                color: #2c3e50; 
                margin-bottom: 20px; 
                font-weight: 600; 
            }
            .main-text { 
                font-size: 16px; 
                margin-bottom: 25px; 
                color: #555; 
            }
            .download-button { 
                display: inline-block; 
                background: linear-gradient(45deg, #2aff9f, #00d4aa); 
                color: #111; 
                padding: 18px 35px; 
                text-decoration: none; 
                border-radius: 50px; 
                font-weight: 700; 
                font-size: 16px;
                margin: 25px 0;
                text-align: center;
                box-shadow: 0 8px 25px rgba(42, 255, 159, 0.3);
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .download-section { 
                text-align: center; 
                margin: 30px 0; 
                padding: 25px;
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 12px;
                border: 2px dashed #2aff9f;
            }
            .bonus-section { 
                background: linear-gradient(135deg, #fff5f5, #f0f9ff); 
                padding: 30px; 
                border-radius: 12px; 
                margin: 30px 0; 
                border-left: 5px solid #2aff9f;
            }
            .bonus-section h3 { 
                color: #2c3e50; 
                margin-bottom: 20px; 
                font-size: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .bonus-list { 
                list-style: none; 
                margin: 0; 
                padding: 0; 
            }
            .bonus-list li { 
                padding: 10px 0; 
                border-bottom: 1px solid #e9ecef; 
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .bonus-list li:last-child { 
                border-bottom: none; 
            }
            .check-icon { 
                color: #2aff9f; 
                font-weight: bold; 
                font-size: 16px;
            }
            .community-section { 
                background: linear-gradient(45deg, #2aff9f, #00d4aa); 
                color: white; 
                padding: 30px; 
                border-radius: 12px; 
                margin: 30px 0; 
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            .community-section::before {
                content: "🌍";
                position: absolute;
                top: -20px;
                right: -20px;
                font-size: 120px;
                opacity: 0.1;
            }
            .community-section h3 { 
                margin-bottom: 15px; 
                font-size: 22px;
                position: relative;
                z-index: 1;
            }
            .community-section p { 
                margin-bottom: 20px; 
                font-size: 16px;
                position: relative;
                z-index: 1;
            }
            .community-button { 
                background: white; 
                color: #2aff9f; 
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                display: inline-block;
                position: relative;
                z-index: 1;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .next-steps { 
                background: #f8f9fa; 
                padding: 25px; 
                border-radius: 8px; 
                margin: 25px 0; 
            }
            .next-steps h3 { 
                color: #2c3e50; 
                margin-bottom: 15px; 
            }
            .next-steps ol { 
                padding-left: 20px; 
            }
            .next-steps li { 
                margin-bottom: 8px; 
                color: #555; 
            }
            .urgency-box {
                background: linear-gradient(45deg, #ff6b6b, #ee5a24);
                color: white;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: center;
                font-weight: 600;
            }
            .social-proof {
                background: #e8f5e8;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                border-left: 4px solid #2aff9f;
            }
            .footer { 
                background: #2c3e50; 
                color: #ecf0f1; 
                padding: 25px 30px; 
                text-align: center; 
                font-size: 14px; 
            }
            .footer a { 
                color: #2aff9f; 
                text-decoration: none; 
            }
            .signature {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #eee;
                font-style: italic;
                color: #666;
            }
            
            @media (max-width: 600px) {
                .content { padding: 25px 20px; }
                .header { padding: 30px 20px; }
                .header h1 { font-size: 24px; }
                .download-button { padding: 15px 25px; font-size: 14px; }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="header">
                <h1>🎉 Welcome to The Invisible Workforce!</h1>
                <div class="subtitle">Your AI transformation journey starts now, ' . htmlspecialchars($firstName) . '!</div>
            </div>
            
            <div class="content">
                <div class="greeting">Hi ' . htmlspecialchars($firstName) . '! 👋</div>
                
                <div class="main-text">
                    Thank you for joining our exclusive community of <strong>12,000+ entrepreneurs</strong> who are already using AI to scale their businesses effortlessly! 
                </div>
                
                <div class="urgency-box">
                    🔥 <strong>ACTION REQUIRED:</strong> Download your book within the next 24 hours to secure your bonuses!
                </div>
                
                <div class="download-section">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">📚 Your Free Book is Ready!</h3>
                    <p style="margin-bottom: 20px; color: #666;">Click below to instantly download your copy:</p>
                    <a href="' . htmlspecialchars($bookUrl) . '" class="download-button">
                        📖 DOWNLOAD YOUR BOOK NOW
                    </a>
                    <div style="font-size: 12px; color: #999; margin-top: 10px;">
                        Usually sells for $97 - Yours FREE today!
                    </div>
                </div>
                
                <div class="bonus-section">
                    <h3>🎁 Your Exclusive Bonuses (Worth $347):</h3>
                    <ul class="bonus-list">
                        <li><span class="check-icon">✅</span> <strong>AI Tools Checklist</strong> - 50+ tools to automate your business <em>($47 value)</em></li>
                        <li><span class="check-icon">✅</span> <strong>30-Day Implementation Guide</strong> - Step-by-step action plan <em>($97 value)</em></li>
                        <li><span class="check-icon">✅</span> <strong>Email Templates Pack</strong> - Ready-to-use automation templates <em>($47 value)</em></li>
                        <li><span class="check-icon">✅</span> <strong>Private Community Access</strong> - Connect with entrepreneurs globally <em>($147 value)</em></li>
                        <li><span class="check-icon">✅</span> <strong>Weekly AI Strategy Sessions</strong> - Live Q&A with experts <em>(Priceless)</em></li>
                    </ul>
                </div>
                
                <div class="social-proof">
                    💬 <strong>"This book saved me 20+ hours per week and $50K in hiring costs!"</strong> - Sarah Chen, Tech Startup Founder
                </div>
                
                <div class="community-section">
                    <h3>🌎 Welcome to Our Elite Community!</h3>
                    <p>You now have exclusive access to our private community of <strong>2,500+ successful entrepreneurs from 47 countries</strong> who are already using AI to scale their businesses 10x faster!</p>
                    <a href="' . htmlspecialchars($communityUrl) . '" class="community-button">Join Community Now →</a>
                </div>
                
                <div class="next-steps">
                    <h3>🚀 Your Next Steps (Do This Today):</h3>
                    <ol>
                        <li><strong>Download your book</strong> using the button above</li>
                        <li><strong>Read Chapter 1</strong> to discover the #1 mistake 90% of businesses make</li>
                        <li><strong>Join our community</strong> to connect with like-minded entrepreneurs</li>
                        <li><strong>Implement your first AI strategy</strong> within 24 hours (I show you exactly how)</li>
                        <li><strong>Watch for my next email</strong> with exclusive case studies</li>
                    </ol>
                </div>
                
                <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 25px 0;">
                    <p><strong>💡 Pro Tip:</strong> The entrepreneurs who implement what they learn in the first 48 hours see 3x faster results. Don\'t let this opportunity slip away!</p>
                </div>
                
                <div class="main-text">
                    I can\'t wait to see how AI transforms your business, ' . htmlspecialchars($firstName) . '! If you have any questions, just reply to this email - I personally read every response.
                </div>
                
                <div class="signature">
                    <p>To your success,<br>
                    <strong>The DBMansion Labs Team</strong><br>
                    <em>P.S. Remember, this free access won\'t last forever. Download now and start your transformation today!</em></p>
                </div>
            </div>
            
            <div class="footer">
                <p><strong>You\'re receiving this because you downloaded "The Invisible Workforce" from our website.</strong></p>
                <p style="margin-top: 10px;">
                    DBMansion Labs | Transforming Businesses with AI<br>
                    <a href="#">Unsubscribe</a> | <a href="#">Update Preferences</a> | <a href="#">Privacy Policy</a>
                </p>
            </div>
        </div>
    </body>
    </html>';
}
?>
