-- SQL Script for Gym Management System

-- Create the database
CREATE DATABASE gym_management;
USE gym_management;

-- Table for Users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('admin', 'user') DEFAULT 'user'
);

-- Table for Gym Plans
CREATE TABLE gym_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    plan_description TEXT,
    plan_price DECIMAL(10, 2) NOT NULL,
    plan_duration INT NOT NULL -- Duration in months
);

-- Table for Subscriptions
CREATE TABLE subscriptions (
    subscription_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('Pending', 'Paid', 'Approved') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES gym_plans(plan_id) ON DELETE CASCADE
);

-- Table for Attendance Records
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_in_time TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Add payment tracking
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    khalti_token VARCHAR(255),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id)
);

-- Insert Sample Data (Optional)
INSERT INTO gym_plans (plan_name, plan_description, plan_price, plan_duration) 
VALUES 
('Basic Plan', 'Access to gym equipment only', 50.00, 1),
('Standard Plan', 'Includes equipment and group classes', 100.00, 3),
('Premium Plan', 'All-inclusive access with personal training', 200.00, 6);

-- Insert admin user
INSERT INTO users (full_name, email, password, role) 
VALUES ('Admin', 'admin@gmail.com', 'Admin@1234', 'admin');
