<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Declined</title>
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
        blockquote {
            margin: 0 0 20px;
            padding: 20px;
            border-left: 5px solid #eee;
            background-color: #f5f5f5;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://github.com/AmineZCS/internship-management-vue/blob/main/src/assets/IA.png?raw=true" alt="Logo" class="logo">
        
        <p>Dear <b>{{ $data['fname'] }} {{ $data['lname'] }}</b>,</p>
        <p>We regret to inform you that your application for the <b>{{ $data['position'] }}</b> Internship has been declined.</p>
        <!-- blockquoto the feedback   -->
        <blockquote>
            <p>{{ $data['feedback'] }}</p>
            <!-- author -->
            <cite>~{{$data['name']}}</cite>
        </blockquote>
        <p>Thank you for your interest in the company. We wish you the best of luck in your future endeavors.</p>
        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
        <p>Best regards,</p>
        <p>The Website Team</p>
    </div>
</body>
</html>