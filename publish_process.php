<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (حارس البوابة 😈) ---
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $_SESSION['user_id'];

// --- الخطوة 1: الاتصال ---
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// --- الخطوة 2: استقبال البيانات (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام البيانات النصية
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = !empty($_POST['budget']) ? trim($_POST['budget']) : null;
    $duration = !empty($_POST['duration']) ? trim($_POST['duration']) : null;
    
    // (الكود الأسطوري الجديد 😈: استقبال "التعليقات" 📝)
    $additional_notes = trim($_POST['additional_notes']) ?? null; 
    
    // (الكود الأسطوري الجديد 😈: استقبال "المصفوفة" 🚀)
    $skills_array_ids = $_POST['skills'] ?? []; // (استقبال "المصفوفة" 😈)

    // (الحارس الأسطوري 😈)
    if (empty($title) || empty($description)) {
        header("Location: publish-project.php?error=empty_fields");
        exit();
    }

    $final_file_path = null; 

    // --- الخطوة 3: (الوحش 😈) التعامل مع رفع الملف (المؤمّن 🛡️) ---
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'uploads/projects/'; 
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        
        $file = $_FILES['file'];
        $max_size = 10 * 1024 * 1024; // (10MB)
        if ($file['size'] > $max_size) { die("خطأ: الملف كبير جداً (10MB الأقصى)."); }

        $allowed_types = [
            'application/pdf' => '.pdf',
            'application/zip' => '.zip',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
            'text/plain' => '.txt'
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime_type, $allowed_types)) {
            die("خطأ: نوع الملف غير مسموح به (فقط PDF, ZIP, DOCX, TXT).");
        }
        
        $file_extension = $allowed_types[$mime_type];
        $file_name = "project_" . uniqid() . $file_extension;
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $final_file_path = $upload_path; 
        } else {
            die("خطأ: فشل في نقل ملف المشروع.");
        }
    }
    // --- (نهاية "الحارس" 🛡️) ---

    // --- الخطوة 4: (الأسطورية 😈) إدراج كل شيء في قاعدة البيانات ---
    try {
        $conn->beginTransaction(); 

        // 1. إدراج المشروع الأساسي (مع "التعليقات" 📝)
        // 1. إدراج المشروع الأساسي (النسخة "المطورة" 🚀)
$stmt_project = $conn->prepare(
    "INSERT INTO projects (client_id, title, description, budget, duration, file_path, status, additional_notes) 
     VALUES (?, ?, ?, ?, ?, ?, 'pending_approval', ?)" // (الآن هو "بانتظار الموافقة" 😈)
);
// (تأكد أن باقي الكود (execute) سليم 100%)
        ;
        $stmt_project->execute([
            $client_id, $title, $description, $budget, $duration, $final_file_path, $additional_notes 
        ]);

        $project_id = $conn->lastInsertId();

        // 3. (التعامل مع "مصفوفة" الـ IDs 🚀)
        if (!empty($skills_array_ids)) {
            $stmt_link_skill = $conn->prepare("INSERT IGNORE INTO project_skill (project_id, skill_id) VALUES (?, ?)");
            
            foreach ($skills_array_ids as $skill_id) {
                if (is_numeric($skill_id)) {
                    $stmt_link_skill->execute([$project_id, $skill_id]);
                }
            }
        }
        // --- (نهاية كود المهارات) ---

        $conn->commit();
        header("Location: dashboard-client.php?status=project_success");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("حدث خطأ أثناء نشر المشروع: " . $e->getMessage());
    }

} else {
    header("Location: publish-project.php");
    exit();
}
?>