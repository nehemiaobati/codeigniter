<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>Hello <?= esc($name) ?>,</p>
        <p>You recently requested to reset your password. Please click the link below to set a new password:</p>
        <p style="text-align: center;">
            <a href="<?= esc($resetLink) ?>" class="button">Reset Password</a>
        </p>
        <p>If you did not request a password reset, please ignore this email.</p>
        <p>This link will expire in 1 hour.</p>
        <p>Thank you,<br>Your Application Team</p>
    </div>
</body>
</html>
