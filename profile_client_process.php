<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (حارس البوابة 😈 - نسخة العميل 💼) ---
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
    $name = trim($_POST['name']);
    $company_name = trim($_POST['company_name']);
    $website = trim($_POST['website']);
    $bio = trim($_POST['bio']);

    $final_file_path = null; 

    // --- الخطوة 3: (الوحش 😈) التعامل مع رفع الصورة ---
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        
        $upload_dir = 'uploads/avatars/'; // (سنستخدم نفس مجلد "avatars" 🚀)
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if($check === false) { die("خطأ: الملف الذي تم رفعه ليس صورة."); }

        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $file_name = "user_" . $client_id . "_" . uniqid() . "." . $file_extension;
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $final_file_path = $upload_path; 
        } else {
            die("خطأ: فشل في نقل صورة البروفايل.");
        }
    }

    // --- الخطوة 4: (الأسطورية 😈) تحديث (UPDATE) قاعدة البيانات ---
    try {
        $conn->beginTransaction(); // (ابدأ العملية)

        // 1. تحديث جدول `users` (الاسم)
        $stmt_user = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt_user->execute([$name, $client_id]);
        $_SESSION['user_name'] = $name; // (تحديث الجلسة 🚀)

        // 2. تحديث جدول `client_profiles` (البيانات + الصورة)
        if ($final_file_path) {
            // (تحديث كل شيء + الصورة)
            $stmt_profile = $conn->prepare(
                "UPDATE client_profiles 
                 SET company_name = ?, website = ?, bio = ?, profile_picture = ?
                 WHERE user_id = ?"
            );
            $stmt_profile->execute([$company_name, $website, $bio, $final_file_path, $client_id]);
        } else {
            // (تحديث كل شيء *ما عدا* الصورة)
            $stmt_profile = $conn->prepare(
                "UPDATE client_profiles 
                 SET company_name = ?, website = ?, bio = ?
                 WHERE user_id = ?"
            );
            $stmt_profile->execute([$company_name, $website, $bio, $client_id]);
        }

        $conn->commit();
        header("Location: profile-client-edit.php?status=profile_success"); // (سنستخدم نفس رسالة النجاح 😈)
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("حدث خطأ أثناء تحديث البروفايل: " . $e->getMessage());
    }

} else {
    header("Location: profile-client-edit.php");
    exit();
}
?>