<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once '../config.php'; // (استخدم ".." 😈)

// --- (الخطوة 2: "الحارس" 🛡️ الأسطوري 😈) ---
if (!$current_user_id || $user_role != 'admin') {
    header("Location: ../login.html");
    exit();
}
// ($conn جاهز 🚀)

// --- الخطوة 3: "العقل" 🧠 (تنفيذ الأوامر 😈) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // (استلام "الأمر" 😈)
    $action = $_POST['action'] ?? null;

    try {
        // --- (الـ "سويتش" 😈 الأسطوري) ---
        switch ($action) {

            // --- (الأمر 1: الموافقة على المشروع 👍) ---
            case 'approve':
                $project_id = $_POST['project_id'];
                if (empty($project_id)) die("ID Projet manquant.");
                
                // (تأكد 🛡️ أنه "معلق")
                $stmt_check = $conn->prepare("SELECT id FROM projects WHERE id = ? AND status = 'pending_approval'");
                $stmt_check->execute([$project_id]);
                if (!$stmt_check->fetch()) die("Projet non trouvé ou déjà traité.");

                // (نفذ 🚀)
                $stmt_action = $conn->prepare("UPDATE projects SET status = 'open' WHERE id = ?");
                $stmt_action->execute([$project_id]);
                
                header("Location: dashboard.php?status=approved");
                exit();

            // --- (الأمر 2: رفض المشروع 👎) ---
            case 'reject':
                $project_id = $_POST['project_id'];
                if (empty($project_id)) die("ID Projet manquant.");
                
                // (تأكد 🛡️ أنه "معلق")
                $stmt_check = $conn->prepare("SELECT id FROM projects WHERE id = ? AND status = 'pending_approval'");
                $stmt_check->execute([$project_id]);
                if (!$stmt_check->fetch()) die("Projet non trouvé ou déjà traité.");
                
                // (نفذ 🚀 - احذفه 💣)
                $stmt_action = $conn->prepare("DELETE FROM projects WHERE id = ?");
                $stmt_action->execute([$project_id]);

                header("Location: dashboard.php?status=rejected");
                exit();

            // --- (الكود الأسطوري الجديد 😈: إضافة مهارة ⭐️) ---
            case 'add_skill':
                $skill_name = trim(ucwords(strtolower($_POST['skill_name']))); // (تنظيف 🧹 احترافي)
                if (empty($skill_name)) die("Nom de compétence vide.");

                // (تأكد 🛡️ أنها غير موجودة)
                $stmt_check = $conn->prepare("SELECT id FROM skills WHERE name = ?");
                $stmt_check->execute([$skill_name]);
                if ($stmt_check->fetch()) {
                    header("Location: manage_skills.php?error=Skill existe déjà");
                    exit();
                }
                
                // (نفذ 🚀 - أضفها 🤓)
                $stmt_add = $conn->prepare("INSERT INTO skills (name) VALUES (?)");
                $stmt_add->execute([$skill_name]);

                header("Location: manage_skills.php?status=skill_added");
                exit();

            // --- (الكود الأسطوري الجديد 😈: حذف مهارة 💣) ---
            case 'delete_skill':
                $skill_id = $_POST['skill_id'];
                if (empty($skill_id)) die("ID Compétence manquant.");

                // (ملاحظة: "البيس" 💾 ستقوم بـ "ON DELETE CASCADE" 🌪️)
                // (هذا سيحذف 💣 المهارة من جدول `skills`، `project_skill`، و `student_skill` تلقائياً!)
                
                // (نفذ 🚀 - احذفها 💣)
                $stmt_delete = $conn->prepare("DELETE FROM skills WHERE id = ?");
                $stmt_delete->execute([$skill_id]);
                
                header("Location: manage_skills.php?status=skill_deleted");
                exit();
                
            default:
                die("Action non valide.");
        }

    } catch (Exception $e) {
        // (في حال فشل "الحذف" 💣 بسبب "ON DELETE CASCADE" 🛡️)
        if (str_contains($e->getMessage(), 'foreign key constraint')) {
            header("Location: manage_skills.php?error=Impossible de supprimer: La compétence est utilisée par des étudiants ou des projets.");
            exit();
        }
        die("حدث خطأ فادح: " . $e->getMessage());
    }

} else {
    // إذا حاول شخص فتح الملف مباشرة
    header("Location: dashboard.php");
    exit();
}
?>