<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (حارس البوابة 😈) ---
session_start(); // 1. ابدأ الجلسة

// 2. هل المستخدم مسجل أصلاً؟ وهل هو "freelancer"؟
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'freelancer') {
    header("Location: login.html");
    exit();
}

// 3. احصل على ID الطالب من الجلسة
$freelancer_id = $_SESSION['user_id'];

// --- الخطوة 1: إعداد الاتصال بقاعدة البيانات ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // (هذا هو السطر الذي تم تصحيحه)
    die("فشل الاتصال: " . $e->getMessage());
}

// --- الخطوة 2: استقبال البيانات (فقط إذا كان الطلب POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام البيانات النصية + ID المشروع
    $project_id = trim($_POST['project_id']);
    $amount = trim($_POST['amount']);
    $duration = trim($_POST['duration']);
    $cover_letter = trim($_POST['cover_letter']);

    // (تحقق بسيط)
    if (empty($project_id) || empty($amount) || empty($duration) || empty($cover_letter)) {
        die("Erreur : Veuillez remplir tous les champs obligatoires.");
    }

    $final_file_path = null; // (مسار الملف المرفق)

    // --- الخطوة 3: (الوحش 😈) التعامل مع رفع الملف (إن وجد) ---
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        
        $upload_dir = 'uploads/proposals/'; // (أنشئ مجلد "proposals" داخل "uploads"!)
        
        // (تأكد من وجود المجلد)
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['file']['name']);
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            $final_file_path = $upload_path; // (نجح الرفع!)
        } else {
            die("Erreur : Échec du transfert du fichier téléchargé.");
        }
    }

    // --- الخطوة 4: (الأسطورية) إدراج العرض (Proposal) في الداتابيس ---
    try {
        
        // (تحقق أخير: هل قدم عرضاً بالفعل؟ - حماية إضافية 😈)
        $stmt_check = $conn->prepare("SELECT id FROM proposals WHERE project_id = ? AND freelancer_id = ?");
        $stmt_check->execute([$project_id, $freelancer_id]);
        if ($stmt_check->fetch()) {
            die("Erreur : J'ai déjà soumis une offre pour ce projet.");
        }

        // 1. إدراج العرض الأساسي
        $stmt_proposal = $conn->prepare(
            "INSERT INTO proposals (project_id, freelancer_id, amount, duration, cover_letter, attachment_path, status) 
             VALUES (?, ?, ?, ?, ?, ?, 'pending')"
        );
        $stmt_proposal->execute([
            $project_id,
            $freelancer_id,
            $amount,
            $duration,
            $cover_letter,
            $final_file_path
        ]);

        // 2. (تم!) أعد توجيه الطالب إلى نفس الصفحة مع رسالة نجاح
        header("Location: project-details.php?id=" . $project_id . "&status=proposal_success");
        exit();

    } catch (Exception $e) {
        die("Une erreur s'est produite lors de la soumission de l'offre :" . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: explore-projects.php");
    exit();
}
?>