-- SQL Script for Gym Management System

-- Create the database
CREATE DATABASE IF NOT EXISTS gym_management;
USE gym_management;

-- Table for Users
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('admin', 'user') DEFAULT 'user'
);

-- Table for Gym Plans
CREATE TABLE IF NOT EXISTS gym_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    plan_description TEXT,
    plan_price DECIMAL(10, 2) NOT NULL,
    plan_duration INT NOT NULL -- Duration in months
);

-- Table for Subscriptions
CREATE TABLE IF NOT EXISTS subscriptions (
    subscription_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('Pending', 'Paid', 'Approved', 'Rejected') DEFAULT 'Pending',
    payment_token VARCHAR(255) NULL, -- Added payment_token column
    payment_date DATETIME NULL, -- Added payment_date column
    approval_date TIMESTAMP NULL,
    admin_note TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES gym_plans(plan_id) ON DELETE CASCADE
);

-- Table for Attendance Records
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    check_in_time DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for Payments
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    khalti_token VARCHAR(255),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id)
);

-- Insert Sample Gym Plans (Prices adjusted for Khalti limits: Rs. 10 - Rs. 1000)
INSERT INTO gym_plans (plan_name, plan_description, plan_price, plan_duration) VALUES 
('Basic Plan', 'Access to basic gym equipment\nCardio area access\nLocker room access\nBasic fitness assessment', 100.00, 1),
('Standard Plan', 'All Basic Plan features\nGroup fitness classes\nPersonal trainer (2 sessions/month)\nNutrition consultation', 500.00, 3),
('Premium Plan', 'All Standard Plan features\nUnlimited personal training\nSauna access\nProtein shake per visit\nGuest passes (2/month)', 799.00, 6);

-- Insert Demo Users (passwords are hashed version of 'password')
INSERT INTO users (full_name, email, password, phone_number, role) VALUES
('Admin User', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9800000000', 'admin'),
('Premium Member', 'premium@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9811111111', 'user'),
('Regular Member', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9822222222', 'user');

-- Add Premium subscription for premium user
INSERT INTO subscriptions (user_id, plan_id, payment_status) 
SELECT 
    (SELECT user_id FROM users WHERE email = 'premium@test.com'),
    (SELECT plan_id FROM gym_plans WHERE plan_name = 'Premium Plan'),
    'Approved';

-- Add some attendance records for premium user
INSERT INTO attendance (user_id, check_in_time) 
SELECT 
    (SELECT user_id FROM users WHERE email = 'premium@test.com'),
    DATE_SUB(NOW(), INTERVAL n DAY)
FROM (
    SELECT 0 AS n UNION SELECT 1 UNION SELECT 3 UNION SELECT 5 UNION SELECT 7
) numbers;

-- Remove notifications table if it exists
DROP TABLE IF EXISTS notifications;



ALTER TABLE subscriptions 
ADD COLUMN approved_by INT NULL,
ADD FOREIGN KEY (approved_by) REFERENCES users(user_id);