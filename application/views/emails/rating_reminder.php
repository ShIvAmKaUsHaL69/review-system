<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 0.9em; }
        .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Rating Submission Reminder</h2>
        </div>
        <div class="content">
            <p>Dear <?=$user_name?>,</p>
            
            <p>This is a friendly reminder that you haven't submitted your ratings for <?=date('F Y')?>. The rating period is coming to an end soon.</p>
            
            <p>Please take a moment to submit your ratings. Your feedback is valuable for everyone's growth and improvement.</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="<?=site_url?>" class="btn">Submit Ratings Now</a>
            </p>
            
            <p>If you've already submitted your ratings recently, please ignore this reminder.</p>
            
            <p>Best regards,<br>Rating System</p>
        </div>
        <div class="footer">
            This is an automated message. Please do not reply to this email.
        </div>
    </div>
</body>
</html> 