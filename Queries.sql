
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(15) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

CREATE TABLE teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    semester INT NOT NULL
);

CREATE TABLE teacher_courses (
    teacher_id INT,
    course_id INT,
    PRIMARY KEY (teacher_id, course_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

CREATE TABLE student_courses (
    student_id INT,
    course_id INT,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    teacher_id INT,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE SET NULL
);


INSERT INTO teachers (name, email, password) VALUES 
('Dr. Robert Johnson', 'r.johnson@university.edu', 'Teach@123'),
('Prof. Sarah Williams', 's.williams@university.edu', 'SecurePass!'),
('Dr. Michael Brown', 'm.brown@university.edu', 'Brown2023'),
('Prof. Emily Davis', 'e.davis@university.edu', 'Davis#456'),
('Dr. David Wilson', 'd.wilson@university.edu', 'Wilson789$');

INSERT INTO courses (course_name, course_code, semester) VALUES
('Engineering Mathematics', 'MATH301', 3),
('Probability and Statistics', 'MATH302', 3),
('Object Oriented Programming', 'CS303', 3),
('Database Management Systems', 'CS401', 4),
('Data Structures and Algorithms', 'CS402', 4),
('Artificial Intelligence', 'CS501', 5),
('Operating Systems', 'CS502', 5),
('Computer Architecture', 'CS601', 6),
('Computer Networks', 'CS602', 6);

INSERT INTO students (name, email, phone_number) VALUES
('Emma Johnson', 'emma.johnson@university.edu', '5550101234'),
('Liam Smith', 'liam.smith@university.edu', '5550102345'),
('Olivia Williams', 'olivia.williams@university.edu', '5550103456'),
('Noah Brown', 'noah.brown@university.edu', '5550104567'),
('Ava Jones', 'ava.jones@university.edu', '5550105678'),
('William Garcia', 'william.garcia@university.edu', '5550106789'),
('Sophia Miller', 'sophia.miller@university.edu', '5550107890'),
('Benjamin Davis', 'benjamin.davis@university.edu', '5550108901'),
('Isabella Rodriguez', 'isabella.rodriguez@university.edu', '5550109012'),
('James Martinez', 'james.martinez@university.edu', '5550110123'),
('Mia Hernandez', 'mia.hernandez@university.edu', '5550111234'),
('Elijah Lopez', 'elijah.lopez@university.edu', '5550112345'),
('Charlotte Gonzalez', 'charlotte.gonzalez@university.edu', '5550113456'),
('Alexander Wilson', 'alexander.wilson@university.edu', '5550114567'),
('Amelia Anderson', 'amelia.anderson@university.edu', '5550115678'),
('Michael Thomas', 'michael.thomas@university.edu', '5550116789'),
('Harper Taylor', 'harper.taylor@university.edu', '5550117890'),
('Ethan Moore', 'ethan.moore@university.edu', '5550118901'),
('Evelyn Jackson', 'evelyn.jackson@university.edu', '5550119012'),
('Daniel Martin', 'daniel.martin@university.edu', '5550120123');

INSERT INTO teacher_courses (teacher_id, course_id) VALUES
(1, 3), (1, 4), (1, 5),  -- Dr. Johnson (OOP, DBMS, DSA)
(2, 1), (2, 2),           -- Prof. Williams (Math)
(3, 7), (3, 8),           -- Dr. Brown (OS, Architecture)
(4, 6),                   -- Prof. Davis (AI)
(5, 9);                   -- Dr. Wilson (Networks)

INSERT INTO student_courses (student_id, course_id) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),
(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,2),(17,2),(18,2),(19,2),(20,2),
(1,3),(2,3),(3,3),(4,3),(5,3),(6,3),(7,3),(8,3),(9,3),(10,3),(11,3),(12,3),(13,3),(14,3),(15,3),(16,3),(17,3),(18,3),(19,3),(20,3),

(1,4),(2,4),(3,4),(4,4),(5,4),(6,4),(7,4),(8,4),(9,4),(10,4),(11,4),(12,4),(13,4),(14,4),(15,4),(16,4),(17,4),(18,4),(19,4),(20,4),
(1,5),(2,5),(3,5),(4,5),(5,5),(6,5),(7,5),(8,5),(9,5),(10,5),(11,5),(12,5),(13,5),(14,5),(15,5),(16,5),(17,5),(18,5),(19,5),(20,5),

(1,6),(2,6),(3,6),(4,6),(5,6),(6,6),(7,6),(8,6),(9,6),(10,6),  -- AI
(1,7),(2,7),(3,7),(4,7),(5,7),(6,7),(7,7),(8,7),(9,7),(10,7),  -- OS

(1,8),(2,8),(3,8),(4,8),(5,8),   -- Computer Architecture
(6,9),(7,9),(8,9),(9,9),(10,9);  -- Computer Networks