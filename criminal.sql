-- Create the database
CREATE DATABASE IF NOT EXISTS criminal_db;
USE criminal_db;

-- Create criminals table
CREATE TABLE criminals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    gender ENUM('Male', 'Female', 'Other'),
    date_of_birth DATE,
    address TEXT,
    arrest_date DATE
);

-- Insert sample criminals
INSERT INTO criminals (first_name, last_name, gender, date_of_birth, address, arrest_date) VALUES
('Ravi', 'Sharma', 'Male', '1990-05-12', 'Mumbai, India', '2023-04-10'),
('Anjali', 'Mehta', 'Female', '1985-11-23', 'Delhi, India', '2024-01-15'),
('Salman', 'Khan', 'Male', '1982-07-03', 'Lucknow, India', '2024-03-20');

-- Create crimes table
CREATE TABLE crimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crime_type VARCHAR(100),
    description TEXT,
    crime_date DATE,
    location VARCHAR(100)
);

-- Insert sample crimes
INSERT INTO crimes (crime_type, description, crime_date, location) VALUES
('Theft', 'Stolen vehicle reported near highway.', '2023-03-18', 'Bangalore'),
('Cyber Fraud', 'Online banking fraud case.', '2024-01-12', 'Chennai'),
('Assault', 'Physical assault in a local market.', '2024-03-01', 'Pune');

-- Create cases table to link criminals and crimes
CREATE TABLE cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    criminal_id INT,
    crime_id INT,
    officer_in_charge VARCHAR(100),
    status ENUM('Open', 'Closed', 'Under Investigation') DEFAULT 'Open',
    FOREIGN KEY (criminal_id) REFERENCES criminals(id) ON DELETE CASCADE,
    FOREIGN KEY (crime_id) REFERENCES crimes(id) ON DELETE CASCADE
);

-- Insert sample cases
INSERT INTO cases (criminal_id, crime_id, officer_in_charge, status) VALUES
(1, 1, 'Officer Ramesh', 'Closed'),
(2, 2, 'Officer Priya', 'Under Investigation'),
(3, 3, 'Officer Arjun', 'Open');

-- Create users table for admin login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, password) VALUES
('admin', MD5('admin123'));
