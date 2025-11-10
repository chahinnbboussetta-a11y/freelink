<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (استخدم ".." 😈)

// --- (الخطوة 2: "الحارس" 🛡️ الأسطوري 😈) ---
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $current_user_id; 
// ($conn جاهز 🚀)

// --- الخطوة 3: "العقل" 🧠 (تنفيذ الأوامر 😈) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام IDs
    $project_id = $_POST['project_id'];
    $proposal_id = $_POST['proposal_id'];

    if (empty($project_id) || empty($proposal_id)) {
        die("خطأ: بيانات ناقصة.");
    }

    // --- (التأمين الأسطوري 😈) ---
    try {
        $stmt_check = $conn->prepare("SELECT id FROM projects WHERE id = ? AND client_id = ? AND status = 'open'");
        $stmt_check->execute([$project_id, $client_id]);
        $project = $stmt_check->fetch();

        if (!$project) {
            die("خطأ: لا يمكنك قبول عرض لهذا المشروع (إما أنه لا تملكه، أو أنه ليس 'open').");
        }

    } catch (Exception $e) {
        die("خطأ في التحقق من الملكية: " . $e->getMessage());
    }
    
    // --- الخطوة 4: (التورنيدو 🌪️ - "النسخة" النهائية 🏁) ---
    try {
        $conn->beginTransaction(); // (ابدأ العملية)

        // (أ. "اصطياد" 🕵️‍♂️ الـ ID والمبلغ 💰)
        $stmt_data = $conn->prepare("SELECT freelancer_id, amount FROM proposals WHERE id = ?");
        $stmt_data->execute([$proposal_id]);
        $proposal_data = $stmt_data->fetch(PDO::FETCH_ASSOC);
        $freelancer_id = $proposal_data['freelancer_id'];
        $amount = $proposal_data['amount'];

        if (empty($freelancer_id) || empty($amount)) {
            die("خطأ: بيانات العرض (Proposal) ناقصة.");
        }
        
        // (ب. تحديث العروض (Proposals) كالمعتاد 😈)
        $stmt_accept = $conn->prepare("UPDATE proposals SET status = 'accepted' WHERE id = ?");
        $stmt_accept->execute([$proposal_id]);
        $stmt_reject = $conn->prepare("UPDATE proposals SET status = 'rejected' WHERE project_id = ? AND id != ?");
        $stmt_reject->execute([$project_id, $proposal_id]);
        
        // (ج. "الكود الأسطوري" 😈: إنشاء "العملية" 💰 في "الخزنة" 🏦)
        $stmt_trans = $conn->prepare(
            "INSERT INTO transactions (project_id, client_id, freelancer_id, amount, status)
             VALUES (?, ?, ?, ?, 'pending')"
        );
        $stmt_trans->execute([$project_id, $client_id, $freelancer_id, $amount]);
        $transaction_id = $conn->lastInsertId(); // (احصل على "المفتاح" 🔑 للعملية)

        // (د. "الكود الأسطوري" 😈: إنشاء "المحادثة" 💬)
        $stmt_check_convo = $conn->prepare("SELECT id FROM conversations WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
        $stmt_check_convo->execute([$client_id, $freelancer_id, $freelancer_id, $client_id]);
        
        if (!$stmt_check_convo->fetch()) {
            // (أنشئ 🚀 المحادثة!)
            $stmt_create_convo = $conn->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)");
            $stmt_create_convo->execute([$client_id, $freelancer_id]);
        }
        // --- (نهاية كود المحادثة 💬) ---

        // (هـ. "تم!" 🏁)
        $conn->commit();

        // (و. "إعادة التوجيه" 😈 إلى "بوابة الدفع" 💳)
        header("Location: pay.php?tid=" . $transaction_id); // (tid = Transaction ID)
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("حدث خطأ فادح أثناء قبول العرض: " . $e->getMessage());
    }

} else {
    header("Location: dashboard-client.php");
    exit();
}
?>