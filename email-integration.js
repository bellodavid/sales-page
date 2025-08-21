// Simple email capture with backend integration
// Replace the existing form handler in script.js

// Configuration - Replace with your actual endpoints
const CONFIG = {
  // Option 1: ConvertKit Integration
  CONVERTKIT_FORM_ID: "YOUR_FORM_ID", // Get from ConvertKit
  CONVERTKIT_API_KEY: "YOUR_API_KEY", // Get from ConvertKit

  // Option 2: Simple backend endpoint
  BACKEND_URL: "https://your-backend.com/api/subscribe", // Your server

  // Option 3: Zapier webhook
  ZAPIER_WEBHOOK: "https://hooks.zapier.com/hooks/catch/YOUR_WEBHOOK_ID",

  // Book download URL
  BOOK_DOWNLOAD_URL: "https://your-site.com/downloads/invisible-workforce.pdf",
};

// Enhanced form submission with CRM integration
async function enhancedFormValidation(event) {
  event.preventDefault();

  const email = emailInput.value.trim();

  if (!email) {
    showUrgentAlert(
      "⚠️ Don't miss out! Enter your email to claim your free copy."
    );
    emailInput.focus();
    return;
  }

  if (!isValidEmail(email)) {
    showUrgentAlert(
      "⚠️ Please enter a valid email address to secure your copy!"
    );
    emailInput.focus();
    return;
  }

  // Show loading state
  const submitButton = event.target.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.textContent = "📤 SENDING...";
  submitButton.disabled = true;

  try {
    // Choose your integration method
    await integrateWithConvertKit(email);
    // await integrateWithBackend(email);
    // await integrateWithZapier(email);

    // Enhanced success message
    showEnhancedSuccessMessage();

    // Update counters
    downloadCount++;
    remainingCopies = Math.max(50, remainingCopies - 1);
    document.getElementById("download-counter").textContent = downloadCount;
    document.getElementById("remaining-copies").textContent = remainingCopies;

    emailForm.reset();
  } catch (error) {
    console.error("Subscription error:", error);
    showUrgentAlert("⚠️ Something went wrong. Please try again in a moment.");
  } finally {
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  }
}

// Option 1: ConvertKit Integration
async function integrateWithConvertKit(email) {
  const response = await fetch(
    `https://api.convertkit.com/v3/forms/${CONFIG.CONVERTKIT_FORM_ID}/subscribe`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        api_key: CONFIG.CONVERTKIT_API_KEY,
        email: email,
        tags: ["invisible-workforce-download"], // Tag for segmentation
        fields: {
          source: "landing-page",
          download_date: new Date().toISOString(),
          user_agent: navigator.userAgent,
        },
      }),
    }
  );

  if (!response.ok) {
    throw new Error("ConvertKit subscription failed");
  }

  return response.json();
}

// Option 2: Simple Backend Integration
async function integrateWithBackend(email) {
  const response = await fetch(CONFIG.BACKEND_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email,
      source: "invisible-workforce-landing",
      timestamp: new Date().toISOString(),
      book_url: CONFIG.BOOK_DOWNLOAD_URL,
      send_email: true, // Trigger automatic email
    }),
  });

  if (!response.ok) {
    throw new Error("Backend subscription failed");
  }

  return response.json();
}

// Option 3: Zapier Webhook Integration
async function integrateWithZapier(email) {
  const response = await fetch(CONFIG.ZAPIER_WEBHOOK, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email,
      book_title: "The Invisible Workforce",
      download_url: CONFIG.BOOK_DOWNLOAD_URL,
      source: "landing-page",
      timestamp: new Date().toISOString(),
    }),
  });

  if (!response.ok) {
    throw new Error("Zapier webhook failed");
  }

  return response.json();
}

// Email validation function (enhanced)
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email) && email.length <= 254;
}

// Enhanced success message with download link
function showEnhancedSuccessMessage() {
  const ctaSection = document.getElementById("book-cta");
  const originalHTML = ctaSection.innerHTML;

  ctaSection.innerHTML = `
    <div style="text-align: center; padding: 3rem 2rem;">
      <h2 style="color: #2aff9f;">🎉 SUCCESS! Your Copy is On The Way!</h2>
      <p style="font-size: 1.2rem; margin: 1rem 0;">
        <strong>Check your email in the next 2-3 minutes</strong> for your free copy of 
        "The Invisible Workforce" plus all bonus materials!
      </p>
      
      <div style="background: #2aff9f; color: white; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
        <h3 style="margin-bottom: 1rem;">📧 Didn't receive it yet?</h3>
        <p style="margin-bottom: 1rem;">Check your spam folder, or click below for instant access:</p>
        <a href="${CONFIG.BOOK_DOWNLOAD_URL}" 
           target="_blank" 
           class="book-btn" 
           style="background: white; color: #2aff9f; margin: 0.5rem;">
          📖 DOWNLOAD NOW
        </a>
      </div>
      
      <div style="background: #e8f5e8; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
        <h3 style="color: #2d5a2d; margin-bottom: 1rem;">What happens next:</h3>
        <ul style="text-align: left; max-width: 450px; margin: 0 auto;">
          <li>📧 Check your email inbox (and spam folder)</li>
          <li>📖 Download your free book + bonuses</li>
          <li>🌎 <strong>Get instant access to our private AI community</strong></li>
          <li>🚀 Start implementing AI in your business today</li>
          <li>💰 Watch your productivity soar while costs plummet</li>
        </ul>
      </div>
      
      <a href="#book-header" class="book-btn">Back to Top</a>
    </div>
  `;

  // Confetti effect
  createConfettiEffect();

  // Revert back after 15 seconds
  setTimeout(() => {
    ctaSection.innerHTML = originalHTML;
    const newForm = document.getElementById("email-form");
    if (newForm) {
      newForm.addEventListener("submit", enhancedFormValidation);
    }
    initializeCountdown();
  }, 15000);
}
