<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 36px;
            margin: 0;
            padding: 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            font-size: 18px;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .qr-code {
            text-align: center;
            margin-bottom: 20px;
            width: 40px;
            height: 40px;
            float: right;
        }
        .qr-code img {
            max-width: 100%;
            height: auto;
        }
        .footer {
            text-align: center;
        }
        .footer p {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Certificate of Completion</h1>
        </div>
        <div class="content">
            <p>This certificate is awarded to:</p>
            <p><strong>First Name , Name</strong></p>
            <p>For completing the course:</p>
            <p><strong>Internship Title</strong></p>
            <p>On 2020/blabla</p>
        </div>
        <div class="qr-code">
        {{ $qrCode }}
        </div>
        <div class="footer">
            <p>Certificate ID: {{ $token }}</p>
        </div>
    </div>
</body>
</html>