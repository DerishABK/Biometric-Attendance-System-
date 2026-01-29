-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS prisoner_attendance_db;
USE prisoner_attendance_db;

-- Users table (Admins, Wardens, Guards)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'warden', 'guard') NOT NULL,
    full_name VARCHAR(100),
    designation VARCHAR(100),
    status ENUM('On Duty', 'Off Duty', 'On Leave') DEFAULT 'Off Duty',
    assigned_wing VARCHAR(50),
    shift_type VARCHAR(50),
    joining_date DATE,
    contact_ext VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prisoners table
CREATE TABLE IF NOT EXISTS prisoners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prisoner_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    nationality VARCHAR(50),
    contact_number VARCHAR(20),
    address TEXT,
    block_wing VARCHAR(50),
    cell_number VARCHAR(20),
    crime VARCHAR(100),
    sentence_duration VARCHAR(50),
    admission_date DATE,
    expected_release DATE,
    photo_path VARCHAR(255),
    fingerprint_data TEXT, -- Placeholder for fingerprint blob or template
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prisoner_id VARCHAR(20) NOT NULL,
    attendance_date DATE NOT NULL,
    time_in TIME,
    status ENUM('Present', 'Absent', 'Excused') DEFAULT 'Present',
    FOREIGN KEY (prisoner_id) REFERENCES prisoners(prisoner_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert demo users and specific staff members (21 total)
-- Superintendent (1)
INSERT IGNORE INTO users (user_id, password, role, full_name, designation, status, assigned_wing, shift_type, joining_date, contact_ext) VALUES 
('admin', 'admin123', 'admin', 'Amit Sharma', 'Superintendent of Police', 'On Duty', 'Admin Wing', 'General (09:00-17:00)', '2015-06-12', '101');

-- Wardens (5) - Designation: Sub Inspector
INSERT IGNORE INTO users (user_id, password, role, full_name, designation, status, assigned_wing, shift_type, joining_date, contact_ext) VALUES 
('warden1', 'warden123', 'warden', 'Rajesh Kumar', 'Sub Inspector', 'On Duty', 'Block A Control', 'Day Shift', '2018-03-20', '201'),
('warden2', 'warden123', 'warden', 'Suraj Singh', 'Sub Inspector', 'Off Duty', 'Block B Control', 'Night Shift', '2019-11-05', '202'),
('warden3', 'warden123', 'warden', 'Vijay Pratap', 'Sub Inspector', 'On Duty', 'High Security Wing', 'Day Shift', '2017-08-14', '203'),
('warden4', 'warden123', 'warden', 'Anil Verma', 'Sub Inspector', 'On Leave', 'Medical Wing', 'General', '2020-01-22', '204'),
('warden5', 'warden123', 'warden', 'Sanjay Dutt', 'Sub Inspector', 'On Duty', 'Visitation Lobby', 'Day Shift', '2016-12-01', '205');

-- Guards (15) 
-- Assistant Sub Inspectors (5)
INSERT IGNORE INTO users (user_id, password, role, full_name, designation, status, assigned_wing, shift_type, joining_date, contact_ext) VALUES 
('guard1', 'guard123', 'guard', 'Ramesh Yadav', 'Assistant Sub Inspector', 'On Duty', 'Main Gate', 'Day Shift', '2021-05-15', '301'),
('guard2', 'guard123', 'guard', 'Manoj Tiwari', 'Assistant Sub Inspector', 'Off Duty', 'Tower 1', 'Night Shift', '2021-07-10', '302'),
('guard3', 'guard123', 'guard', 'Pankaj Mishra', 'Assistant Sub Inspector', 'On Duty', 'Block A East', 'Day Shift', '2022-02-28', '303'),
('guard4', 'guard123', 'guard', 'Deepak Chahar', 'Assistant Sub Inspector', 'On Leave', 'Block B West', 'Day Shift', '2021-12-12', '304'),
('guard5', 'guard123', 'guard', 'Rahul Dravid', 'Assistant Sub Inspector', 'On Duty', 'Control Room', 'Day Shift', '2020-09-30', '305');

-- Constables (10)
INSERT IGNORE INTO users (user_id, password, role, full_name, designation, status, assigned_wing, shift_type, joining_date, contact_ext) VALUES 
('guard6', 'guard123', 'guard', 'Karan Johar', 'Constable', 'On Duty', 'Block A Corridor', 'Night Shift', '2023-01-10', '406'),
('guard7', 'guard123', 'guard', 'Varun Dhawan', 'Constable', 'On Duty', 'Block B Corridor', 'Day Shift', '2023-03-15', '407'),
('guard8', 'guard123', 'guard', 'Sid Malhotra', 'Constable', 'Off Duty', 'Yard Security', 'Night Shift', '2023-05-20', '408'),
('guard9', 'guard123', 'guard', 'Ranbir Kapoor', 'Constable', 'On Duty', 'Mess Hall', 'Day Shift', '2022-11-01', '409'),
('guard10', 'guard123', 'guard', 'Kartik Aryan', 'Constable', 'On Duty', 'Admin Entry', 'Day Shift', '2023-08-14', '410'),
('guard11', 'guard123', 'guard', 'Ayushmann K', 'Constable', 'On Duty', 'Block C Entrance', 'Day Shift', '2023-09-01', '411'),
('guard12', 'guard123', 'guard', 'Vicky Kaushal', 'Constable', 'Off Duty', 'Perimeter Wall', 'Night Shift', '2022-06-18', '412'),
('guard13', 'guard123', 'guard', 'Ishaan Khatter', 'Constable', 'On Duty', 'Workshop Area', 'Day Shift', '2024-01-05', '413'),
('guard14', 'guard123', 'guard', 'Rajkummar Rao', 'Constable', 'On Duty', 'Gym Area', 'Day Shift', '2023-02-14', '414'),
('guard15', 'guard123', 'guard', 'Tahir Bhasin', 'Constable', 'Off Duty', 'Store Room', 'Night Shift', '2023-04-25', '415');
 
 -- Leave Applications table
 CREATE TABLE IF NOT EXISTS leaves (
     id INT AUTO_INCREMENT PRIMARY KEY,
     user_id VARCHAR(50) NOT NULL,
     leave_date DATE NOT NULL,
     shift ENUM('Day Shift', 'Night Shift', 'General') NOT NULL,
     duration ENUM('Half Day', 'Full Day') NOT NULL,
     reason TEXT NOT NULL,
     alt_staff_id VARCHAR(50) NOT NULL,
     status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
     FOREIGN KEY (alt_staff_id) REFERENCES users(user_id) ON DELETE CASCADE
 );
