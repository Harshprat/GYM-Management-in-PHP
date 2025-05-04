-- Create new database
CREATE DATABASE IF NOT EXISTS fitness_center;
USE fitness_center;

-- Drop existing tables if they exist (to ensure clean state)
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS trainers;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS membership_plans;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create membership_plans table
CREATE TABLE membership_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in months',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create members table
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    plan_id INT,
    join_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id)
);

-- Create trainers table
CREATE TABLE trainers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    specialization VARCHAR(100),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create equipment table
CREATE TABLE equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    quantity INT NOT NULL,
    status ENUM('Available', 'Maintenance', 'Out of Order') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert sample membership plans
INSERT INTO membership_plans (name, description, price, duration) VALUES
('Basic Plan', 'Access to gym floor and basic equipment', 29.99, 1),
('Premium Plan', 'Full access to all facilities including pool and sauna', 49.99, 1),
('Annual Plan', 'Full access with 2 months free', 499.99, 12);

-- Insert sample trainers
INSERT INTO trainers (name, email, phone, specialization) VALUES
('John Doe', 'john@example.com', '1234567890', 'Weight Training'),
('Jane Smith', 'jane@example.com', '0987654321', 'Yoga'),
('Mike Johnson', 'mike@example.com', '5555555555', 'CrossFit');

-- Insert sample equipment
INSERT INTO equipment (name, description, quantity) VALUES
('Treadmill', 'Cardio equipment', 5),
('Dumbbells Set', 'Weight training equipment', 10),
('Exercise Bike', 'Cardio equipment', 3),
('Bench Press', 'Weight training equipment', 2),
('Yoga Mats', 'Yoga equipment', 20); 