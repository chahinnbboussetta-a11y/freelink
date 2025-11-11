<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - نسخة العميل 💼) ---
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $current_user_id; 
// ($conn جاهز 🚀)

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
    
    // --- الخطوة 4: (المستوى الأخير 😈) - "المال" 💰 + "التقييم" ⭐️ ---
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
        
        // --- (الكود الأسطوري الجديد 😈: "تحرير" 💸 "المال" 💰) ---
        
        // 3. (أ. "اصطياد" 🕵️‍♂️ "العملية" 💰)
        $stmt_trans = $conn->prepare(
            "SELECT id, amount FROM transactions 
             WHERE project_id = ? AND status = 'paid'" // (ابحث 🕵️‍♂️ عن "المال" 💰 "المدفوع")
        );
        $stmt_trans->execute([$project_id]);
        $transaction = $stmt_trans->fetch(PDO::FETCH_ASSOC);

        if ($transaction) {
            $amount_to_release = $transaction['amount'];
            $transaction_id = $transaction['id'];

            // 3. (ب. "حرر" 💸 "العملية" 💰)
            $stmt_release_trans = $conn->prepare("UPDATE transactions SET status = 'released' WHERE id = ?");
            $stmt_release_trans->execute([$transaction_id]);

            // 3. (ج. "أضف" 😈 "المال" 💰 إلى "محفظة" 🤑 الطالب 👨‍🎓)
            $stmt_update_wallet = $conn->prepare(
                "UPDATE freelancer_profiles 
                 SET wallet_balance = wallet_balance + ? 
                 WHERE user_id = ?"
            );
            $stmt_update_wallet->execute([$amount_to_release, $freelancer_id]);
        }
        // --- (نهاية "الكود الأسطوري" 😈) ---

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
