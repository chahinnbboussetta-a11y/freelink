<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; 

// --- (الخطوة 2: "الحارس" 🛡️) ---
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $current_user_id;

// --- الخطوة 3: استقبال البيانات (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام IDs
    $transaction_id = $_POST['transaction_id'];
    $project_id = $_POST['project_id'];

    if (empty($transaction_id) || empty($project_id)) {
        die("خطأ: بيانات الدفع ناقصة.");
    }

    // --- (التأمين الأسطوري 😈) ---
    // (نتأكد أن العميل يملك هذه "العملية" 💰 وأنها "معلقة" ⏳)
    try {
        $stmt_check = $conn->prepare(
            "SELECT id FROM transactions 
             WHERE id = ? AND client_id = ? AND status = 'pending'"
        );
        $stmt_check->execute([$transaction_id, $client_id]);
        $transaction = $stmt_check->fetch();

        if (!$transaction) {
            die("خطأ: لا يمكنك payer cette transaction.");
        }

    } catch (Exception $e) {
        die("خطأ في التحقق من العملية: " . $e->getMessage());
    }
    
    // --- الخطوة 4: (التحديث 🚀) - "المال" 💰 دفع! ---
    try {
        $conn->beginTransaction(); // (ابدأ العملية)

        // 1. تحديث "العملية" 💰 إلى "مدفوعة" (Paid)
        $stmt_trans = $conn->prepare("UPDATE transactions SET status = 'paid' WHERE id = ?");
        $stmt_trans->execute([$transaction_id]);
        
        // 2. (الأهم 😈) تحديث "المشروع" 🚀 إلى "قيد التنفيذ" (in_progress)
        $stmt_project = $conn->prepare("UPDATE projects SET status = 'in_progress' WHERE id = ?");
        $stmt_project->execute([$project_id]);

        // 3. (تم!) أكّد العملية
        $conn->commit();

        // 4. أعد توجيه العميل إلى "الداشبورد" 🕹️
        header("Location: dashboard-client.php?status=payment_success"); // (رسالة نجاح جديدة 🚀)
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("حدث خطأ فادح أثناء الدفع: " . $e->getMessage());
    }

} else {
    header("Location: dashboard-client.php");
    exit();
}
?>