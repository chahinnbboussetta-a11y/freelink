<?php
// --- (الكود السري لإظهار الأخطاء) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ------------------------------------

// --- (حارس البوابة 😈) ---
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non authentifié']);
    exit();
}
$current_user_id = $_SESSION['user_id'];

// --- الخطوة 1: الاتصال ---
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion DB']);
    exit();
}

// (مهم 🚀: إخبار المتصفح أن هذا "JSON" وليس "HTML")
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? null;

try {
    switch ($action) {
        
        // --- الحالة 1: جلب المحادثات (النسخة النهائية 100% 🚀) ---
        case 'load_conversations':
            $stmt = $conn->prepare(
                "SELECT 
                    c.id as conversation_id,
                    u.id as other_user_id,
                    u.name as other_user_name,
                    
                    -- (تصحيح 1 😈: جلب صورة العميل أو الفريلانسر)
                    COALESCE(fp.profile_picture, cp.profile_picture, 'images/default-avatar.png') as other_user_pic,
                    
                    (SELECT body FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) as last_message,
                    c.updated_at as last_message_time 
                    
                 FROM conversations c
                 JOIN users u ON u.id = IF(c.user1_id = ?, c.user2_id, c.user1_id)
                 LEFT JOIN freelancer_profiles fp ON u.id = fp.user_id AND u.role = 'freelancer'
                 LEFT JOIN client_profiles cp ON u.id = cp.user_id AND u.role = 'client'
                 WHERE c.user1_id = ? OR c.user2_id = ?
                 
                 -- (LE VRAI CORRECTIF 🚀: Trier par la date de la conversation, c'est plus simple)
                 ORDER BY c.updated_at DESC
                "
            );
            $stmt->execute([$current_user_id, $current_user_id, $current_user_id]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // (إرسال الـ JSON 🚀)
            echo json_encode(['conversations' => $conversations]); 
            break;

        // --- الحالة 2: جلب الرسائل (النسخة المطورة 😈) ---
        case 'load_messages':
            $convo_id = $_GET['convo_id'] ?? 0;
            if (empty($convo_id)) throw new Exception('ID Conversation manquant');

            $check_stmt = $conn->prepare("SELECT * FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
            $check_stmt->execute([$convo_id, $current_user_id, $current_user_id]);
            $conversation = $check_stmt->fetch(PDO::FETCH_ASSOC);
            if (!$conversation) {
                throw new Exception('Accès non autorisé');
            }

            $stmt_msgs = $conn->prepare("SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC");
            $stmt_msgs->execute([$convo_id]);
            $messages = $stmt_msgs->fetchAll(PDO::FETCH_ASSOC);

            // (الكود الأسطوري 😈: جلب بيانات المشروع المرتبط)
            $other_user_id = ($conversation['user1_id'] == $current_user_id) ? $conversation['user2_id'] : $conversation['user1_id'];
            $stmt_project = $conn->prepare(
                "SELECT p.id, p.status, p.client_id
                 FROM projects p
                 JOIN proposals pr ON p.id = pr.project_id
                 WHERE pr.status = 'accepted' 
                 AND ( (p.client_id = ? AND pr.freelancer_id = ?) OR (p.client_id = ? AND pr.freelancer_id = ?) )
                 LIMIT 1"
            );
            $stmt_project->execute([$current_user_id, $other_user_id, $other_user_id, $current_user_id]);
            $project_data = $stmt_project->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'messages' => $messages,
                'project_data' => $project_data
            ]);
            break;

        // --- الحالة 3: إرسال رسالة (لا تغيير) ---
        case 'send_message':
            $data = json_decode(file_get_contents('php://input'), true);
            $convo_id = $data['convo_id'] ?? 0;
            $receiver_id = $data['receiver_id'] ?? 0;
            $message_body = trim($data['message_body'] ?? '');

            if (empty($message_body)) throw new Exception('Message vide');
            if (empty($convo_id) && empty($receiver_id)) throw new Exception('Destinataire manquant');

            $new_convo_id = null;
            if (empty($convo_id)) {
                $stmt_find = $conn->prepare("SELECT id FROM conversations WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
                $stmt_find->execute([$current_user_id, $receiver_id, $receiver_id, $current_user_id]);
                $existing_convo = $stmt_find->fetch();
                
                if ($existing_convo) {
                    $convo_id = $existing_convo['id'];
                } else {
                    $stmt_create = $conn->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)");
                    $stmt_create->execute([$current_user_id, $receiver_id]);
                    $convo_id = $conn->lastInsertId();
                    $new_convo_id = $convo_id; 
                }
            }
            
            $stmt_insert = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)");
            $stmt_insert->execute([$convo_id, $current_user_id, $message_body]);

            // (تحديث ⏱️ وقت المحادثة)
            $conn->prepare("UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$convo_id]);

            echo json_encode(['status' => 'success', 'message' => 'Message envoyé', 'new_convo_id' => $new_convo_id]);
            break;

        default:
            throw new Exception('Action non valide');
    }
} catch (Exception $e) {
    http_response_code(400);
    error_log("Chat API Error: " . $e->getMessage()); // Écrit l'erreur dans les logs du serveur
    echo json_encode(['error' => $e->getMessage()]); // Renvoie l'erreur au JS
}
?>
