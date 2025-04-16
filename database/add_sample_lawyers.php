<?php
require_once __DIR__ . '/database.php';

try {
    $pdo = getDBConnection();
    echo "Connected to database successfully.\n";

    // First, drop the existing lawyers table and recreate it with the new structure
    echo "Dropping existing lawyers table...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS lawyers");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "Creating new lawyers table...\n";
    $pdo->exec("
        CREATE TABLE lawyers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            gender ENUM('Male', 'Female', 'Other') NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            specialization VARCHAR(100) NOT NULL,
            experience_years INT NOT NULL,
            hourly_rate DECIMAL(10,2) NOT NULL,
            bio TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    // Sample lawyers data
    $lawyers = [
        [
            'name' => 'John Smith',
            'gender' => 'Male',
            'email' => 'john.smith@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'specialization' => 'Criminal Law',
            'experience_years' => 15,
            'hourly_rate' => 150,
            'bio' => 'Experienced criminal defense attorney with a strong track record of successful cases and expertise in handling complex criminal matters.'
        ],
        [
            'name' => 'Sarah Johnson',
            'gender' => 'Female',
            'email' => 'sarah.johnson@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'specialization' => 'Family Law',
            'experience_years' => 10,
            'hourly_rate' => 120,
            'bio' => 'Dedicated family law specialist helping clients with divorce, custody, and adoption cases. Committed to achieving the best outcomes for families.'
        ],
        [
            'name' => 'Michael Chen',
            'gender' => 'Male',
            'email' => 'michael.chen@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'specialization' => 'Corporate Law',
            'experience_years' => 25,
            'hourly_rate' => 200,
            'bio' => 'Corporate law expert specializing in mergers, acquisitions, and business contracts with extensive experience in international business law.'
        ],
        [
            'name' => 'Emily Rodriguez',
            'gender' => 'Female',
            'email' => 'emily.rodriguez@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'specialization' => 'Immigration Law',
            'experience_years' => 8,
            'hourly_rate' => 140,
            'bio' => 'Passionate immigration lawyer dedicated to helping individuals and families navigate the complex immigration system.'
        ],
        [
            'name' => 'David Kim',
            'gender' => 'Male',
            'email' => 'david.kim@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'specialization' => 'Real Estate Law',
            'experience_years' => 12,
            'hourly_rate' => 160,
            'bio' => 'Experienced real estate attorney specializing in commercial and residential property transactions, leasing, and property disputes.'
        ]
    ];

    echo "Adding sample lawyers...\n";
    $pdo->beginTransaction();

    foreach ($lawyers as $lawyer) {
        $stmt = $pdo->prepare("
            INSERT INTO lawyers (
                name, gender, email, password, specialization, 
                experience_years, hourly_rate, bio, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([
            $lawyer['name'],
            $lawyer['gender'],
            $lawyer['email'],
            $lawyer['password'],
            $lawyer['specialization'],
            $lawyer['experience_years'],
            $lawyer['hourly_rate'],
            $lawyer['bio']
        ]);
        echo "Added lawyer: " . $lawyer['name'] . "\n";
    }

    $pdo->commit();
    echo "All sample lawyers added successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "Transaction rolled back.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        echo "Transaction rolled back.\n";
    }
}

echo "Script completed.\n";
?> 