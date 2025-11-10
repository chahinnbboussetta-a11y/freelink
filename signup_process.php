<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- الخطوة 1: إعداد الاتصال بقاعدة البيانات (Database Connection) ---
// (تأكد من مطابقة هذه المعلومات لإعدادات XAMPP لديك)
$servername = "localhost"; // (عادةً localhost)
$username = "root";        // (الافتراضي في XAMPP)
$password = "";            // (الافتراضي في XAMPP هو "فارغ")
$dbname = "freelink_db";   // (اسم قاعدة البيانات التي أنشأتها)

try {
    // إنشاء اتصال PDO (طريقة احترافية وحديثة للاتصال)
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // ضبط وضع الأخطاء لإظهار الاستثناءات (Exceptions)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Ereue de la connexion" . $e->getMessage());
}


// --- الخطوة 2: استقبال البيانات من الاستمارة (POST Data) ---
// (نتحقق أولاً أن الطلب هو "POST")
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام وتنظيف المدخلات
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_plain = $_POST['password']; // كلمة المرور الخام
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role']; // (client or freelancer)

    // 2. التحقق من كلمة المرور
    if ($password_plain !== $password_confirm) {
        die("Erreur : Les mots de passe ne correspondent pas. Veuillez revenir en arrière et réessayer.");
    }
    
    // (إضافة مهمة: التحقق من أن كلمة المرور ليست فارغة أو قصيرة جداً)
    if (strlen($password_plain) < 8) {
        die("Erreur : Le mot de passe doit comporter au moins 8 caractères.");
    }

    // 3. التحقق من أن الإيميل غير مستخدم
    // (يجب أن يكون try/catch هنا أيضاً تحسباً لأي خطأ في الاتصال)
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            die("Erreur : Cette adresse e-mail est déjà utilisée.<a href='login.html'>Voulez-vous vous connecter ?</a>");
        }
    } catch (Exception $e) {
        die("Erreur lors de la vérification de l'adresse e-mail : " . $e->getMessage());
    }


    // 4. تجزئة (Hashing) كلمة المرور (الأمان أولاً!)
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    
    // --- الخطوة 3: إدراج (INSERT) البيانات في قاعدة البيانات ---
    try {
        // نبدأ "عملية" (Transaction) لضمان تنفيذ كل شيء أو لا شيء
        $conn->beginTransaction();

        // 5. إدراج المستخدم الأساسي في جدول `users`
        $stmt = $conn->prepare("INSERT INTO users (name, email, `password`, `role`) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password_hashed, $role]);
        
        // 6. (النقطة الاحترافية المطورة 🚀) أنشئ بروفايلاً للدور الصحيح
    $user_id = $conn->lastInsertId(); // (احصل على ID المستخدم الجديد)

    if ($role == 'freelancer') {
        // (إذا كان "طالباً"، أنشئ "بروفايل طالب")
        $stmt_profile = $conn->prepare("INSERT INTO freelancer_profiles (user_id, headline, bio) VALUES (?, ?, ?)");
        $stmt_profile->execute([$user_id, 'Nouveau talent sur FreeLink', 'Mettez à jour votre biographie...']);

    } elseif ($role == 'client') {
        // (إذا كان "عميلاً"، أنشئ "بروفايل عميل" 💼)
        $stmt_profile = $conn->prepare("INSERT INTO client_profiles (user_id, company_name, bio) VALUES (?, ?, ?)");
        $stmt_profile->execute([$user_id, 'Nouvelle Entreprise', 'Mettez à jour la description de votre entreprise...']);
    }

        // 7. إذا نجح كل شيء، قم بتأكيد "العملية" (Transaction)
        $conn->commit();

        // 9. توجيه المستخدم إلى صفحة تسجيل الدخول
        header("Location: login.html?status=signup_success");
        exit(); // (مهم جداً إيقاف الكود بعد التوجيه)

    } catch (Exception $e) {
        // إذا حدث أي خطأ، قم بإلغاء "العملية" (Transaction)
        $conn->rollBack();
        die("Une erreur s'est produite lors de l'enregistrement : " . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: signup.php");
    exit();
}
?>
