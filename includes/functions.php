<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../database/database.php';

// Initialize connection
$conn = getDBConnection();

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function loginUser($email, $password) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, name, email, password, is_approved, created_at FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }

        if ($user['is_approved'] == 0) {
            return [
                'success' => false,
                'message' => 'Your account is pending admin approval. Please wait for approval before logging in.'
            ];
        }

        return [
            'success' => true,
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'created_at' => $user['created_at']
        ];
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred during login. Please try again.'
        ];
    }
}

function registerUser($name, $email, $password, $age, $gender) {
    global $conn;
    
    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Validate age
        $age = intval($age);
        if ($age < 0) {
            return ['success' => false, 'message' => 'Age must be a positive number'];
        }

        // Validate gender
        $validGenders = ['Male', 'Female', 'Other'];
        if (!in_array($gender, $validGenders)) {
            return ['success' => false, 'message' => 'Invalid gender selection'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user with is_approved set to FALSE (0)
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, age, gender, is_approved) VALUES (?, ?, ?, ?, ?, FALSE)");
        $stmt->execute([$name, $email, $hashedPassword, $age, $gender]);

        // Send notification to admin
        sendAdminNotification($email, $name);

        return ['success' => true, 'message' => 'Registration successful. Please wait for admin approval.'];
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

function sendAdminNotification($email, $name) {
    $adminEmail = "admin@example.com"; // Replace with your admin email
    $subject = "New User Registration - Approval Required";
    
    $message = "
    <html>
    <head>
        <title>New User Registration</title>
    </head>
    <body>
        <h2>New User Registration</h2>
        <p>A new user has registered and requires approval:</p>
        <ul>
            <li>Name: $name</li>
            <li>Email: $email</li>
        </ul>
        <p>Please review and approve/reject this user at your earliest convenience.</p>
        <p><a href='http://yourdomain.com/admin/approve_users.php'>Click here to manage user approvals</a></p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@yourdomain.com" . "\r\n";
    
    mail($adminEmail, $subject, $message, $headers);
}

function getLawyers() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT * FROM lawyers
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get lawyers error: " . $e->getMessage());
        throw new Exception("Database error while fetching lawyers");
    }
}

function getDocumentTemplates() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM document_templates");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get templates error: " . $e->getMessage());
        throw new Exception("Database error while fetching templates");
    }
}

function createAppointment($clientId, $lawyerId, $date, $duration, $notes = '') {
    try {
        $conn = getDBConnection();
        
        // Validate duration
        $duration = intval($duration);
        if ($duration <= 0) {
            throw new Exception("Invalid duration specified");
        }

        // Check for overlapping appointments
        $stmt = $conn->prepare("
            SELECT id FROM appointments 
            WHERE lawyer_id = ? 
            AND appointment_date = ? 
            AND status != 'cancelled'
        ");
        $stmt->execute([$lawyerId, $date]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("This time slot is already booked");
        }

        $stmt = $conn->prepare("
            INSERT INTO appointments (client_id, lawyer_id, appointment_date, duration, notes, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$clientId, $lawyerId, $date, $duration, $notes]);
    } catch (PDOException $e) {
        error_log("Create appointment error: " . $e->getMessage());
        throw new Exception("Database error while creating appointment");
    }
}

function getUserDocuments($userId) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT d.*, u.name as user_name 
            FROM documents d
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.user_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get user documents error: " . $e->getMessage());
        return [];
    }
}

function createDocument($userId, $title, $documentType, $filePath) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            INSERT INTO documents (user_id, title, document_type, file_path, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $title, $documentType, $filePath]);
    } catch (PDOException $e) {
        error_log("Create document error: " . $e->getMessage());
        return false;
    }
}

function getAllDocuments() {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT d.*, u.name as user_name 
            FROM documents d
            LEFT JOIN users u ON d.user_id = u.id
            ORDER BY d.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get all documents error: " . $e->getMessage());
        return [];
    }
}

function deleteDocument($documentId, $userId) {
    try {
        $conn = getDBConnection();
        
        // First get the file path
        $stmt = $conn->prepare("
            SELECT file_path FROM documents 
            WHERE id = ? AND (user_id = ? OR ? IN (SELECT id FROM admins))
        ");
        $stmt->execute([$documentId, $userId, $userId]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($document) {
            // Delete the file
            if (file_exists($document['file_path'])) {
                unlink($document['file_path']);
            }
            
            // Delete the database record
            $stmt = $conn->prepare("
                DELETE FROM documents 
                WHERE id = ? AND (user_id = ? OR ? IN (SELECT id FROM admins))
            ");
            return $stmt->execute([$documentId, $userId, $userId]);
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Delete document error: " . $e->getMessage());
        return false;
    }
}

function getDocument($documentId, $userId) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT d.*, u.name as user_name 
            FROM documents d
            LEFT JOIN users u ON d.user_id = u.id
            WHERE d.id = ? AND (d.user_id = ? OR ? IN (SELECT id FROM admins))
        ");
        $stmt->execute([$documentId, $userId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get document error: " . $e->getMessage());
        return null;
    }
}

function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function isAdmin() {
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare("SELECT id FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Admin check error: " . $e->getMessage());
        return false;
    }
}

function requireAdmin() {
    // No longer needed as admin section is public
}

function isLawyer() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'lawyer';
}

function isClient() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'client';
}

function getPendingUsers() {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, name, email, age, gender, created_at, is_approved FROM users WHERE is_approved = 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching pending users: " . $e->getMessage());
        return [];
    }
}

function approveUser($userId) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Error approving user: " . $e->getMessage());
        return false;
    }
}

function rejectUser($userId) {
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_approved = 0");
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Error rejecting user: " . $e->getMessage());
        return false;
    }
}

function sendApprovalEmail($userId) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Use Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'your-email@gmail.com';  // Your Gmail address
                $mail->Password = 'your-app-password';     // Your Gmail app password
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('noreply@legalease.com', 'LegalEase');
                $mail->addAddress($user['email'], $user['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Your Account Has Been Approved";
                $mail->Body = "
                <html>
                <head>
                    <title>Account Approved</title>
                </head>
                <body>
                    <h2>Account Approved</h2>
                    <p>Dear {$user['name']},</p>
                    <p>Your account has been approved by the administrator. You can now log in to your account.</p>
                    <p><a href='http://yourdomain.com/login.php'>Click here to login</a></p>
                </body>
                </html>
                ";

                $mail->send();
                error_log("Approval email sent to: " . $user['email']);
            } catch (Exception $e) {
                error_log("Error sending approval email: " . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        error_log("Error in sendApprovalEmail: " . $e->getMessage());
    }
}

function sendRejectionEmail($userId) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT email, name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  // Use Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'your-email@gmail.com';  // Your Gmail address
                $mail->Password = 'your-app-password';     // Your Gmail app password
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('noreply@legalease.com', 'LegalEase');
                $mail->addAddress($user['email'], $user['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Your Account Registration Has Been Rejected";
                $mail->Body = "
                <html>
                <head>
                    <title>Account Rejected</title>
                </head>
                <body>
                    <h2>Account Rejected</h2>
                    <p>Dear {$user['name']},</p>
                    <p>We regret to inform you that your account registration has been rejected by the administrator.</p>
                    <p>If you believe this is a mistake, please contact our support team.</p>
                </body>
                </html>
                ";

                $mail->send();
                error_log("Rejection email sent to: " . $user['email']);
            } catch (Exception $e) {
                error_log("Error sending rejection email: " . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        error_log("Error in sendRejectionEmail: " . $e->getMessage());
    }
}
?> 