<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'legalease');

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Check and update schema if needed
        updateSchema($conn);
        
        return $conn;
    } catch(PDOException $e) {
        // Log the error
        error_log("Database connection failed: " . $e->getMessage());
        
        // Show a user-friendly message
        die("Sorry, we're experiencing technical difficulties. Please try again later.");
    }
}

function updateSchema($conn) {
    try {
        // Check if age column exists in users table
        $checkAge = $conn->query("SHOW COLUMNS FROM users LIKE 'age'");
        if ($checkAge->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN age INT DEFAULT NULL");
            error_log("Added age column to users table");
        }
        
        // Check if gender column exists in users table
        $checkGender = $conn->query("SHOW COLUMNS FROM users LIKE 'gender'");
        if ($checkGender->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN gender VARCHAR(10) DEFAULT NULL");
            error_log("Added gender column to users table");
        }

        // Check if is_approved column exists in users table
        $checkApproved = $conn->query("SHOW COLUMNS FROM users LIKE 'is_approved'");
        if ($checkApproved->rowCount() == 0) {
            $conn->exec("ALTER TABLE users ADD COLUMN is_approved BOOLEAN DEFAULT FALSE");
            error_log("Added is_approved column to users table");
        }

        // Check if gender column exists in lawyers table
        $checkLawyerGender = $conn->query("SHOW COLUMNS FROM lawyers LIKE 'gender'");
        if ($checkLawyerGender->rowCount() == 0) {
            $conn->exec("ALTER TABLE lawyers ADD COLUMN gender ENUM('Male', 'Female', 'Other') NOT NULL AFTER name");
            error_log("Added gender column to lawyers table");
        }
    } catch (PDOException $e) {
        error_log("Schema update error: " . $e->getMessage());
    }
}

// Test database connection
try {
    $testConn = getDBConnection();
    if ($testConn) {
        error_log("Database connection successful");
    }
} catch (Exception $e) {
    error_log("Database connection test failed: " . $e->getMessage());
}

// Function to get appointment user
function getAppointmentUser() {
    $pdo = getDBConnection();
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting appointment user: " . $e->getMessage());
        return null;
    }
}

// Function to get available lawyers for appointments
function getAvailableLawyers() {
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->query("
            SELECT * FROM lawyers 
            WHERE status = 'active'
            ORDER BY name
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting available lawyers: " . $e->getMessage());
        return [];
    }
}

// Function to create new appointment
function createNewAppointment($clientId, $lawyerId, $appointmentDate, $duration, $notes) {
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO appointments (
                client_id, 
                lawyer_id, 
                appointment_date, 
                duration, 
                notes, 
                status
            ) VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$clientId, $lawyerId, $appointmentDate, $duration, $notes]);
    } catch (PDOException $e) {
        error_log("Error creating new appointment: " . $e->getMessage());
        return false;
    }
}

function getClientAppointments($clientId) {
    try {
        $pdo = getDBConnection();
        error_log("Getting appointments for client ID: " . $clientId);
        
        // First check if the client exists
        $checkStmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ?");
        $checkStmt->execute([$clientId]);
        $client = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$client) {
            error_log("Client ID " . $clientId . " not found in users table");
            return [];
        }
        
        error_log("Found client: " . $client['name'] . " (ID: " . $clientId . ")");

        // Get all appointments with lawyer details, excluding cancelled ones
        $stmt = $pdo->prepare("
            SELECT 
                a.id,
                a.client_id,
                a.lawyer_id,
                a.appointment_date,
                a.duration,
                a.status,
                a.notes,
                a.created_at,
                l.name as lawyer_name,
                l.email as lawyer_email,
                l.specialization,
                l.experience_years,
                l.hourly_rate
            FROM appointments a
            LEFT JOIN lawyers l ON a.lawyer_id = l.id
            WHERE a.client_id = ? AND a.status != 'cancelled'
            ORDER BY a.appointment_date DESC
        ");
        
        $stmt->execute([$clientId]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($appointments)) {
            error_log("No active appointments found for client ID: " . $clientId);
        } else {
            error_log("Found " . count($appointments) . " active appointments for client ID: " . $clientId);
            error_log("First appointment details: " . print_r($appointments[0], true));
        }
        
        return $appointments;
    } catch (PDOException $e) {
        error_log("Error fetching client appointments: " . $e->getMessage());
        error_log("SQL State: " . $e->errorInfo[0]);
        error_log("Error Code: " . $e->errorInfo[1]);
        error_log("Error Message: " . $e->errorInfo[2]);
        return [];
    }
}

function cancelAppointment($appointmentId, $clientId) {
    try {
        $pdo = getDBConnection();
        
        // Verify the appointment belongs to the client and is not already cancelled
        $stmt = $pdo->prepare("
            UPDATE appointments 
            SET status = 'cancelled' 
            WHERE id = ? AND client_id = ? AND status != 'cancelled'
        ");
        
        return $stmt->execute([$appointmentId, $clientId]);
    } catch (PDOException $e) {
        error_log("Error cancelling appointment: " . $e->getMessage());
        return false;
    }
}

// Function to get lawyer details by ID
function getLawyerById($lawyerId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM lawyers 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->execute([$lawyerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting lawyer details: " . $e->getMessage());
        return null;
    }
}

// Function to get all lawyers for admin
function getAllLawyers() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT * FROM lawyers 
            ORDER BY name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting all lawyers: " . $e->getMessage());
        return [];
    }
}

// Function to get lawyer appointments
function getLawyerAppointments($lawyerId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                a.id,
                a.client_id,
                a.appointment_date,
                a.duration,
                a.status,
                a.notes,
                a.created_at,
                u.name as client_name,
                u.email as client_email
            FROM appointments a
            LEFT JOIN users u ON a.client_id = u.id
            WHERE a.lawyer_id = ?
            ORDER BY a.appointment_date DESC
        ");
        $stmt->execute([$lawyerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting lawyer appointments: " . $e->getMessage());
        return [];
    }
}

// Function to update appointment status
function updateAppointmentStatus($appointmentId, $lawyerId, $status) {
    try {
        $pdo = getDBConnection();
        
        // Verify the appointment exists and belongs to the lawyer (if lawyerId is provided)
        $sql = "SELECT id FROM appointments WHERE id = ?";
        $params = [$appointmentId];
        
        if ($lawyerId !== null) {
            $sql .= " AND lawyer_id = ?";
            $params[] = $lawyerId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() === 0) {
            error_log("Appointment not found or doesn't belong to the specified lawyer");
            return false;
        }
        
        // Update the appointment status
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $appointmentId]);
        
        if (!$result) {
            error_log("Failed to update appointment status");
            return false;
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating appointment status: " . $e->getMessage());
        return false;
    }
}

function createDocumentsTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        document_type VARCHAR(100) NOT NULL,
        file_path VARCHAR(255),
        content TEXT,
        status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Error creating documents table: " . $e->getMessage());
        return false;
    }
}
?> 