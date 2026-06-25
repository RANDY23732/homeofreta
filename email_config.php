<?php
/**
 * Gmail SMTP settings for verification emails.
 * IMPORTANT: To send actual emails, you must:
 * 1. Enable 2-Step Verification on the Gmail account (homeofreta@gmail.com)
 * 2. Create an App Password: https://myaccount.google.com/apppasswords
 * 3. Select "Mail" as the app
 * 4. Copy the 16-character app password (format: abcd efgh ijkl mnop)
 * 5. Paste it below WITHOUT spaces (e.g., 'abcdefghijklmn')
 * 
 * Without this password, emails will only work in local development mode
 * and will be logged to the Apache error log instead of being sent.
 */
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'homeofreta@gmail.com');
define('SMTP_PASS', ''); // PASTE YOUR 16-CHARACTER APP PASSWORD HERE WITHOUT SPACES
define('SMTP_FROM', 'homeofreta@gmail.com');
define('SMTP_FROM_NAME', 'HOME OF RETA');
