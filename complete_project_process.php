<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - نسخة العميل 💼) ---
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html");
    exit();
}
// (نستخدم $current_user_id بدلاً من $client_id)
$client_id = $current_user_id; 

// --- الخطوة 3: استقبال البيانات (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. استلام البيانات
    $project_id = $_POST['project_id'];
    $freelancer_id = $_POST['freelancer_id'];
    $rating = $_POST['rating']; // (النجوم ⭐️)
    $comment = trim($_POST['comment']); // (التعليق)

    if (empty($project_id) || empty($freelancer_id) || empty($rating)) {
        die("خطأ: بيانات التقييم ناقصة.");
    }

    // --- (التأمين الأسطوري 😈) ---
    // (نتأكد أن العميل يملك المشروع وأنه "قيد المراجعة")
    try {
        $stmt_check = $conn->prepare(
            "SELECT id FROM projects 
             WHERE id = ? AND client_id = ? AND status = 'in_review'"
        );
        $stmt_check->execute([$project_id, $client_id]);
        $project = $stmt_check->fetch();

        if (!$project) {
            die("خطأ: لا يمكنك إكمال هذا المشروع (إما لا تملكه أو أنه ليس 'in_review').");
        }

    } catch (Exception $e) {
        die("خطأ في التحقق من الملكية: " . $e->getMessage());
    }
    
    // --- الخطوة 4: (المستوى الأخير 😈) - تنفيذ التحديثات (Transaction) ---
    try {
        $conn->beginTransaction(); // (ابدأ العملية)

        // 1. تحديث حالة المشروع إلى "مكتمل" (COMPLETED 🏁)
        $stmt_project = $conn->prepare("UPDATE projects SET status = 'completed' WHERE id = ?");
        $stmt_project->execute([$project_id]);

        // 2. إدراج (INSERT) التقييم ⭐️ في "البيس" 💾
        $stmt_review = $conn->prepare(
            "INSERT INTO reviews (project_id, reviewer_id, reviewed_id, rating, comment)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt_review->execute([
            $project_id,
            $client_id,     // (العميل هو من يقيّم)
            $freelancer_id, // (الطالب هو من يتم تقييمه)
            $rating,
            $comment
        ]);
        
        // (خطوة 3: في المستقبل، هنا يتم "تحرير المال" 💰)

        // 4. (تم!) أكّد العملية
        $conn->commit();

        // 5. أعد توجيه العميل إلى نفس الصفحة مع رسالة نجاح
        header("Location: manage-project.php?id=" . $project_id . "&status=completed");
        exit();

    } catch (Exception $e) {
        // (حدث خطأ!) ألغِ العملية
        $conn->rollBack();
        die("حدث خطأ فادح أثناء إكمال المشروع: " . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: dashboard-client.php");
    exit();
}
?>