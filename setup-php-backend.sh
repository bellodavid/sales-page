#!/bin/bash

echo "🐘 Setting up PHP Backend for The Invisible Workforce..."
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "index.html" ]; then
    echo "❌ Please run this script from the project root directory"
    exit 1
fi

# Create backend directory if it doesn't exist
if [ ! -d "backend" ]; then
    mkdir backend
    echo "✅ Created backend directory"
fi

# Navigate to backend directory
cd backend

# Check if Composer is installed
if command -v composer &> /dev/null; then
    echo "📦 Installing PHP dependencies with Composer..."
    composer install
    
    if [ $? -eq 0 ]; then
        echo "✅ Dependencies installed successfully"
    else
        echo "❌ Failed to install dependencies"
        echo "💡 You can install PHPMailer manually from: https://github.com/PHPMailer/PHPMailer"
    fi
else
    echo "⚠️  Composer not found. You'll need to install PHPMailer manually."
    echo "💡 Download from: https://github.com/PHPMailer/PHPMailer/releases"
    echo "📁 Extract to: backend/vendor/phpmailer/phpmailer/"
fi

# Create CSV file with proper permissions
echo "📄 Creating subscribers CSV file..."
touch subscribers.csv
chmod 666 subscribers.csv
echo "✅ CSV file created and permissions set"

# Create .htaccess for security
echo "🔒 Creating security .htaccess file..."
cat > .htaccess << 'EOF'
# Protect sensitive files
<Files "subscribers.csv">
    Order Allow,Deny
    Deny from all
</Files>

<Files "composer.json">
    Order Allow,Deny
    Deny from all
</Files>

<Files "composer.lock">
    Order Allow,Deny
    Deny from all
</Files>

# Enable CORS for form submissions
Header add Access-Control-Allow-Origin "*"
Header add Access-Control-Allow-Headers "Content-Type"
Header add Access-Control-Allow-Methods "POST"
EOF
echo "✅ Security .htaccess created"

# Go back to project root
cd ..

echo ""
echo "🎉 PHP Backend Setup Complete!"
echo "==============================="
echo ""
echo "📝 Next Steps:"
echo "1. Edit backend/subscribe.php and update SMTP settings"
echo "2. Update script.js with your backend URL"
echo "3. Upload files to your web server"
echo "4. Test the form submission"
echo ""
echo "📖 Read PHP-SETUP-GUIDE.md for detailed configuration instructions"
echo ""
echo "🔧 Quick Configuration:"
echo "   - Gmail SMTP: smtp.gmail.com:587"
echo "   - Use App Password (not regular password)"
echo "   - Enable 2-Factor Authentication first"
echo ""
echo "Happy launching! 🚀"
