<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meta & WhatsApp Setup Guide - CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.6;
            padding: 2rem 1rem;
            margin: 0;
        }
        .container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #e2e8f0;
        }
        .header-badge {
            display: inline-block;
            background: rgba(14, 165, 233, 0.1);
            color: #0284c7;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }
        .subtitle {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 2rem;
        }
        h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        p {
            color: #475569;
            margin-bottom: 1rem;
        }
        ol, ul {
            padding-left: 1.5rem;
            color: #334155;
            margin-bottom: 1.25rem;
        }
        li {
            margin-bottom: 0.6rem;
        }
        li::marker {
            font-weight: 700;
            color: #0284c7;
        }
        code {
            background: #f1f5f9;
            color: #0f172a;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-family: monospace;
            border: 1px solid #e2e8f0;
        }
        .alert {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 1.25rem;
            margin: 1.5rem 0;
            border-radius: 0 10px 10px 0;
            color: #0369a1;
        }
        .alert-warning {
            background: #fffbeb;
            border-left-color: #f59e0b;
            color: #b45309;
        }
        .alert-tip {
            background: #f0fdf4;
            border-left-color: #10b981;
            color: #15803d;
        }
        a {
            color: #0284c7;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
        .step-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .btn-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #0f172a;
            color: #ffffff !important;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.5rem 0 1rem 0;
            font-size: 0.9rem;
        }
        .btn-link:hover {
            text-decoration: none;
            background: #1e293b;
        }
        .screenshot {
            width: 100%;
            max-width: 700px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin: 1.5rem 0;
            display: block;
        }
        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="header-badge">Complete Setup Guide</span>
        <h1>Meta (Facebook & WhatsApp) Integration Guide</h1>
        <p class="subtitle">Follow this step-by-step tutorial to create your Meta Business Account, setup WhatsApp Cloud API, and connect Social Media & Lead Ads to your CRM.</p>

        <!-- STEP 0 -->
        <h2>Step 0: Prerequisites (Meta Business Account & Facebook Page)</h2>
        <div class="step-card">
            <p>Before creating the Developer App, you must have a <strong>Meta Business Account (Business Portfolio)</strong> and a <strong>Facebook Business Page</strong>.</p>
            
            <h3>A. Create a Meta Business Account (If you don't have one)</h3>
            <ol>
                <li>Go to <a href="https://business.facebook.com/overview" target="_blank" class="btn-link">Meta Business Overview ↗</a></li>
                <li>Click <strong>Create an account</strong>.</li>
                <li>Enter your Official Business Name, your name, and your work email address. Click <strong>Submit</strong>.</li>
                <li>Check your email inbox and click the confirmation link sent by Meta.</li>
            </ol>
            <img src="/images/guide/meta_business_creation.png" alt="Meta Business Creation" class="screenshot">

            <h3>B. Create a Facebook Page for Your Business</h3>
            <ol>
                <li>Go to <a href="https://facebook.com/pages/create" target="_blank">facebook.com/pages/create</a>.</li>
                <li>Enter your Business Name and Category (e.g., Solar Energy Company).</li>
                <li>Click <strong>Create Page</strong>.</li>
            </ol>
        </div>

        <!-- STEP 1 -->
        <h2>Step 1: Register as a Meta Developer & Create App</h2>
        <div class="step-card">
            <ol>
                <li>Go to the <a href="https://developers.facebook.com/" target="_blank" class="btn-link">Meta for Developers Dashboard ↗</a> and log in with your Facebook account.</li>
                <li>Click <strong>My Apps</strong> in the top right header, then click <strong>Create App</strong>.</li>
                <li>When asked <em>"What do you want your app to do?"</em>:
                    <ul>
                        <li>Select <strong>Other</strong> (or <strong>Connect a Business Portfolio</strong>) and click Next.</li>
                        <li>Select <strong>Business</strong> as the App Type and click Next.</li>
                    </ul>
                </li>
            </ol>
            <img src="/images/guide/meta_create_app.png" alt="Meta Create App" class="screenshot">
            <ol start="4">
                <li>Enter your <strong>App Display Name</strong> (e.g., <code>Solar CRM Connector</code>).</li>
                <li>Select your <strong>Business Portfolio</strong> (created in Step 0) from the dropdown list.</li>
                <li>Click <strong>Create App</strong> and re-enter your Facebook password if prompted.</li>
            </ol>
        </div>

        <!-- STEP 2 -->
        <h2>Step 2: Add WhatsApp Cloud API & Verify Phone Number</h2>
        <div class="step-card">
            <ol>
                <li>Inside your App Dashboard, scroll down under <strong>Add products to your app</strong>.</li>
                <li>Find <strong>WhatsApp</strong> and click <strong>Set up</strong>.</li>
                <li>In the left sidebar menu, click <strong>WhatsApp > API Setup</strong>.</li>
                <li>Scroll down to <strong>Step 5: Add a Phone Number</strong> and click <strong>Add Phone Number</strong>.</li>
            </ol>
            <img src="/images/guide/meta_whatsapp_setup.png" alt="WhatsApp API Setup" class="screenshot">
            <ol start="5">
                <li>Enter your Business Profile Name and select your Business Category. Click Next.</li>
                <li>Enter your official Business Phone Number (Mobile or Landline).
                    <div class="alert alert-warning" style="margin: 0.5rem 0;">
                        <strong>Important:</strong> This number must NOT be currently active on the consumer WhatsApp phone app. If it is, delete your account in the WhatsApp phone app first, or use a new/separate number.
                    </div>
                </li>
                <li>Choose SMS or Phone Call verification, enter the 6-digit verification code, and click <strong>Verify</strong>.</li>
                <li>Once verified, copy these two values from the <strong>API Setup</strong> page:
                    <ul>
                        <li><strong>WhatsApp Phone Number ID</strong></li>
                        <li><strong>WhatsApp Business Account ID</strong></li>
                    </ul>
                </li>
            </ol>
        </div>

        <!-- STEP 3 -->
        <h2>Step 3: Enable Facebook Social Media & Lead Ads</h2>
        <div class="step-card">
            <ol>
                <li>In the left sidebar, click <strong>Add Product</strong> (or go back to Dashboard).</li>
                <li>Find <strong>Facebook Login for Business</strong> and click <strong>Set up</strong>.</li>
                <li>This enables your CRM to publish Social Media Posts and receive Facebook Lead Ads automatically.</li>
            </ol>
        </div>

        <!-- STEP 4 -->
        <h2>Step 4: Create a Permanent System User & Token</h2>
        <div class="step-card">
            <div class="alert alert-warning">
                <strong>Why System User?</strong> Temporary developer tokens expire every 24 hours. Creating a System User generates a <strong>Permanent Token</strong> that never expires, ensuring your CRM stays connected 24/7.
            </div>

            <ol>
                <li>Open <a href="https://business.facebook.com/settings" target="_blank" class="btn-link">Meta Business Settings ↗</a></li>
                <li>In the left sidebar under <strong>Users</strong>, click <strong>System Users</strong>.</li>
                <li>Click <strong>Add</strong>. Set the name as <code>CRM System User</code> and user role as <strong>Admin</strong>. Click Create.</li>
                <li>Click on the new System User, then click <strong>Add Assets</strong>:
                    <ul>
                        <li>Click <strong>Pages</strong>: Select your Facebook Page and enable <strong>Full Control</strong>.</li>
                        <li>Click <strong>Apps</strong>: Select your Developer App (created in Step 1) and enable <strong>Full Control</strong>.</li>
                        <li>Click <strong>Save Changes</strong>.</li>
                    </ul>
                </li>
                <li>Click <strong>Generate New Token</strong>.</li>
            </ol>
            <img src="/images/guide/meta_system_user.png" alt="Meta System User Setup" class="screenshot">
            <ol start="6">
                <li>Select your App from the dropdown list. Set Token Expiration to <strong>Never</strong> (or 60 days/Permanent).</li>
                <li>Scroll down and check the following permission checkboxes:
                    <ul>
                        <li><code>whatsapp_business_messaging</code></li>
                        <li><code>whatsapp_business_management</code></li>
                        <li><code>pages_manage_posts</code></li>
                        <li><code>pages_read_engagement</code></li>
                        <li><code>pages_show_list</code></li>
                        <li><code>leads_retrieval</code></li>
                    </ul>
                </li>
                <li>Click <strong>Generate Token</strong>.</li>
            </ol>

            <div class="alert alert-tip">
                <strong>Copy Immediately:</strong> Copy the long Access Token string generated on screen and paste it into a safe document. Meta will only display it once!
            </div>
        </div>

        <!-- STEP 5 -->
        <h2>Step 5: Copy Credentials into Your CRM Settings</h2>
        <div class="step-card">
            <p>You now have all 4 credentials ready! Provide these to your CRM Administrator or enter them under <strong>CRM > Settings > Integrations</strong>:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <th style="text-align: left; padding: 0.5rem 0;">Credential Name</th>
                    <th style="text-align: left; padding: 0.5rem 0;">Where to find it</th>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 0.6rem 0;"><strong>Permanent Access Token</strong></td>
                    <td style="padding: 0.6rem 0;">Meta Business Settings > System Users (Step 4)</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 0.6rem 0;"><strong>WhatsApp Phone Number ID</strong></td>
                    <td style="padding: 0.6rem 0;">Developer Dashboard > WhatsApp > API Setup (Step 2)</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 0.6rem 0;"><strong>WhatsApp Business Account ID</strong></td>
                    <td style="padding: 0.6rem 0;">Developer Dashboard > WhatsApp > API Setup (Step 2)</td>
                </tr>
                <tr>
                    <td style="padding: 0.6rem 0;"><strong>App ID & App Secret</strong></td>
                    <td style="padding: 0.6rem 0;">Developer Dashboard > App Settings > Basic</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin-top: 3rem; color: #94a3b8; font-size: 0.9rem;">
            &copy; {{ date('Y') }} Solar CRM. All rights reserved.
        </div>
    </div>
</body>
</html>
