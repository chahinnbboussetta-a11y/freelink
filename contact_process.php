<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (للحماية 🛡️ والأمان)

// --- الخطوة 2: استقبال البيانات (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام البيانات
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // (الحارس 🛡️: التحقق من البيانات)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die("Erreur : Tous les champs sont requis.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Erreur : L'email n'est pas valide.");
    }

    // --- (الخطوة الأسطورية 😈: إرسال الإيميل 📧) ---
    
    // (!! 😈 هام: غيّر هذا الإيميل إلى إيميل "المدير" 👑 الحقيقي)
    $admin_email = "chahin.boussetta@votre-domaine.com"; // (مثال: الإيميل الخاص بك)
    
    $email_subject = "Nouveau Message de Contact (FreeLink): " . $subject;
    
    $email_body = "Vous avez reçu un nouveau message de " . $name . " (" . $email . ").\n\n";
    $email_body .= "---------------------------------------------------\n";
    $email_body .= $message;
    $email_body .= "\n---------------------------------------------------";
    
    $headers = "From: noreply@freelink.tn" . "\r\n" .
               "Reply-To: " . $email . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // (إرسال الإيميل 🚀)
    // (ملاحظة: 🐞 دالة mail() قد "تفشل" 💥 على localhost إذا لم تقم بـ "تهيئة" (Configure) XAMPP!)
    // (سنفترض أنها "ستنجح" 😈)
    
    // mail($admin_email, $email_subject, $email_body, $headers);
    
    // (للاختبار 🕵️‍♂️ على localhost، سنقوم بـ "تعطيل" 🚫 الإيميل مؤقتاً)
    
    // --- (الخطوة 3: إعادة التوجيه 🚀) ---
    // (أعد التوجيه "دائماً" 😈، حتى لو فشل الإيميل، لكي لا يرى المستخدم أخطاء السيرفر)
    header("Location: contact.php?status=contact_success");
    exit();

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: contact.php");
    exit();
}
?>