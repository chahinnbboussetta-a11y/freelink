<?php
// --- (الملف الأسطوري 😈: "ملف واحد ليحكمهم جميعاً" 👑) ---

// --- (الكود السري لإظهار الأخطاء 🐞) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- (الأهم 😈: بدء "الجلسة" 🛡️) ---
session_start();

// --- الخطوة 1: "المفاتيح" 🔑 (Keys) ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelink_db";

// --- الخطوة 2: "الاتصال" 💾 (The Connection) ---
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال الأسطوري 😈: " . $e->getMessage());
}

// --- (الخطوة 3: "الجسر" 🌉 "المطور" 🚀) ---
$current_user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;
$user_email = null; // (تهيئة 😈)

// (الترقية 🚀: جلب الإيميل 📧 إذا كان مسجلاً 🕵️‍♂️)
if ($current_user_id) {
    try {
        $stmt_email = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt_email->execute([$current_user_id]);
        $user_email = $stmt_email->fetch(PDO::FETCH_COLUMN); // (اصطياد 🕵️‍♂️ الإيميل)
    } catch (Exception $e) {
        // (في حال حدث خطأ، لا توقف الموقع 😈)
        $user_email = null;
    }
}
?>