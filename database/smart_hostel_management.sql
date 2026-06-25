
-- SMART HOSTEL MANAGEMENT - FULL DATABASE SCHEMA

CREATE DATABASE IF NOT EXISTS smart_hostel_management;
USE smart_hostel_management;

-- 1) Admins (admin login)
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hostel_name VARCHAR(100) NOT NULL AFTER username
);

-- ALTER TABLE admins
-- ADD hostel_name VARCHAR(100) NOT NULL AFTER username;


-- 2) Logins (student login)
CREATE TABLE IF NOT EXISTS logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(255) DEFAULT NULL,
    token_expiry DATETIME DEFAULT NULL
);

-- ALTER TABLE logins 
-- ADD reset_token VARCHAR(255) DEFAULT NULL,
-- ADD token_expiry DATETIME DEFAULT NULL;

-- 3) Students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    enrollment VARCHAR(50) NOT NULL UNIQUE,
    gender ENUM('Male','Female','Other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    blood VARCHAR(5) DEFAULT NULL,
    mobile VARCHAR(15) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    address TEXT,
    college VARCHAR(100),
    course VARCHAR(100),
    department VARCHAR(100),
    semester VARCHAR(20),
    father VARCHAR(100),
    mother VARCHAR(100),
    parent_mobile VARCHAR(15),
    occupation VARCHAR(100),
    parent_address TEXT,
    emergency_contact VARCHAR(15),
    room_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    COLUMN status VARCHAR(20) DEFAULT 'pending',
    COLUMN is_deleted TINYINT(1) DEFAULT 0;
    username VARCHAR(100) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    hostel_name VARCHAR(100) NOT NULL
);

-- ALTER TABLE students 
-- ADD COLUMN status VARCHAR(20) DEFAULT 'pending';
-- ALTER TABLE students
-- ADD status ENUM('pending','approved','rejected') DEFAULT 'pending',
-- ALTER TABLE students 
-- ADD COLUMN is_deleted TINYINT(1) DEFAULT 0;
-- ADD username VARCHAR(100) DEFAULT NULL,
-- ADD password VARCHAR(255) DEFAULT NULL;

-- ALTER TABLE students
-- ADD hostel_name VARCHAR(100) NOT NULL;

-- 4) Rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- room_no VARCHAR(20) NOT NULL UNIQUE,
    room_type VARCHAR(50) DEFAULT NULL,
    floor_no VARCHAR(20) DEFAULT NULL,
    block_name VARCHAR(50) DEFAULT NULL,
    capacity INT NOT NULL DEFAULT 0,
    occupied INT NOT NULL DEFAULT 0,
    status ENUM('Available','Full','Blocked') DEFAULT 'Available',
    UNIQUE KEY unique_room_hostel (room_no, hostel_name)
);

-- ALTER TABLE rooms
-- ADD UNIQUE KEY unique_room_hostel (room_no, hostel_name);

-- 5) Staff (including security)
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    alt_mobile VARCHAR(15),
    email VARCHAR(100),
    gender ENUM('Male','Female','Other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    address TEXT,
    role VARCHAR(50) NOT NULL,
    shift VARCHAR(50),
    salary DECIMAL(10,2) DEFAULT 0,
    joining_date DATE DEFAULT NULL,
    username VARCHAR(50) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    photo VARCHAR(255),
    status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6) Visitors + history
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    purpose VARCHAR(255),
    address VARCHAR(255),
    relation VARCHAR(50),
    checkin_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    checkout_time DATETIME DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7) Complaints (student + admin side)
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    room_no VARCHAR(20),
    priority ENUM('Low','Medium','High','Urgent') DEFAULT 'Low',
    image VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'Open',  -- Open/In-Progress/Resolved/Rejected
    assigned_staff_id INT DEFAULT NULL,
    admin_response TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL
    ALTER TABLE complaints
    ADD COLUMN cancel_reason TEXT NULL,
    ADD COLUMN reopen_reason TEXT NULL,
    ADD COLUMN resolution_note TEXT NULL,
    ADD COLUMN updated_at DATETIME NULL;
);

-- 8) Student Leave (table name: student_leave – code ke hisaab se)
CREATE TABLE student_leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    leave_type VARCHAR(100) NOT NULL,
    reason TEXT NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    going_address VARCHAR(255) DEFAULT NULL,
    contact_number VARCHAR(20) DEFAULT NULL,
    emergency_contact VARCHAR(20) DEFAULT NULL,
    document VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    admin_remark TEXT DEFAULT NULL,
    approved_by INT DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    applied_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    exit_marked TINYINT(1) DEFAULT 0,
    exit_time DATETIME DEFAULT NULL,
    entry_marked TINYINT(1) DEFAULT 0,
    entry_time DATETIME DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (approved_by) REFERENCES admins(id)
);

-- 9) Fee categories
CREATE TABLE IF NOT EXISTS fee_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    default_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    frequency ENUM('One-Time','Monthly','Quarterly','Yearly','Other')
        DEFAULT 'One-Time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10) Student fees
CREATE TABLE IF NOT EXISTS student_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    due_date DATE NOT NULL,
    status ENUM('Unpaid','Partially Paid','Paid') DEFAULT 'Unpaid',
    payment_date DATE DEFAULT NULL,
    payment_mode VARCHAR(30) DEFAULT NULL,
    transaction_no VARCHAR(100) DEFAULT NULL,
    remarks VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE staff_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'Present',
    check_in TIME NULL,
    check_out TIME NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_attendance (staff_id, attendance_date),

    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

CREATE TABLE staff_leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    leave_type VARCHAR(50),
    start_date DATE,
    end_date DATE,
    reason TEXT,
    status VARCHAR(20) DEFAULT 'Pending',
    decided_at DATETIME NULL,
    decided_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

-- 11) Attendance (for dashboard stats)
CREATE TABLE student_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL DEFAULT 'Absent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_date (student_id, attendance_date),
    CONSTRAINT fk_student_attendance_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 12) Notices (optional notice board)
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    audience VARCHAR(50) DEFAULT 'All',
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    COLUMN is_pinned TINYINT(1) NOT NULL DEFAULT 0 AFTER audience
);

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    incident_type ENUM('Fight','Noise','Damage','Theft','Suspicious') NOT NULL,
    location VARCHAR(255) NOT NULL,
    severity ENUM('Low','Medium','High') DEFAULT 'Low',

    reported_by INT NOT NULL, -- security guard id
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    status ENUM('Pending','In Review','Resolved') DEFAULT 'Pending',

    admin_remark TEXT,
    handled_by INT DEFAULT NULL,
    handled_at DATETIME DEFAULT NULL,

    FOREIGN KEY (reported_by) REFERENCES staff(id),
    FOREIGN KEY (handled_by) REFERENCES admins(id),
    COLUMN updated_at DATETIME DEFAULT NULL
);

-- ALTER TABLE incidents 
-- ADD COLUMN updated_at DATETIME DEFAULT NULL;

-- ALTER TABLE students ADD admin_id INT NOT NULL;
-- ALTER TABLE rooms ADD admin_id INT NOT NULL;
-- ALTER TABLE complaints ADD admin_id INT NOT NULL;
-- ALTER TABLE visitors ADD admin_id INT NOT NULL;
-- ALTER TABLE notices ADD admin_id INT NOT NULL;
-- ALTER TABLE staff ADD admin_id INT NOT NULL;
-- ALTER TABLE incidents ADD admin_id INT NOT NULL;
-- ALTER TABLE student_leaves ADD admin_id INT NOT NULL;
-- ALTER TABLE student_fees ADD admin_id INT NOT NULL;

-- CREATE TABLE hostels (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     hostel_name VARCHAR(100) NOT NULL
-- );

ALTER TABLE students
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER  TABLE logins 
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE rooms
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE complaints
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE visitors
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE notices
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE staff
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER  TABLE student_leaves  
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER TABLE incidents
ADD hostel_name VARCHAR(100) NOT NULL;

ALTER  TABLE student_attendance
ADD hostel_name VARCHAR(100) NOT NULL;


-- Useful indexes
ALTER TABLE students      ADD INDEX idx_students_room_id (room_id);
ALTER TABLE complaints    ADD INDEX idx_complaints_student (student_id);
ALTER TABLE complaints    ADD INDEX idx_complaints_status  (status);
ALTER TABLE student_leave ADD INDEX idx_leave_student      (student_id);
ALTER TABLE visitors      ADD INDEX idx_visitors_student   (student_id);
ALTER TABLE student_fees  ADD INDEX idx_fees_student       (student_id);
ALTER TABLE student_fees  ADD INDEX idx_fees_category      (category_id);
ALTER TABLE attendance    ADD INDEX idx_attendance_date    (attendance_date);
