<?php
// --- (حارس البوابة المطور 😈) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. التحقق من الجلسة (الحارس)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $_SESSION['user_id'];

// 2. (الأهم) احصل على ID المشروع من العنوان (URL)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("خطأ: لم يتم تحديد المشروع!");
}
$project_id = $_GET['id'];

// 3. الاتصال بقاعدة البيانات
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// 4. (تأمين أسطوري 😈) جلب المشروع والتأكد من أنه ملك لهذا العميل
$stmt_project = $conn->prepare("SELECT * FROM projects WHERE id = ? AND client_id = ?");
$stmt_project->execute([$project_id, $client_id]);
$project = $stmt_project->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("خطأ: هذا المشروع غير موجود أو لا تملكه.");
}

// --- (المنطق الأسطوري الجديد 😈) ---
// (سنجلب بيانات مختلفة بناءً على حالة المشروع)

$pending_proposals = []; // (العروض المعلقة)
$accepted_proposal = null; // (العرض المقبول)

if ($project['status'] == 'open') {
    // 5أ. إذا كان المشروع "مفتوحاً"، اجلب العروض "المعلقة"
    $stmt_pending = $conn->prepare(
        "SELECT pr.*, u.name as freelancer_name, fp.headline as freelancer_headline, fp.profile_picture as freelancer_pic
         FROM proposals pr
         JOIN users u ON pr.freelancer_id = u.id
         JOIN freelancer_profiles fp ON u.id = fp.user_id
         WHERE pr.project_id = ? AND pr.status = 'pending'"
    );
    $stmt_pending->execute([$project_id]);
    $pending_proposals = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);

} elseif ($project['status'] == 'in_progress' || $project['status'] == 'in_review') {
    // 5ب. إذا كان "قيد التنفيذ" أو "قيد المراجعة"، اجلب العرض "المقبول"
    $stmt_accepted = $conn->prepare(
        "SELECT pr.*, u.name as freelancer_name, fp.headline as freelancer_headline, fp.profile_picture as freelancer_pic
         FROM proposals pr
         JOIN users u ON pr.freelancer_id = u.id
         JOIN freelancer_profiles fp ON u.id = fp.user_id
         WHERE pr.project_id = ? AND pr.status = 'accepted'
         LIMIT 1" // (يوجد عرض واحد مقبول فقط)
    );
    $stmt_accepted->execute([$project_id]);
    $accepted_proposal = $stmt_accepted->fetch(PDO::FETCH_ASSOC);
}
// (إذا كان 'completed'، لن نجلب أي عروض)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer le Projet - FreeLink</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="dashboard-client.php" class="logo">FreeLink</a>
            <ul class="nav-links">
                <li><a href="dashboard-client.php">Tableau de bord</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="#" class="nav-link-login"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
                <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li> 
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            
            <div class="project-summary-header">
                <h1 class="dashboard-title">Gérer le projet : "<?php echo htmlspecialchars($project['title']); ?>"</h1>
                <p>État actuel du projet: <span class="status status-<?php echo htmlspecialchars($project['status']); ?>"><?php echo htmlspecialchars($project['status']); ?></span></p>
                
                <?php if (isset($_GET['status']) && $_GET['status'] == 'accepted'): ?>
                    <div class="success-message">Succès ! Vous avez accepté cette offre. Le projet est maintenant 'en cours'.</div>
                <?php endif; ?>
            </div>

            <?php if ($project['status'] == 'open'): ?>
                <h2 class="section-title-sm">Offres en attente (Pending)</h2>
                <div class="proposals-list-container">
                    <?php if (empty($pending_proposals)): ?>
                        <div class="proposal-card-item" style="text-align: center; color: var(--light-text);">Vous n'avez encore reçu aucune offre.</div>
                    <?php else: ?>
                        <?php foreach ($pending_proposals as $proposal): ?>
                            <div class="proposal-card-item">
                                <div class="proposal-header">
                                    <img src="<?php echo htmlspecialchars($proposal['freelancer_pic'] ?? 'images/default-avatar.png'); ?>" alt="Avatar" class="proposal-avatar">
                                    <div class="proposal-freelancer-info">
                                    <h4>
                                        <a href="profile.php?id=<?php echo $proposal['freelancer_id']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($proposal['freelancer_name']); ?>
                                        </a>
                                    </h4>
                                    <p><?php echo htmlspecialchars($proposal['freelancer_headline'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="proposal-details">
                                    <div class="detail-item"><span>Offre</span><strong><?php echo htmlspecialchars($proposal['amount']); ?> TND</strong></div>
                                    <div class="detail-item"><span>Durée</span><strong><?php echo htmlspecialchars($proposal['duration']); ?> Jours</strong></div>
                                </div>
                                <div class="proposal-body">
                                    <p><?php echo nl2br(htmlspecialchars($proposal['cover_letter'])); ?></p>
                                    <?php if ($proposal['attachment_path']): ?>
                                        <a href="<?php echo htmlspecialchars($proposal['attachment_path']); ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-paperclip"></i> Voir Fichier Joint</a>
                                    <?php endif; ?>
                                </div>
                                <div class="proposal-actions">
                                    <form action="accept_proposal.php" method="POST">
                                        <input type="hidden" name="proposal_id" value="<?php echo $proposal['id']; ?>">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" class="btn btn-primary-solid"><i class="fas fa-check"></i> Accepter l'offre</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            
            <?php elseif ($project['status'] == 'in_progress' || $project['status'] == 'in_review'): ?>
                
                <?php if ($accepted_proposal): ?>
                    <h2 class="section-title-sm">Freelance accepté pour ce projet</h2>
                    <div class="proposals-list-container" style="grid-template-columns: 1fr;"> <div class="proposal-card-item">
                            <div class="proposal-header">
                                <img src="<?php echo htmlspecialchars($accepted_proposal['freelancer_pic'] ?? 'images/default-avatar.png'); ?>" alt="Avatar" class="proposal-avatar">
                                <div class="proposal-freelancer-info">
                                    <h4><?php echo htmlspecialchars($accepted_proposal['freelancer_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($accepted_proposal['freelancer_headline'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="proposal-details">
                                <div class="detail-item"><span>Offre Acceptée</span><strong><?php echo htmlspecialchars($accepted_proposal['amount']); ?> TND</strong></div>
                                <div class="detail-item"><span>Durée</span><strong><?php echo htmlspecialchars($accepted_proposal['duration']); ?> Jours</strong></div>
                            </div>
                            <div class="proposal-actions">
                                <a href="messages.php" class="btn btn-secondary"><i class="fas fa-comments"></i> Contacter le freelance</a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($project['status'] == 'in_review'): ?>
                        <div class="review-form-container">
                            <h2 class="section-title-sm">Le freelance a soumis son travail !</h2>
                            <p class="section-subtitle">Veuillez examiner le travail. Si tout est correct, acceptez le paiement et laissez un avis.</p>
                            
                            <form action="complete_project_process.php" method="POST" class="auth-form" style="max-width: 100%;">
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                <input type="hidden" name="freelancer_id" value="<?php echo $accepted_proposal['freelancer_id']; ?>">
                                
                                <div class="form-group">
                                    <label>Évaluation (Note sur 5)</label>
                                    <div class="rating-stars">
                                        <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
                                        <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                                        <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                                        <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                                        <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="comment">Commentaire public</label>
                                    <textarea id="comment" name="comment" rows="5" placeholder="Laissez un avis sur votre expérience avec ce freelance..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary-solid btn-full">
                                        <i class="fas fa-check-circle"></i> Accepter le paiement et terminer le projet
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?> <?php endif; ?> <?php elseif ($project['status'] == 'completed'): ?>
                <h2 class="section-title-sm">Projet Terminé</h2>
                <div class="success-message">Ce projet a été complété avec succès.</div>
                <?php endif; ?>

        </div>
    </main>
</body>
</html>