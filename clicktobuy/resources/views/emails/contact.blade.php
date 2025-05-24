<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 15px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        
        <div class="content">
            <p><strong>Name:</strong> {{ $contactData['name'] }}</p>
            <p><strong>Email:</strong> {{ $contactData['email'] }}</p>
            <p><strong>Subject:</strong> {{ $contactData['subject'] }}</p>
            
            <div style="margin-top: 20px;">
                <strong>Message:</strong>
                <p>{{ $contactData['message'] }}</p>
            </div>
        </div>
        
        <div class="footer">
            <p>This email was sent from the ClickToBuy contact form.</p>
            <p>&copy; {{ date('Y') }} ClickToBuy. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 