// Zapier Integration Configuration
const CONFIG = {
  // Zapier webhook URL - Replace with your actual webhook
  ZAPIER_WEBHOOK: "https://hooks.zapier.com/hooks/catch/YOUR_WEBHOOK_ID",

  // Book download URL (optional - can be sent via email)
  BOOK_DOWNLOAD_URL: "https://your-site.com/downloads/invisible-workforce.pdf",

  // Integration method
  INTEGRATION_METHOD: "zapier",
};

// DOM Elements
const stickyCtaButton = document.getElementById("book-sticky-cta");
const emailForm = document.getElementById("email-form");
const emailInput = document.getElementById("email-input");
const firstNameInput = document.getElementById("first-name");
const phoneNumberInput = document.getElementById("phone-number");
const countryCodeSelect = document.getElementById("country-code");

// Smooth scroll functionality for sticky CTA
function scrollToCTA() {
  document.getElementById("book-cta").scrollIntoView({
    behavior: "smooth",
  });
}

// Form submission handler
function handleFormSubmission(event) {
  event.preventDefault();

  const email = emailInput.value.trim();

  if (!email) {
    alert("Please enter your email address.");
    return;
  }

  if (!isValidEmail(email)) {
    alert("Please enter a valid email address.");
    return;
  }

  // Show success message (you can replace this with actual form submission logic)
  showSuccessMessage();

  // Reset form
  emailForm.reset();
}

// Email validation function
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Show success message
function showSuccessMessage() {
  const ctaSection = document.getElementById("book-cta");
  const originalHTML = ctaSection.innerHTML;

  ctaSection.innerHTML = `
    <h2>Thank You! 🎉</h2>
    <p>Your free copy of "The Invisible Workforce" is on its way to your inbox. Check your email in a few minutes!</p>
    <a href="#book-header" class="book-btn">Back to Top</a>
  `;

  // Revert back after 5 seconds (optional)
  setTimeout(() => {
    ctaSection.innerHTML = originalHTML;
    // Re-attach event listener to the new form
    const newForm = document.getElementById("email-form");
    if (newForm) {
      newForm.addEventListener("submit", handleFormSubmission);
    }
  }, 5000);
}

// Sticky CTA visibility based on scroll position
function handleStickyCtaVisibility() {
  const ctaSection = document.getElementById("book-cta");
  const ctaSectionTop = ctaSection.offsetTop;
  const scrollPosition = window.pageYOffset;
  const windowHeight = window.innerHeight;

  // Hide sticky CTA when the main CTA section is visible
  if (scrollPosition + windowHeight >= ctaSectionTop) {
    stickyCtaButton.style.display = "none";
  } else {
    stickyCtaButton.style.display = "block";
  }
}

// Smooth scroll for all CTA buttons
function initializeSmoothScroll() {
  const ctaButtons = document.querySelectorAll('a[href="#book-cta"]');

  ctaButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      scrollToCTA();
    });
  });
}

// Add subtle animations on scroll
function initializeScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1";
        entry.target.style.transform = "translateY(0)";
      }
    });
  }, observerOptions);

  // Observe sections for animation
  const sections = document.querySelectorAll(".book-section");
  sections.forEach((section) => {
    section.style.opacity = "0";
    section.style.transform = "translateY(20px)";
    section.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(section);
  });
}

// Enhanced functionality for urgency and social proof
let downloadCount = 847;
let remainingCopies = 153;

// Countdown Timer with auto-restart
function initializeCountdown() {
  let endTime = new Date().getTime() + 24 * 60 * 60 * 1000; // 24 hours from now

  function updateCountdown() {
    const now = new Date().getTime();
    const timeLeft = endTime - now;

    if (timeLeft > 0) {
      const hours = Math.floor(timeLeft / (1000 * 60 * 60));
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

      document.getElementById("hours").textContent = hours
        .toString()
        .padStart(2, "0");
      document.getElementById("minutes").textContent = minutes
        .toString()
        .padStart(2, "0");
      document.getElementById("seconds").textContent = seconds
        .toString()
        .padStart(2, "0");
    } else {
      // Timer expired - restart with new 24-hour period
      endTime = new Date().getTime() + 24 * 60 * 60 * 1000;

      // Flash effect to indicate timer reset
      const timerContainer = document.querySelector(".countdown-container");
      if (timerContainer) {
        timerContainer.style.animation = "flash-reset 0.5s ease-in-out";
        setTimeout(() => {
          timerContainer.style.animation = "";
        }, 500);
      }

      // Update the timer text briefly to show it's reset
      const timerHeading = timerContainer.querySelector("h3");
      const originalText = timerHeading.textContent;
      timerHeading.textContent = "🔄 OFFER EXTENDED - NEW 24 HOURS!";
      timerHeading.style.color = "#2aff9f";

      setTimeout(() => {
        timerHeading.textContent = originalText;
        timerHeading.style.color = "";
      }, 3000);
    }
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
}

// Social Proof Counter Animation
function animateCounter(elementId, target) {
  const element = document.getElementById(elementId);
  if (!element) return;

  const start = parseInt(element.textContent) || 0;
  const increment = Math.ceil((target - start) / 100);
  let current = start;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }
    element.textContent = current;
  }, 20);
}

// Recent Activity Notifications
function showRecentActivity() {
  const names = [
    "Jennifer from Miami",
    "Mike from Seattle",
    "Sarah from Austin",
    "David from Boston",
    "Lisa from Denver",
    "Tom from Portland",
    "Amy from Chicago",
    "John from Phoenix",
    "Kate from Atlanta",
  ];

  const activityElement = document.getElementById("recent-activity");
  if (!activityElement) return;

  function showNotification() {
    const randomName = names[Math.floor(Math.random() * names.length)];
    activityElement.innerHTML = `<p>🔥 <strong>${randomName}</strong> just downloaded their copy</p>`;
    activityElement.classList.add("show");

    // Update counters
    downloadCount += Math.floor(Math.random() * 3) + 1;
    remainingCopies = Math.max(
      50,
      remainingCopies - Math.floor(Math.random() * 2) - 1
    );

    document.getElementById("download-counter").textContent = downloadCount;
    document.getElementById("remaining-copies").textContent = remainingCopies;

    setTimeout(() => {
      activityElement.classList.remove("show");
    }, 4000);
  }

  // Show first notification after 3 seconds
  setTimeout(showNotification, 3000);

  // Then show notifications every 15-30 seconds
  setInterval(() => {
    if (Math.random() > 0.3) {
      // 70% chance
      showNotification();
    }
  }, Math.random() * 15000 + 15000);
}

// Enhanced form validation with complete data collection
async function enhancedFormValidation(event) {
  event.preventDefault();

  // Get all form values
  const firstName = firstNameInput.value.trim();
  const email = emailInput.value.trim();
  const phoneNumber = phoneNumberInput.value.trim();
  const countryCode = countryCodeSelect.value;

  // Validation
  if (!firstName) {
    showUrgentAlert("⚠️ Please enter your first name to get your free copy!");
    firstNameInput.focus();
    return;
  }

  if (!email) {
    showUrgentAlert("⚠️ Please enter your email address!");
    emailInput.focus();
    return;
  }

  if (!isValidEmail(email)) {
    showUrgentAlert("⚠️ Please enter a valid email address!");
    emailInput.focus();
    return;
  }

  if (!countryCode) {
    showUrgentAlert("⚠️ Please select your country!");
    countryCodeSelect.focus();
    return;
  }

  if (!phoneNumber) {
    showUrgentAlert("⚠️ Please enter your phone number!");
    phoneNumberInput.focus();
    return;
  }

  // Show loading state
  const submitButton = event.target.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.textContent = "📤 SENDING...";
  submitButton.disabled = true;

  try {
    // Send data to Zapier
    await sendToZapier({
      firstName,
      email,
      phoneNumber,
      countryCode,
      country: countryCode.split("-")[1], // Extract country code
      fullPhone: countryCode.split("-")[0] + phoneNumber,
      timestamp: new Date().toISOString(),
      source: "invisible-workforce-landing",
      bookTitle: "The Invisible Workforce",
      downloadUrl: CONFIG.BOOK_DOWNLOAD_URL,
    });

    // Enhanced success message
    showEnhancedSuccessMessage(firstName);

    // Update counters
    downloadCount++;
    remainingCopies = Math.max(50, remainingCopies - 1);
    document.getElementById("download-counter").textContent = downloadCount;
    document.getElementById("remaining-copies").textContent = remainingCopies;

    // Reset form
    emailForm.reset();
  } catch (error) {
    console.error("Subscription error:", error);
    showUrgentAlert("⚠️ Something went wrong. Please try again in a moment.");
  } finally {
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  }
}

// Send data to Zapier webhook
async function sendToZapier(data) {
  const response = await fetch(CONFIG.ZAPIER_WEBHOOK, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    throw new Error("Failed to submit form");
  }

  return response.json();
}

// ConvertKit Integration
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
        tags: ["invisible-workforce-download"],
        fields: {
          source: "landing-page",
          download_date: new Date().toISOString(),
        },
      }),
    }
  );

  if (!response.ok) {
    throw new Error("Email subscription failed");
  }

  return response.json();
}

// Zapier Integration
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
    throw new Error("Email subscription failed");
  }

  return response.json();
}

// Backend Integration
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
      send_email: true,
    }),
  });

  if (!response.ok) {
    throw new Error("Email subscription failed");
  }

  return response.json();
}

// Show urgent alert message
function showUrgentAlert(message) {
  const alertDiv = document.createElement("div");
  alertDiv.style.cssText = `
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    z-index: 10000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    animation: slideDown 0.5s ease;
  `;

  alertDiv.textContent = message;
  document.body.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.style.animation = "slideUp 0.5s ease";
    setTimeout(() => document.body.removeChild(alertDiv), 500);
  }, 3000);
}

// Show enhanced success message with personalization
function showEnhancedSuccessMessage(firstName = "") {
  const ctaSection = document.getElementById("book-cta");
  const originalHTML = ctaSection.innerHTML;

  const personalGreeting = firstName ? `Hi ${firstName}! ` : "";
  const downloadSection =
    CONFIG.BOOK_DOWNLOAD_URL &&
    CONFIG.BOOK_DOWNLOAD_URL !==
      "https://your-site.com/downloads/invisible-workforce.pdf"
      ? `<div style="background: #2aff9f; color: white; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
         <h3 style="margin-bottom: 1rem;">📧 Email delayed? No problem!</h3>
         <p style="margin-bottom: 1rem;">Get instant access to your book while you wait:</p>
         <a href="${CONFIG.BOOK_DOWNLOAD_URL}" target="_blank" class="book-btn" 
            style="background: white; color: #2aff9f; margin: 0.5rem;">
           📖 INSTANT DOWNLOAD
         </a>
       </div>`
      : "";

  ctaSection.innerHTML = `
    <div style="text-align: center; padding: 3rem 2rem;">
      <h2 style="color: #2aff9f;">🎉 SUCCESS! ${personalGreeting}Your Copy is On The Way!</h2>
    <div style="text-align: center; padding: 3rem 2rem;">
      <h2 style="color: #2aff9f;">🎉 SUCCESS! Your Copy is On The Way!</h2>
      <p style="font-size: 1.2rem; margin: 1rem 0;">
        <strong>Check your email in the next 2-3 minutes</strong> for your free copy of 
        "The Invisible Workforce" plus all bonus materials!
      </p>
      
      ${downloadSection}
      
      <div style="background: #e8f5e8; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
        <h3 style="color: #2d5a2d; margin-bottom: 1rem;">What happens next:</h3>
        <ul style="text-align: left; max-width: 450px; margin: 0 auto;">
          <li>📧 Check your email inbox (and spam folder)</li>
          <li>📖 Download your free book + bonuses</li>
          <li>🌎 <strong>Get instant access to our private AI community</strong> - connect with 2,500+ entrepreneurs from 47 countries</li>
          <li>🚀 Start implementing AI in your business today</li>
          <li>💰 Watch your productivity soar while costs plummet</li>
        </ul>
      </div>
      <div style="background: linear-gradient(45deg, #2aff9f, #00d4aa); color: white; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <strong>🔥 BONUS:</strong> You'll also receive exclusive invites to our weekly AI strategy sessions where top entrepreneurs share their latest automation breakthroughs!
      </div>
      <a href="#book-header" class="book-btn">Back to Top</a>
    </div>
  `;

  // Confetti effect (simple version)
  createConfettiEffect();

  // Revert back after 15 seconds (longer to allow download)
  setTimeout(() => {
    ctaSection.innerHTML = originalHTML;
    const newForm = document.getElementById("email-form");
    if (newForm) {
      newForm.addEventListener("submit", enhancedFormValidation);
    }
    initializeCountdown();
  }, 15000);
}

function createConfettiEffect() {
  const colors = ["#2aff9f", "#ff6b6b", "#4ecdc4", "#45b7d1", "#96ceb4"];

  for (let i = 0; i < 50; i++) {
    setTimeout(() => {
      const confetti = document.createElement("div");
      confetti.style.cssText = `
        position: fixed;
        top: -10px;
        left: ${Math.random() * 100}%;
        width: 8px;
        height: 8px;
        background: ${colors[Math.floor(Math.random() * colors.length)]};
        z-index: 10000;
        pointer-events: none;
        animation: confetti-fall 3s linear forwards;
      `;

      document.body.appendChild(confetti);

      setTimeout(() => {
        if (confetti.parentNode) {
          confetti.parentNode.removeChild(confetti);
        }
      }, 3000);
    }, i * 50);
  }
}

// Add CSS for confetti animation
const confettiStyle = document.createElement("style");
confettiStyle.textContent = `
  @keyframes confetti-fall {
    to {
      transform: translateY(100vh) rotate(360deg);
    }
  }
  
  @keyframes slideDown {
    from { transform: translate(-50%, -100%); }
    to { transform: translate(-50%, 0); }
  }
  
  @keyframes slideUp {
    from { transform: translate(-50%, 0); }
    to { transform: translate(-50%, -100%); }
  }
`;
document.head.appendChild(confettiStyle);

// Add exit-intent popup trigger
let exitIntentShown = false;
document.addEventListener("mouseleave", function (e) {
  if (e.clientY <= 0 && !exitIntentShown) {
    exitIntentShown = true;
    showExitIntentPopup();
  }
});

// Exit Intent Popup
function showExitIntentPopup() {
  const popup = document.createElement("div");
  popup.id = "exit-popup";
  popup.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 20000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
  `;

  popup.innerHTML = `
    <div style="
      background: white;
      padding: 3rem 2rem;
      border-radius: 12px;
      max-width: 500px;
      text-align: center;
      position: relative;
      animation: slideIn 0.3s ease;
    ">
      <button onclick="document.body.removeChild(document.getElementById('exit-popup'))" 
              style="
                position: absolute;
                top: 15px;
                right: 20px;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #999;
              ">×</button>
      <h2 style="color: #ff6b6b; margin-bottom: 1rem;">Wait! Don't Leave Empty-Handed! 🚨</h2>
      <p style="margin-bottom: 1.5rem; color: #333;">
        You're about to miss out on the <strong>exact AI strategies</strong> that could save you 
        20+ hours per week and $50K+ in hiring costs.
      </p>
      <div style="background: #f0f9ff; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border-left: 4px solid #2aff9f;">
        <strong>🌎 PLUS:</strong> Get instant access to our exclusive community of 2,500+ AI-powered entrepreneurs from 47 countries!
      </div>
      <p style="color: #ff6b6b; font-weight: 600; margin-bottom: 2rem;">
        This offer disappears when you close this page!
      </p>
      <button onclick="scrollToCTA(); document.body.removeChild(document.getElementById('exit-popup'));" 
              class="book-btn mega-btn">
        🚀 GET MY FREE COPY NOW
      </button>
    </div>
  `;

  document.body.appendChild(popup);
}

// Add exit popup styles
const exitPopupStyle = document.createElement("style");
exitPopupStyle.textContent = `
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideIn {
    from { transform: scale(0.8) translateY(-50px); }
    to { transform: scale(1) translateY(0); }
  }
`;
document.head.appendChild(exitPopupStyle);

// Event Listeners
document.addEventListener("DOMContentLoaded", function () {
  // Initialize smooth scrolling
  initializeSmoothScroll();

  // Initialize scroll animations
  initializeScrollAnimations();

  // Sticky CTA click handler
  if (stickyCtaButton) {
    stickyCtaButton.addEventListener("click", scrollToCTA);
  }

  // Form submission handler
  if (emailForm) {
    emailForm.addEventListener("submit", enhancedFormValidation);
  }

  // Scroll handler for sticky CTA visibility
  window.addEventListener("scroll", handleStickyCtaVisibility);

  // Initial check for sticky CTA visibility
  handleStickyCtaVisibility();

  // Initialize countdown timer
  initializeCountdown();

  // Start social proof features
  animateCounter("download-counter", downloadCount);
  animateCounter("remaining-copies", remainingCopies);
  showRecentActivity();
});

// Optional: Add keyboard navigation support
document.addEventListener("keydown", function (event) {
  // Press 'C' to scroll to CTA
  if (event.key.toLowerCase() === "c" && !event.ctrlKey && !event.metaKey) {
    const activeElement = document.activeElement;
    // Only trigger if not typing in an input
    if (
      activeElement.tagName !== "INPUT" &&
      activeElement.tagName !== "TEXTAREA"
    ) {
      scrollToCTA();
    }
  }
});
