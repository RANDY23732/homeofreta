# Hosting Guide - Home of RETA E-Commerce

## Pre-Hosting Checklist

### 1. Database Setup

**Step 1: Run Database Setup Script**
- Open in browser: `http://localhost/E-COMMECE/api/setup_database.php`
- This will create all 14 database tables with default data
- Expected output: `{"success": true, "message": "Database setup completed successfully"}`

**Tables Created:**
- users (customer and admin accounts)
- sessions (JWT token storage for cross-device auth)
- products (all 80 products)
- categories (9 default categories)
- shipping_methods (7 shipping options)
- payment_methods (8 payment options)
- orders (customer orders)
- cart (user shopping carts)
- wishlist (user wishlists)
- conversations (customer support chats)
- messages (chat messages)
- pinned_messages (pinned important messages)
- message_notes (message notes)
- reminders (user reminders)

### 2. Production Configuration

**Step 2: Configure Database Connection**
- Copy `api/production.config.example.php` to `api/config.php`
- Edit `api/config.php` with your hosting credentials:
```php
$host = "your_hosting_db_host";  // From hosting provider
$user = "your_db_username";     // From hosting provider
$password = "your_db_password";  // From hosting provider
$database = "your_db_name";     // From hosting provider
```

**Step 3: Update Frontend BASE_URL**
- Edit `index.html` line ~2925
- Change from: `const BASE_URL = 'http://localhost';`
- Change to: `const BASE_URL = 'https://yourdomain.com';`

### 3. Upload Files to Hosting

**Required Files:**
```
/E-COMMECE/
├── index.html (main application file)
├── api/
│   ├── db.php (database connection)
│   ├── config.php (production credentials - create this)
│   ├── setup_database.php (run once to setup DB)
│   ├── config.php (categories/shipping/payment API)
│   ├── products.php (products API)
│   ├── login.php (authentication)
│   ├── register.php (registration)
│   ├── logout.php (logout)
│   ├── sync_data.php (user data sync)
│   ├── validate_session.php (session validation)
│   ├── jwt_helper.php (JWT token handling)
│   ├── create_order.php (order creation)
│   ├── paypal_payment.php (PayPal integration)
│   ├── stripe_payment.php (Stripe integration)
│   └── send_order_email.php (email notifications)
├── uploads/ (product images folder - create this)
└── .htaccess (URL rewriting)
```

**File Permissions:**
- PHP files: 644
- Folders: 755
- `api/config.php`: 600 (for security - contains credentials)

### 4. Import Database (Alternative Method)

If you cannot run setup_database.php on hosting:

**Option A: Export Local Database**
1. Export your local `home_of_reta` database via phpMyAdmin
2. Import to hosting database via phpMyAdmin

**Option B: Run Setup Script**
1. Upload all files to hosting
2. Open `https://yourdomain.com/api/setup_database.php` in browser
3. This will create all tables automatically

### 5. Test After Hosting

**Test Checklist:**
- [ ] Open website - should load homepage
- [ ] Test customer registration
- [ ] Test customer login
- [ ] Test admin login (homeofreta@gmail.com)
- [ ] Verify all 80 products display in shop
- [ ] Test category filtering (select different categories)
- [ ] Test "All" category (should show all 80 products)
- [ ] Test add to cart
- [ ] Test checkout flow
- [ ] Test cross-device sync (login on different device)
- [ ] Verify admin can add/edit/delete products
- [ ] Verify admin can add/edit/delete categories
- [ ] Verify admin can add/edit/delete shipping methods
- [ ] Verify admin can add/edit/delete payment methods

### 6. Security Notes

**Important Security Steps:**
1. Never commit `api/config.php` to version control
2. Set strong database password
3. Use HTTPS (SSL certificate)
4. Keep PHP and MySQL updated
5. Regular backups of database
6. Monitor for suspicious activity

### 7. Troubleshooting

**Common Issues:**

**Database Connection Failed:**
- Check `api/config.php` credentials
- Verify database exists on hosting
- Check MySQL server is running
- Verify host address (sometimes not localhost on hosting)

**404 Errors on API calls:**
- Check BASE_URL in index.html
- Verify .htaccess is uploaded
- Check file permissions on api/ folder

**Images not loading:**
- Create uploads/ folder on hosting
- Set folder permissions to 755
- Upload product images to uploads/ folder

**Cross-device sync not working:**
- Verify sessions table exists
- Check JWT token is being stored in database
- Verify validate_session.php is accessible

### 8. Production Environment

**To enable production mode:**
Add to `api/config.php`:
```php
putenv('ENVIRONMENT=production');
```

This will:
- Disable error display
- Enable error logging
- Improve security

### 9. Firebase Configuration (Optional)

If using Firebase for image uploads:
- Update Firebase config in index.html
- Ensure Firebase project is accessible from your domain
- Add your domain to Firebase authorized domains

### 10. Email Configuration (Optional)

For order confirmation emails:
- Configure `api/email_config.php`
- Update SMTP settings with your email provider
- Test email sending functionality

## Support

If you encounter issues:
1. Check browser console for JavaScript errors
2. Check browser network tab for API errors
3. Check server error logs
4. Verify database tables exist via phpMyAdmin
5. Test API endpoints directly in browser

## Quick Start Summary

1. **Local:** Run `http://localhost/E-COMMECE/api/setup_database.php`
2. **Hosting:** Upload files, create `api/config.php`, update BASE_URL in `index.html`
3. **Test:** Open website, test login, verify products display

**All features are ready for hosting!**
