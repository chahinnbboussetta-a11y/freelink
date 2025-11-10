<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once '../config.php'; // (استخدم ".." 😈)

// --- الخطوة 2: استقبال البيانات (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام المدخلات
    $email = trim($_POST['email']);
    $password_plain = $_POST['password'];

    if (empty($email) || empty($password_plain)) {
        header("Location: index.php?error=empty");
        exit();
    }

    // --- (الخطوة 3: "الاصطياد" 🕵️‍♂️ الأسطوري 😈) ---
    try {
        // (ابحث 🕵️‍♂️ عن "مدير" 👑 *فقط*)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. (الأمان 🛡️) التحقق من كلمة المرور
        if ($admin && password_verify($password_plain, $admin['password'])) {
            // --- نجح تسجيل الدخول! 🔥 ---

            // 5. (الأهم 😈) قم بتخزين "المدير" 👑 في "الجلسة" (Session)
            session_regenerate_id(true); 
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['user_role'] = $admin['role']; // (سيكون "admin")

            // 6. (التوجيه 🚀) اذهب إلى "العرش" 👑
            header("Location: dashboard.php"); 
            exit();

        } else {
            // (اطرده 😈)
             header("Location: index.php?error=invalid");
             exit();
        }

    } catch (Exception $e) {
         header("Location: index.php?error=db_error");
         exit();
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: index.php");
    exit();
}
?>