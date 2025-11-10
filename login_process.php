<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (الأهم 😈) ابدأ الجلسة (Session) ---
// يجب أن يكون هذا السطر في *كل* صفحة محمية (مثل الداشبورد)
session_start();

// --- الخطوة 1: إعداد الاتصال بقاعدة البيانات ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelink_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Échec de la connexion :" . $e->getMessage());
}

// --- الخطوة 2: استقبال بيانات "تسجيل الدخول" ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام المدخلات
    $email = trim($_POST['email']);
    $password_plain = $_POST['password'];

    // 2. التحقق من أن الحقول ليست فارغة
    if (empty($email) || empty($password_plain)) {
        die("Erreur : Veuillez remplir les champs adresse e-mail et mot de passe.");
    }

    // --- الخطوة 3: البحث عن المستخدم ومطابقة كلمة المرور ---
    try {
        // 3. ابحث عن المستخدم عن طريق الإيميل
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // جلب المستخدم كـ "مصفوفة" (array)

        // 4. (الأمان) التحقق من كلمة المرور
        if ($user && password_verify($password_plain, $user['password'])) {
            // --- نجح تسجيل الدخول! ---

            // 5. (الأهم) قم بتخزين معلومات المستخدم في "الجلسة" (Session)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // 6. (التوجيه الاحترافي) قم بتوجيه المستخدم بناءً على "دوره" (role)
            if ($user['role'] == 'client') {
                header("Location: dashboard-client.php");
                exit();
            } elseif ($user['role'] == 'freelancer') {
                header("Location: dashboard-student.php");
                exit();
            } else {
                // (احتياطاً إذا كان الدور غير معروف)
                die("Erreur : le rôle de l’utilisateur est inconnu.");
            }

        } else {
            // (خطأ شائع) لا تخبر المستخدم *بالضبط* ما هو الخطأ (أمان)
            die("Erreur : Adresse e-mail ou mot de passe incorrect. <a href='login.html'>Essayer à nouveau</a>");
        }

    } catch (Exception $e) {
        die("Une erreur s'est produite lors de la connexion :" . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: login.html
    ");
    exit();
}/*
// ... (في ملف login_process.php)
if ($user['role'] == 'client') {
    header("Location: dashboard-client.php"); // <--- تأكد أنها .php
    exit();
} elseif ($user['role'] == 'freelancer') {
    header("Location: dashboard-student.php"); // <--- تأكد أنها .php
    exit();
}
// ...*/
?>