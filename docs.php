<?php
session_start();
require_once 'includes/functions.php';
require_once 'database/database.php';
require_once 'includes/document_generator.php';

// Check if user is logged in
if (!isAuthenticated()) {
    header('Location: login.php');
    exit;
}

// Get user ID and role
$userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
$isAdmin = isAdmin();
$error = '';
$success = '';

// Initialize database connection
try {
    $pdo = getDBConnection();
    createDocumentsTable($pdo);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    $error = "Database connection failed. Please try again later.";
}

// Initialize AI Document Generator
$aiGenerator = new DocumentGenerator('YOUR_OPENAI_API_KEY'); // Replace with your actual API key

// Handle document submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'generate') {
                // Handle document generation
                $title = trim($_POST['title'] ?? '');
                $documentType = trim($_POST['document_type'] ?? '');
                $details = trim($_POST['details'] ?? '');
                $format = trim($_POST['format'] ?? 'html');
                
                if (empty($title) || empty($documentType) || empty($details)) {
                    $error = 'Please fill in all required fields';
                } else {
                    // Generate document using AI
                    $content = $aiGenerator->generateDocument($documentType, $details, $format);
                    
                    // Store in database
                    $stmt = $pdo->prepare("INSERT INTO documents (user_id, title, document_type, content, status) VALUES (?, ?, ?, ?, 'draft')");
                    
                    if (!$stmt) {
                        throw new Exception("Failed to prepare statement: " . $pdo->errorInfo()[2]);
                    }
                    
                    $result = $stmt->execute([$userId, $title, $documentType, $content]);
                    
                    if (!$result) {
                        throw new Exception("Failed to execute statement: " . $stmt->errorInfo()[2]);
                    }
                    
                    if ($stmt->rowCount() > 0) {
                        $success = 'Document generated successfully!';
                    } else {
                        throw new Exception("No rows were inserted");
                    }
                }
            } elseif ($_POST['action'] === 'submit') {
                // Handle document upload
                $title = $_POST['title'] ?? '';
                $documentType = $_POST['document_type'] ?? '';
                $file = $_FILES['document_file'] ?? null;

                if (empty($title) || empty($documentType) || !$file || $file['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Please fill in all required fields and upload a valid file';
                } else {
                    // Validate file type
                    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    if (!in_array($file['type'], $allowedTypes)) {
                        $error = 'Only PDF, DOC, and DOCX files are allowed';
                    } else {
                        // Generate unique filename
                        $filename = uniqid() . '_' . basename($file['name']);
                        $uploadPath = 'uploads/documents/' . $filename;

                        // Create uploads directory if it doesn't exist
                        if (!file_exists('uploads/documents')) {
                            mkdir('uploads/documents', 0777, true);
                        }

                        // Move uploaded file
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $stmt = $pdo->prepare("INSERT INTO documents (user_id, title, document_type, file_path, status) VALUES (?, ?, ?, ?, 'submitted')");
                            if ($stmt->execute([$userId, $title, $documentType, $uploadPath])) {
                                $success = 'Document submitted successfully!';
                            } else {
                                $error = 'Failed to submit document';
                                // Clean up uploaded file if database insert fails
                                unlink($uploadPath);
                            }
                        } else {
                            $error = 'Failed to upload file';
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        error_log("Document operation error: " . $e->getMessage());
        $error = "An error occurred while processing your request. Please try again.";
    }
}

// Get documents based on user role
try {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching documents: " . $e->getMessage());
    $documents = [];
    $error = "Error loading documents. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isAdmin ? 'Document Management' : 'Submit Document'; ?> - LegalEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#FBBF24',
                        accent: '#10B981',
                        neutral: '#64748B',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
        }
        .dark body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
            padding-top: 0;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dark .card {
            background: rgba(30, 41, 59, 0.95);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        /* Full Width Header Styles */
        .page-header {
            background: linear-gradient(90deg, var(--primary) 0%, #3B82F6 100%);
            padding: 5rem 0;
            border-radius: 0;
            margin-bottom: 2.5rem;
            margin-top: 0;
            position: relative;
            overflow: hidden;
            box-shadow: none;
            min-height: 300px;
            display: flex;
            align-items: center;
            width: 100vw;
            margin-left: calc(-50vw + 50%);
            margin-right: calc(-50vw + 50%);
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                url('https://images.unsplash.com/photo-1575505586569-646b2ca898fc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover,
                linear-gradient(90deg, rgba(79, 70, 229, 0.85) 0%, rgba(59, 130, 246, 0.85) 100%);
            background-blend-mode: overlay;
            opacity: 0.9;
        }
        .page-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        .header-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .header-text {
            max-width: 800px;
        }
        .header-text h1 {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            line-height: 1.2;
        }
        .header-text p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
        }

        .security-banner {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .security-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .security-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .security-item i {
            font-size: 1.25rem;
            color: var(--secondary);
        }
        .security-item span {
            color: white;
            font-weight: 500;
            font-size: 0.95rem;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @media (max-width: 768px) {
            .header-text h1 {
                font-size: 2rem;
            }
            .header-text p {
                font-size: 1.1rem;
            }
            .security-banner {
                flex-direction: column;
                gap: 1rem;
            }
        }

        .tab-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(203, 213, 225, 0.2);
        }
        .dark .tab-container {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .tab-button {
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            background: rgba(241, 245, 249, 0.8);
            color: var(--primary);
            border: 1px solid rgba(203, 213, 225, 0.3);
        }
        .dark .tab-button {
            background: rgba(51, 65, 85, 0.8);
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .tab-button.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            border-color: transparent;
        }
        .dark .tab-button.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        }
        .tab-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .tab-button.active:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }
        .tab-button i {
            margin-right: 0.5rem;
            transition: transform 0.3s ease;
        }
        .tab-button:hover i {
            transform: scale(1.1);
        }
        .tab-button.active i {
            color: white;
        }
        .dark .tab-button i {
            color: rgba(255, 255, 255, 0.7);
        }
        .dark .tab-button.active i {
            color: white;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }
        .dark .form-section {
            background: rgba(30, 41, 59, 0.95);
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1.75rem;
        }
        .input-wrapper input,
        .input-wrapper select,
        .input-wrapper textarea {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3.5rem;
            border: 2px solid rgba(203, 213, 225, 0.4);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }
        .dark .input-wrapper input,
        .dark .input-wrapper select,
        .dark .input-wrapper textarea {
            background: rgba(51, 65, 85, 0.9);
            border-color: rgba(255, 255, 255, 0.15);
            color: white;
        }
        .input-wrapper input:focus,
        .input-wrapper select:focus,
        .input-wrapper textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            overflow: hidden;
        }
        .dark .table-container {
            background: rgba(30, 41, 59, 0.95);
        }
        .table-container table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-container th {
            background: rgba(241, 245, 249, 0.9);
            padding: 1.25rem;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
        }
        .dark .table-container th {
            background: rgba(51, 65, 85, 0.9);
        }
        .table-container td {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(203, 213, 225, 0.2);
            transition: background 0.2s ease;
        }
        .dark .table-container td {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        .table-container tr:hover td {
            background: rgba(79, 70, 229, 0.05);
        }

        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }
        .btn-secondary {
            background: rgba(203, 213, 225, 0.3);
            color: var(--primary);
        }
        .btn-secondary:hover {
            background: rgba(203, 213, 225, 0.5);
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #EF4444;
            color: white;
        }
        .btn-danger:hover {
            background: #DC2626;
            transform: translateY(-2px);
        }

        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }
        .notification.success {
            background: var(--accent);
            color: white;
        }
        .notification.error {
            background: #EF4444;
            color: white;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        .drop-zone {
            transition: all 0.3s ease;
        }
        .drop-zone.active {
            background: rgba(79, 70, 229, 0.1);
            border-color: var(--primary);
            transform: scale(1.02);
        }

        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'includes/navbar.php'; ?>

    <div class="main-container">
        <!-- Notifications -->
        <?php if ($success): ?>
            <div class="notification success animate-fade-in"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="notification error animate-fade-in"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h1 class="animate-fade-in">Document Management</h1>
                    <p class="animate-fade-in" style="animation-delay: 0.2s">
                        Streamline your legal document workflow with our comprehensive management system
                    </p>
                </div>
                <div class="security-banner animate-fade-in" style="animation-delay: 0.4s">
                    <div class="security-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure</span>
                    </div>
                    <div class="security-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Compliant</span>
                    </div>
                    <div class="security-item">
                        <i class="fas fa-bolt"></i>
                        <span>Efficient</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-container card" id="generate">
            <div class="flex flex-wrap gap-3">
                <button class="tab-button active" data-tab="generate">
                    <i class="fas fa-file-signature"></i>Generate
                </button>
                <button class="tab-button" data-tab="submit">
                    <i class="fas fa-upload"></i>Upload
                </button>
                
            </div>
        </div>

        <!-- Generate Tab -->
        <div class="tab-content active animate-fade-in" id="generate-tab">
            <div class="form-section card">
                <h2 class="text-2xl font-bold text-primary mb-6">Generate New Document</h2>
                <form id="documentForm" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="generate">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="input-wrapper">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Type</label>
                            <div class="relative">
                                <select name="document_type" required class="appearance-none">
                                    <option value="" disabled selected>Select Document Type</option>
                                    <option>Legal Notice</option>
                                    <option>Contract Agreement</option>
                                    <option>Power of Attorney</option>
                                    <option>Will</option>
                                    <option>Affidavit</option>
                                    <option>Lease Agreement</option>
                                    <option>Non-Disclosure Agreement</option>
                                    <option>Employment Contract</option>
                                </select>
                                <i class="fas fa-file-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="input-wrapper">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title</label>
                            <div class="relative">
                                <input type="text" name="title" placeholder="Enter document title" required>
                                <i class="fas fa-heading absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Details</label>
                        <div class="relative">
                            <textarea name="details" rows="6" placeholder="Enter document details and requirements..." required></textarea>
                            <i class="fas fa-align-left absolute left-3 top-5 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="input-wrapper">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Output Format</label>
                        <div class="relative">
                            <select name="format" required class="appearance-none">
                                <option value="pdf" selected>PDF</option>
                                <option value="html">HTML</option>
                                <option value="docx">DOCX</option>
                            </select>
                            <i class="fas fa-file-export absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" class="btn btn-secondary">
                            <i class="fas fa-times"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-download"></i>Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submit Tab -->
        <div class="tab-content animate-fade-in" id="submit-tab">
            <div class="form-section card">
                <h2 class="text-2xl font-bold text-primary mb-6">Upload Document</h2>
                <form id="submitForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="input-wrapper">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Type</label>
                            <div class="relative">
                                <select name="document_type" required class="appearance-none">
                                    <option value="" disabled selected>Select Document Type</option>
                                    <option>Legal Notice</option>
                                    <option>Contract Agreement</option>
                                    <option>Power of Attorney</option>
                                    <option>Will</option>
                                    <option>Affidavit</option>
                                    <option>Lease Agreement</option>
                                    <option>Non-Disclosure Agreement</option>
                                    <option>Employment Contract</option>
                                </select>
                                <i class="fas fa-file-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="input-wrapper">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title</label>
                            <div class="relative">
                                <input type="text" name="title" placeholder="Enter document title" required>
                                <i class="fas fa-heading absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="drop-zone border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-10 text-center">
                        <div class="max-w-md mx-auto">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400 mb-2 font-medium">Drag and drop your document here</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mb-4">or</p>
                            <input type="file" id="fileInput" name="document_file" class="hidden" accept=".pdf,.doc,.docx">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-folder-open"></i>Browse Files
                            </button>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-3">Supported formats: PDF, DOC, DOCX</p>
                            <p id="fileName" class="text-sm text-gray-600 dark:text-gray-400 mt-2"></p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" class="btn btn-secondary">
                            <i class="fas fa-times"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Document History -->
        <div class="table-container card">
            <div class="flex items-center justify-between p-6">
                <h2 class="text-2xl font-bold text-primary">Recent Documents</h2>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search documents..." 
                               class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 
                                      focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="sortDate" class="btn btn-secondary btn-sm">
                            <i class="fas fa-sort-amount-down"></i> Sort by Date
                        </button>
                        <button id="filterType" class="btn btn-secondary btn-sm">
                            <i class="fas fa-filter"></i> Filter by Type
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Document</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Date</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($documents as $doc): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 animate-fade-in">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt text-primary mr-3"></i>
                                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($doc['title']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary">
                                        <?php echo htmlspecialchars($doc['document_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        <?php echo date('M d, Y', strtotime($doc['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-start space-x-2">
                                        <?php if (!empty($doc['file_path'])): ?>
                                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" 
                                               class="btn btn-primary btn-sm" 
                                               title="Download Document"
                                               download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($doc['content'])): ?>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm"
                                                    title="View Document"
                                                    onclick="viewContent('<?php echo htmlspecialchars($doc['content']); ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($isAdmin || $doc['user_id'] == $userId): ?>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');" class="inline">
                                                <input type="hidden" name="document_id" value="<?php echo $doc['id']; ?>">
                                                <button type="submit" 
                                                        name="delete_document" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Delete Document">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($documents)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No documents found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            themeToggle.innerHTML = document.documentElement.classList.contains('dark') 
                ? '<i class="fas fa-sun mr-2"></i>Light Mode'
                : '<i class="fas fa-moon mr-2"></i>Dark Mode';
        });

        // Set initial theme
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            themeToggle.innerHTML = '<i class="fas fa-sun mr-2"></i>Light Mode';
        } else {
            themeToggle.innerHTML = '<i class="fas fa-moon mr-2"></i>Dark Mode';
        }

        // Tab switching
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });

        // File upload handling
        const dropZone = document.querySelector('.drop-zone');
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileName');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('active');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('active');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('active');
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                fileNameDisplay.textContent = files[0].name;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            }
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Form validation
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.querySelectorAll(':invalid').forEach(field => {
                        field.classList.add('border-red-500');
                        field.addEventListener('input', () => field.classList.remove('border-red-500'), { once: true });
                    });
                }
            });
        });

        // Auto-hide notifications
        setTimeout(() => {
            document.querySelectorAll('.notification').forEach(el => {
                el.style.transition = 'opacity 0.3s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            });
        }, 3000);

        // Enhanced table functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sortDateBtn = document.getElementById('sortDate');
            const filterTypeBtn = document.getElementById('filterType');
            const tableBody = document.querySelector('tbody');
            let isAscending = true;

            // Sort by date functionality
            sortDateBtn.addEventListener('click', function() {
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                rows.sort((a, b) => {
                    const dateA = new Date(a.cells[2].textContent);
                    const dateB = new Date(b.cells[2].textContent);
                    return isAscending ? dateA - dateB : dateB - dateA;
                });
                
                rows.forEach(row => tableBody.appendChild(row));
                isAscending = !isAscending;
                
                // Update sort icon
                sortDateBtn.innerHTML = isAscending 
                    ? '<i class="fas fa-sort-amount-down"></i> Sort by Date'
                    : '<i class="fas fa-sort-amount-up"></i> Sort by Date';
            });

            // Filter by type functionality
            filterTypeBtn.addEventListener('click', function() {
                const types = new Set();
                document.querySelectorAll('tbody tr').forEach(row => {
                    types.add(row.cells[1].textContent.trim());
                });
                
                const filterOptions = Array.from(types).map(type => 
                    `<option value="${type}">${type}</option>`
                ).join('');
                
                const select = document.createElement('select');
                select.className = 'appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-primary';
                select.innerHTML = `
                    <option value="">All Types</option>
                    ${filterOptions}
                `;
                
                select.addEventListener('change', function() {
                    const selectedType = this.value;
                    document.querySelectorAll('tbody tr').forEach(row => {
                        const type = row.cells[1].textContent.trim();
                        row.style.display = selectedType === '' || type === selectedType ? '' : 'none';
                    });
                });
                
                filterTypeBtn.replaceWith(select);
            });

            // Enhanced search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });

        // Enhanced view content modal
        function viewContent(content) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-primary">Document Content</h3>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="prose dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-900 p-6 rounded-lg">
                        ${content}
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
    </script>
</body>
</html>