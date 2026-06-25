-- Home page: INDEX.html
-- Create Database
CREATE DATABASE IF NOT EXISTS home_of_reta;
USE home_of_reta;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uid VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Sessions Table for JWT token management
CREATE TABLE IF NOT EXISTS sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- Products Table (70 products will be stored here)
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    rating DECIMAL(3,1) DEFAULT 4.5,
    description TEXT,
    benefits TEXT,
    usage_instructions TEXT,
    image_url VARCHAR(500),
    bestseller BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_bestseller (bestseller)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    customer_name VARCHAR(100),
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_method VARCHAR(50),
    shipping_fee DECIMAL(10,2) DEFAULT 0,
    payment_method VARCHAR(50),
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_customer_id (customer_id),
    INDEX idx_order_id (order_id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    product_id INT,
    product_name VARCHAR(200),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
);

-- Cart Table for cross-device sync
CREATE TABLE IF NOT EXISTS cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT,
    product_name VARCHAR(200),
    product_price DECIMAL(10,2),
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id)
);

-- Wishlist Table for cross-device sync
CREATE TABLE IF NOT EXISTS wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id)
);

-- Conversations Table for messaging
CREATE TABLE IF NOT EXISTS conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50),
    customer_email VARCHAR(100) NOT NULL,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    items JSON,
    last_message TEXT,
    unread_count JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_email (customer_email),
    INDEX idx_order_id (order_id)
);

-- Messages Table for messaging
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender VARCHAR(100) NOT NULL,
    sender_name VARCHAR(100),
    type ENUM('text', 'file') DEFAULT 'text',
    content TEXT,
    file_data JSON,
    reply_to INT,
    reply_to_sender VARCHAR(100),
    reply_to_text TEXT,
    visible_to ENUM('both', 'customer', 'admin') DEFAULT 'both',
    deleted_for_me JSON,
    deleted_for_everyone BOOLEAN DEFAULT FALSE,
    deleted_for_everyone_at TIMESTAMP NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_sender (sender)
);

-- Pinned Messages Table
CREATE TABLE IF NOT EXISTS pinned_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    conversation_id INT,
    message_id INT,
    content TEXT,
    sender VARCHAR(100),
    pinned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_conversation_id (conversation_id)
);

-- Message Notes Table
CREATE TABLE IF NOT EXISTS message_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    conversation_id INT,
    message_id INT,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_message (user_id, message_id),
    INDEX idx_user_id (user_id)
);

-- Reminders Table
CREATE TABLE IF NOT EXISTS reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    product_name VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user_id (user_id)
);

-- Payments Table for Stripe/PayPal integration
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_id VARCHAR(50),
    payment_id VARCHAR(100) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending', 'processing', 'succeeded', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('stripe', 'paypal', 'crypto') NOT NULL,
    payment_method_id VARCHAR(100),
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id),
    INDEX idx_payment_id (payment_id),
    INDEX idx_status (status)
);

-- Insert Admin User (password: homeofreta.com)
-- To generate password hash: echo password_hash('homeofreta.com', PASSWORD_DEFAULT);
INSERT INTO users (uid, email, fullname, phone, password, role) VALUES
('admin_001', 'homeofreta@gmail.com', 'System Admin', '653622655', '$2y$10$bqxxCYX/9kzYSljfnHRK3O3FOKt6H1iyBQBgZwqHI7saPJ6wI/xry', 'admin');

-- Insert Sample Products (First 10 of your 70 products)
INSERT INTO products (name, category, price, old_price, stock, rating, description, benefits, usage_instructions, image_url, bestseller) VALUES
('RETA Mass 1500', 'Weight Gain', 69.99, 94.99, 45, 4.8, '1500 calories per serving for extreme mass gain', 'Rapid weight gain, muscle fullness', '2 scoops with milk or water post-workout', 'WhatsApp Image 2026-04-28 at 21.33.13 (1).jpeg', 1),
('Serious Mass Gainer', 'Weight Gain', 59.99, 79.99, 32, 4.7, '1250 calories, 50g protein per serving', 'Lean mass gain, recovery support', '2 scoops daily between meals', 'Serious Mass Gainer.jpg', 1),
('True Mass Heavyweight', 'Weight Gain', 64.99, 84.99, 28, 4.6, '700 calories, muscle builder formula', 'Clean bulk, steady gains', '1-2 scoops post-workout', 'True Mass Heavyweight.jfif', 0),
('Pro Gainer Complex', 'Weight Gain', 72.99, 99.99, 35, 4.7, 'High protein mass gainer with digestive enzymes', 'Easy digestion, quality mass', '1 scoop with breakfast', 'Pro Gainer Complex.jpg', 0),
('Mega Mass 2000', 'Weight Gain', 89.99, 119.99, 25, 4.8, '2000 calories per serving for hardgainers', 'Maximum calorie density', '2 scoops with whole milk', 'Mega Mass 2000.jpg', 0),
('RETA Gold Whey Isolate', 'Build Muscle', 69.99, 94.99, 55, 4.9, '25g protein per scoop, zero sugar', 'Muscle protein synthesis, fast absorption', 'Post-workout or as needed', 'RETA Gold Whey Isolate.jfif', 1),
('Creatine Monohydrate', 'Build Muscle', 39.99, 54.99, 70, 4.8, 'Micronized creatine for maximum absorption', 'Strength, power, muscle hydration', '5g daily with water', 'Creatine Monohydrate.jpg', 1),
('Thermo Shred Fat Burner', 'Weight Loss', 49.99, 69.99, 50, 4.5, 'Thermogenic fat burner', 'Metabolism boost, energy', '2 capsules AM and PM', 'Thermo Shred Fat Burner.jpg', 0),
('RETA Pre-Workout', 'Pre-Workout', 54.99, 74.99, 55, 4.9, 'Explosive energy + pump formula', 'Focus, endurance, vascularity', '1 scoop 30min pre-workout', 'RETA Pre-Workout.jfif', 1),
('BCAA Recovery Xtreme', 'Recovery', 42.99, 58.99, 60, 4.7, '2:1:1 BCAA + electrolytes', 'Muscle repair, hydration', 'Intra-workout or post', 'BCAA Recovery Xtreme.jfif', 0);

-- Continue inserting remaining 60 products (add all your products here)
-- For brevity, I've shown first 10. Add all 70 products similarly.
