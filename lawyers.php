<?php
require_once __DIR__ . '/database/database.php';

class LawyerManager {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    // Get all available lawyers with their details
    public function getAvailableLawyers() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    l.*,
                    u.name,
                    u.email,
                    s.name as specialization_name,
                    COUNT(a.id) as total_appointments,
                    AVG(r.rating) as average_rating
                FROM lawyers l
                JOIN users u ON l.user_id = u.id
                JOIN specializations s ON l.specialization_id = s.id
                LEFT JOIN appointments a ON l.id = a.lawyer_id
                LEFT JOIN reviews r ON l.id = r.lawyer_id
                WHERE l.is_available = 1
                GROUP BY l.id
                ORDER BY average_rating DESC, total_appointments DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting available lawyers: " . $e->getMessage());
            return [];
        }
    }

    // Get lawyer details by ID
    public function getLawyerById($lawyerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    l.*,
                    u.name,
                    u.email,
                    s.name as specialization_name,
                    COUNT(a.id) as total_appointments,
                    AVG(r.rating) as average_rating
                FROM lawyers l
                JOIN users u ON l.user_id = u.id
                JOIN specializations s ON l.specialization_id = s.id
                LEFT JOIN appointments a ON l.id = a.lawyer_id
                LEFT JOIN reviews r ON l.id = r.lawyer_id
                WHERE l.id = ?
                GROUP BY l.id
            ");
            $stmt->execute([$lawyerId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting lawyer details: " . $e->getMessage());
            return null;
        }
    }

    // Get lawyers by specialization
    public function getLawyersBySpecialization($specializationId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    l.*,
                    u.name,
                    u.email,
                    s.name as specialization_name,
                    COUNT(a.id) as total_appointments,
                    AVG(r.rating) as average_rating
                FROM lawyers l
                JOIN users u ON l.user_id = u.id
                JOIN specializations s ON l.specialization_id = s.id
                LEFT JOIN appointments a ON l.id = a.lawyer_id
                LEFT JOIN reviews r ON l.id = r.lawyer_id
                WHERE l.specialization_id = ? AND l.is_available = 1
                GROUP BY l.id
                ORDER BY average_rating DESC, total_appointments DESC
            ");
            $stmt->execute([$specializationId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting lawyers by specialization: " . $e->getMessage());
            return [];
        }
    }

    // Get all specializations
    public function getSpecializations() {
        try {
            $stmt = $this->pdo->query("
                SELECT s.*, COUNT(l.id) as lawyer_count
                FROM specializations s
                LEFT JOIN lawyers l ON s.id = l.specialization_id
                GROUP BY s.id
                ORDER BY s.name
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting specializations: " . $e->getMessage());
            return [];
        }
    }

    // Check lawyer availability for a specific date and time
    public function checkLawyerAvailability($lawyerId, $datetime) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM appointments
                WHERE lawyer_id = ? 
                AND appointment_datetime = ?
                AND status != 'cancelled'
            ");
            $stmt->execute([$lawyerId, $datetime]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] == 0;
        } catch (PDOException $e) {
            error_log("Error checking lawyer availability: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize lawyer manager
$lawyerManager = new LawyerManager();

// Function to get lawyer options for dropdown
function getLawyerOptions() {
    global $lawyerManager;
    $lawyers = $lawyerManager->getAvailableLawyers();
    
    $options = '<option value="" disabled selected>Select a lawyer from the list</option>';
    foreach ($lawyers as $lawyer) {
        $rating = number_format($lawyer['average_rating'], 1);
        $specialization = htmlspecialchars($lawyer['specialization_name']);
        $experience = $lawyer['experience'] . ' years';
        
        $options .= sprintf(
            '<option value="%d" data-specialization="%s" data-experience="%s" data-rating="%s">%s (%s) - %s ★</option>',
            $lawyer['id'],
            $specialization,
            $experience,
            $rating,
            htmlspecialchars($lawyer['name']),
            $specialization,
            $rating
        );
    }
    
    return $options;
}

// Function to get lawyer info card HTML
function getLawyerInfoCard($lawyerId) {
    global $lawyerManager;
    $lawyer = $lawyerManager->getLawyerById($lawyerId);
    
    if (!$lawyer) {
        return '';
    }
    
    $rating = number_format($lawyer['average_rating'], 1);
    $specialization = htmlspecialchars($lawyer['specialization_name']);
    $experience = $lawyer['experience'] . ' years';
    
    return sprintf(
        '<div class="lawyer-info-card bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mt-4">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-user-tie text-primary text-xl"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">%s</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">%s</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                    <i class="fas fa-star text-yellow-400"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-300">%s / 5.0</span>
                </div>
                <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                    <i class="fas fa-briefcase text-primary"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-300">%s experience</span>
                </div>
            </div>
        </div>',
        htmlspecialchars($lawyer['name']),
        $specialization,
        $rating,
        $experience
    );
}
?> 