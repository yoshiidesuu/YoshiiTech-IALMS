<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .timestamp {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ SMTP Configuration Test</h1>
        <p>Your email system is working correctly!</p>
    </div>
    
    <div class="content">
        <div style="text-align: center;">
            <div class="success-icon">📧</div>
            <h2>Test Email Successful</h2>
            <p>Congratulations! Your SMTP configuration has been set up correctly and is working as expected.</p>
        </div>
        
        <div class="info-box">
            <h3>📋 Test Details</h3>
            <ul>
                <li><strong>Test Type:</strong> SMTP Configuration Verification</li>
                <li><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">✅ Successful</span></li>
                <li><strong>Sent From:</strong> {{ config('mail.from.address') }}</li>
                <li><strong>Sender Name:</strong> {{ config('mail.from.name') }}</li>
                <li><strong>SMTP Host:</strong> {{ config('mail.mailers.smtp.host') }}</li>
                <li><strong>SMTP Port:</strong> {{ config('mail.mailers.smtp.port') }}</li>
                <li><strong>Encryption:</strong> {{ strtoupper(config('mail.mailers.smtp.encryption') ?: 'None') }}</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>🔧 What This Means</h3>
            <p>This test email confirms that:</p>
            <ul>
                <li>Your SMTP server connection is working</li>
                <li>Authentication credentials are correct</li>
                <li>Email delivery is functioning properly</li>
                <li>Your application can send emails successfully</li>
            </ul>
        </div>
        
        <div class="timestamp">
            <strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s T') }}
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated test email sent from {{ config('app.name') }}</p>
        <p>If you received this email unexpectedly, please contact your system administrator.</p>
    </div>
</body>
</html>