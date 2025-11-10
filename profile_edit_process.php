<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (حارس البوابة 😈) ---
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'freelancer') {
    header("Location: login.html");
    exit();
}
$student_id = $_SESSION['user_id'];

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
    $headline = trim($_POST['headline']);
    $bio = trim($_POST['bio']);
    $university = trim($_POST['university']);
    $major = trim($_POST['major']);
    
    // (الأهم 😈: استقبال "المصفوفة" 🚀 من "العلامات")
    $skills_array_ids = $_POST['skills'] ?? []; // (استقبال "المصفوفة" 😈)

    $final_file_path = null; 

    // --- الخطوة 3: (الوحش 😈) التعامل مع رفع الصورة ---
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        
        $upload_dir = 'uploads/avatars/'; 
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if($check === false) { die("خطأ: الملف الذي تم رفعه ليس صورة."); }

        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $file_name = "user_" . $student_id . "_" . uniqid() . "." . $file_extension;
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

        // 1. تحديث `users` (الاسم)
        $stmt_user = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt_user->execute([$name, $student_id]);
        $_SESSION['user_name'] = $name; 

        // 2. تحديث `freelancer_profiles` (البيانات + الصورة)
        if ($final_file_path) {
            $stmt_profile = $conn->prepare(
                "UPDATE freelancer_profiles 
                 SET headline = ?, bio = ?, university = ?, major = ?, profile_picture = ?
                 WHERE user_id = ?"
            );
            $stmt_profile->execute([$headline, $bio, $university, $major, $final_file_path, $student_id]);
        } else {
            $stmt_profile = $conn->prepare(
                "UPDATE freelancer_profiles 
                 SET headline = ?, bio = ?, university = ?, major = ?
                 WHERE user_id = ?"
            );
            $stmt_profile->execute([$headline, $bio, $university, $major, $student_id]);
        }

        // --- (الكود الأسطوري 😈: "Nuke and Rebuild" 💣🚀) ---

        // 3. (القنبلة 💣 "Nuke") احذف كل المهارات القديمة
        $stmt_delete_skills = $conn->prepare("DELETE FROM student_skill WHERE user_id = ?");
        $stmt_delete_skills->execute([$student_id]);

        // 4. (إعادة البناء 🚀) أضف المهارات الجديدة (من "المصفوفة" 😈)
        if (!empty($skills_array_ids)) {
            // (استعد 😈)
            $stmt_link_skill = $conn->prepare("INSERT IGNORE INTO student_skill (user_id, skill_id) VALUES (?, ?)");
            
            foreach ($skills_array_ids as $skill_id) {
                // (لا نحتاج للبحث 🕵️‍♂️، الـ ID موجود 🚀)
                $stmt_link_skill->execute([$student_id, $skill_id]);
            }
        }
        // --- (نهاية كود المهارات) ---

        $conn->commit();
        header("Location: profile-edit.php?status=profile_success");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("حدث خطأ أثناء تحديث البروفايل: " . $e->getMessage());
    }

} else {
    header("Location: profile-edit.php");
    exit();
}
?>