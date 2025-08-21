# Google Sheets + Zapier Integration Setup Guide

## 📊 **Complete Setup for Lead Capture & Email Automation**

This setup will automatically:

1. ✅ Save all form submissions to Google Sheets
2. ✅ Send personalized welcome emails with book download
3. ✅ Track lead sources and timestamps
4. ✅ Cost under $20/month for unlimited leads

---

## 🎯 **Step 1: Create Google Sheet Database**

### **Create Your Spreadsheet:**

1. Go to [sheets.google.com](https://sheets.google.com)
2. Create a new spreadsheet
3. Name it: "Invisible Workforce Leads"
4. Set up columns in Row 1:

| A         | B          | C     | D            | E            | F          | G       | H      | I      |
| --------- | ---------- | ----- | ------------ | ------------ | ---------- | ------- | ------ | ------ |
| Timestamp | First Name | Email | Country Code | Phone Number | Full Phone | Country | Source | Status |

### **Sample Data (Row 2 for testing):**

```
2025-08-21 10:30:00 | John | john@email.com | +1-US | 5551234567 | +15551234567 | US | landing-page | new
```

---

## ⚡ **Step 2: Set Up Zapier Automation**

### **Create Zapier Account:**

1. Go to [zapier.com](https://zapier.com)
2. Sign up for free account (100 tasks/month free)
3. Click "Create Zap"

### **Configure the Zap:**

#### **Trigger: Webhooks by Zapier**

1. **App & Event:** Webhooks by Zapier → Catch Hook
2. **Test:** Copy the webhook URL (looks like: `https://hooks.zapier.com/hooks/catch/123456/abcdef`)
3. **Update your script.js:**
   ```javascript
   const CONFIG = {
     ZAPIER_WEBHOOK:
       "https://hooks.zapier.com/hooks/catch/YOUR_ACTUAL_WEBHOOK_URL",
     INTEGRATION_METHOD: "zapier",
   };
   ```

#### **Action 1: Google Sheets → Create Spreadsheet Row**

1. **App & Event:** Google Sheets → Create Spreadsheet Row
2. **Account:** Connect your Google account
3. **Spreadsheet:** Select "Invisible Workforce Leads"
4. **Worksheet:** Sheet1
5. **Map Fields:**
   - **Timestamp:** `timestamp` (from webhook)
   - **First Name:** `firstName` (from webhook)
   - **Email:** `email` (from webhook)
   - **Country Code:** `countryCode` (from webhook)
   - **Phone Number:** `phoneNumber` (from webhook)
   - **Full Phone:** `fullPhone` (from webhook)
   - **Country:** `country` (from webhook)
   - **Source:** `source` (from webhook)
   - **Status:** "new" (hardcoded)

#### **Action 2: Gmail → Send Email**

1. **App & Event:** Gmail → Send Email
2. **Account:** Connect your Gmail account
3. **Configure Email:**
   - **To:** `email` (from webhook)
   - **Subject:** `📖 Hi {{firstName}}! Your Free Copy of "The Invisible Workforce" is Here!`
   - **Body Type:** HTML
   - **Body:** Use the template below

---

## 📧 **Step 3: Email Template for Zapier**

Copy this HTML template into the Gmail body field:

```html
<!DOCTYPE html>
<html>
  <head>
    <style>
      body {
        font-family: "Poppins", Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: #f9f9fb;
      }
      .container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
      }
      .header {
        background: linear-gradient(135deg, #111, #333);
        color: white;
        padding: 30px;
        text-align: center;
      }
      .content {
        padding: 30px;
        line-height: 1.6;
      }
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
      .bonus-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
      }
      .community-box {
        background: linear-gradient(45deg, #2aff9f, #00d4aa);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        text-align: center;
      }
      .footer {
        background: #f8f9fa;
        padding: 20px;
        text-align: center;
        font-size: 12px;
        color: #666;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1>🎉 Hi {{firstName}}! Welcome to The Invisible Workforce!</h1>
        <p>Your AI transformation starts now</p>
      </div>

      <div class="content">
        <h2>Your Free Book is Ready! 📚</h2>
        <p>
          Thanks for joining over 12,000+ entrepreneurs who are using AI to
          scale their businesses effortlessly!
        </p>

        <div style="text-align: center;">
          <a
            href="https://your-site.com/downloads/invisible-workforce.pdf"
            class="button"
            >📖 DOWNLOAD YOUR BOOK NOW</a
          >
        </div>

        <div class="bonus-box">
          <h3>🎁 Your Exclusive Bonuses Are Also Ready:</h3>
          <ul>
            <li>
              ✅ <strong>AI Tools Checklist</strong> - 50+ tools to automate
              your business ($47 value)
            </li>
            <li>
              ✅ <strong>30-Day Implementation Guide</strong> - Step-by-step
              action plan ($97 value)
            </li>
            <li>
              ✅ <strong>Email Templates Pack</strong> - Ready-to-use automation
              templates ($47 value)
            </li>
            <li>
              ✅ <strong>Weekly AI Strategy Sessions</strong> - Live Q&A with
              experts (Priceless)
            </li>
          </ul>
        </div>

        <div class="community-box">
          <h3>🌎 Welcome to Our Elite Community!</h3>
          <p>
            You now have access to our exclusive community of 2,500+
            entrepreneurs from 47 countries!
          </p>
          <a
            href="https://your-community-link.com"
            style="background: white; color: #2aff9f;"
            class="button"
            >Join Community Now</a
          >
        </div>

        <h3>🚀 What to do next:</h3>
        <ol>
          <li><strong>Download your book</strong> using the link above</li>
          <li>
            <strong>Read Chapter 1</strong> to discover the #1 mistake
            businesses make
          </li>
          <li>
            <strong>Join our community</strong> to connect with like-minded
            entrepreneurs
          </li>
          <li>
            <strong>Implement the first AI strategy</strong> within 24 hours
          </li>
        </ol>

        <p>
          I can't wait to see how AI transforms your business! Reply to this
          email if you have any questions.
        </p>

        <p>
          To your success,<br />
          <strong>The DBMansion Labs Team</strong>
        </p>
      </div>

      <div class="footer">
        <p>
          You received this email because you downloaded "The Invisible
          Workforce" from our website.
        </p>
        <p>
          If you no longer wish to receive emails, you can
          <a href="#">unsubscribe here</a>.
        </p>
      </div>
    </div>
  </body>
</html>
```

---

## 🧪 **Step 4: Test Your Setup**

### **Test the Integration:**

1. **Update your webhook URL** in script.js
2. **Upload files** to your hosting
3. **Fill out the form** with test data
4. **Check Zapier** - should show successful trigger
5. **Check Google Sheets** - new row should appear
6. **Check email** - welcome email should arrive

### **Troubleshooting:**

- **No webhook trigger?** Check the URL is exactly copied
- **No Google Sheets row?** Verify spreadsheet permissions
- **No email sent?** Check Gmail connection and spam folder

---

## 📈 **Step 5: Advanced Features**

### **Add Follow-up Sequence:**

1. **Create additional Zaps** triggered by Google Sheets
2. **Use Delay actions** in Zapier for timing
3. **Set up conditional logic** based on engagement

### **Segment Your Leads:**

- **By Country:** Different messaging for different regions
- **By Phone:** SMS follow-up campaigns
- **By Source:** Track which traffic sources convert best

### **Analytics Integration:**

- **Add Google Analytics** tracking
- **Facebook Pixel** for retargeting
- **Connect to CRM** like HubSpot or Pipedrive

---

## 💰 **Costs Breakdown**

| Service           | Free Tier       | Paid Plan                |
| ----------------- | --------------- | ------------------------ |
| **Google Sheets** | Free            | Free                     |
| **Gmail**         | Free            | Free                     |
| **Zapier**        | 100 tasks/month | $19.99/month (750 tasks) |
| **Total Monthly** | $0              | $19.99                   |

### **Cost Per Lead:**

- **Free Tier:** $0 (up to 100 leads/month)
- **Paid Tier:** $0.027 per lead (750 leads at $19.99)

---

## 🎯 **Data You'll Collect**

With this setup, you'll have complete lead intelligence:

1. **📊 Contact Info:** Name, email, phone, country
2. **⏰ Timing:** When they signed up
3. **🌍 Geography:** Country-based segmentation
4. **📱 Communication:** Email + SMS capabilities
5. **📈 Tracking:** Source attribution and conversion data

This gives you everything you need to:

- Send targeted follow-up campaigns
- Analyze your best traffic sources
- Segment by geography for localized offers
- Build a comprehensive customer database

**Ready to launch your conversion machine! 🚀**
