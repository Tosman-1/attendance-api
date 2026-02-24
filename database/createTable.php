<?php
if (!isset($pdo)) {
    die;
}

try {
    $tables = [

        "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            firstname VARCHAR(50) NOT NULL,
            lastname VARCHAR(50) NOT NULL,
            user_id VARCHAR(15) UNIQUE NOT NULL,
            department VARCHAR(50) NOT NULL,
            role TEXT CHECK (role IN ('student', 'instructor', 'admin')) DEFAULT 'student',
            email VARCHAR(200) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS attendance (
            id SERIAL PRIMARY KEY,
            uid INT NOT NULL,
            user_id VARCHAR(15) NOT NULL,
            date DATE NOT NULL,
            time TIME NOT NULL,
            status TEXT CHECK (status IN ('present', 'absent', 'late')) DEFAULT 'present',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )",

        "CREATE TABLE IF NOT EXISTS activity_logs (
            id SERIAL PRIMARY KEY,
            uid INT,
            user_id VARCHAR(15) NOT NULL,
            browser VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45), 
            action VARCHAR(50) NOT NULL,
            url VARCHAR(255),
            method VARCHAR(10), 
            referrer VARCHAR(255), 
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )",

    ];

    foreach ($tables as $tableSQL) {
        $pdo->exec($tableSQL);
    }

    // echo "All tables created successfully!";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
