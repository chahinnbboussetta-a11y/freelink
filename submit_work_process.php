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
    die("فشل الاتصال: " . $e->getMessage());
}

// --- الخطوة 2: استقبال البيانات (فقط إذا كان الطلب POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام ID المشروع
    $project_id = $_POST['project_id'];

    if (empty($project_id)) {
        die("خطأ: بيانات ناقصة.");
    }

    // --- (التأمين الأسطوري 😈) ---
    // سنتأكد أن هذا الطالب هو فعلاً من يعمل على هذا المشروع (in_progress)
    try {
        $stmt_check = $conn->prepare(
            "SELECT p.id FROM projects p
             JOIN proposals pr ON p.id = pr.project_id
             WHERE p.id = ? 
             AND pr.freelancer_id = ? 
             AND p.status = 'in_progress' 
             AND pr.status = 'accepted'"
        );
        $stmt_check->execute([$project_id, $freelancer_id]);
        $project = $stmt_check->fetch();

        if (!$project) {
            die("خطأ: لا يمكنك تسليم هذا المشروع (إما أنه ليس 'in_progress' أو أنك لست الطالب المقبول).");
        }

    } catch (Exception $e) {
        die("خطأ في التحقق من الملكية: " . $e->getMessage());
    }
    
    // --- الخطوة 3: (التحديث 🚀) - تغيير حالة المشروع ---
    try {
        // 1. تحديث حالة المشروع إلى "قيد المراجعة"
        $stmt_project = $conn->prepare("UPDATE projects SET status = 'in_review' WHERE id = ?");
        $stmt_project->execute([$project_id]);

        // 2. (تم!) أعد توجيه الطالب إلى نفس الصفحة مع رسالة نجاح
        // (سنحتاج إلى ID المحادثة لإعادة التوجيه... سنبسطها الآن)
        // (تحديث: سنعيده إلى الداشبورد الخاص به)
        header("Location: dashboard-student.php?status=work_submitted");
        exit();

    } catch (Exception $e) {
        die("حدث خطأ فادح أثناء تسليم العمل: " . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: dashboard-student.php");
    exit();
}
?>