<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: justify;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: block;
            margin: 0 auto;
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="container">
    <img src="https://github.com/AmineZCS/internship-management-vue/blob/main/src/assets/IA.png?raw=true" alt="Logo" class="logo">
       <h1>Welcome to our website!</h1>
        <p>Dear {{ $user->fname }} {{ $user->lname }},</p>
        <p>Your password is: {{ $password }}</p>
        <p>Thank you for signing up for our website. We're excited to have you as a member of our community!</p>
        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
        <p>Best regards,</p>
        <p>The Website Team</p>
    </div>
</body>
</html>