<?php
// --- (حارس البوابة "الخفيف" 😈) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. (الأهم) احصل على ID المستخدم من العنوان (URL)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("خطأ: لم يتم تحديد بروفايل المستخدم!");
}
$user_id = $_GET['id'];

// 2. الاتصال
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// 3. (الـ JOIN الوحش 🚀) جلب بيانات المستخدم الأساسية + "الدور" (Role)
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("خطأ: هذا المستخدم غير موجود.");
}

// 4. (المنطق الأسطوري 😈: "التحقق من الدور")
$user_role = $user['role'];

$profile_data = null;
$reviews = [];
$skills = []; // (سنملأ هذه ⭐️)
$avg_rating = 0;
$review_count = 0;
$completed_count = 0;
$open_projects = []; 


if ($user_role == 'freelancer') {
    // --- (المنطق 1: إذا كان "طالباً" 👨‍🎓) ---
    
    // 3أ. جلب بروفايل الطالب
    $stmt_profile = $conn->prepare("SELECT * FROM freelancer_profiles WHERE user_id = ?");
    $stmt_profile->execute([$user_id]);
    $profile_data = $stmt_profile->fetch(PDO::FETCH_ASSOC);

    // 4أ. جلب تقييمات الطالب
    $stmt_reviews = $conn->prepare(
        "SELECT r.*, u.name as client_name 
         FROM reviews r JOIN users u ON r.reviewer_id = u.id
         WHERE r.reviewed_id = ? ORDER BY r.created_at DESC"
    );
    $stmt_reviews->execute([$user_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

    // 5أ. حساب التقييم 🧮
    $review_count = count($reviews);
    if ($review_count > 0) {
        $total_rating = 0;
        foreach ($reviews as $review) { $total_rating += $review['rating']; }
        $avg_rating = round($total_rating / $review_count, 1);
    }

    // 6أ. حساب المشاريع المكتملة 🏁
    $stmt_completed = $conn->prepare(
        "SELECT COUNT(p.id) as completed_count FROM projects p
         JOIN proposals pr ON p.id = pr.project_id
         WHERE pr.freelancer_id = ? AND p.status = 'completed' AND pr.status = 'accepted'"
    );
    $stmt_completed->execute([$user_id]);
    $completed_count = $stmt_completed->fetch(PDO::FETCH_ASSOC)['completed_count'] ?? 0;

    // 7أ. (الكود الأسطوري 😈) جلب المهارات ⭐️
    $stmt_skills = $conn->prepare(
        "SELECT s.name 
         FROM skills s
         JOIN student_skill ss ON s.id = ss.skill_id
         WHERE ss.user_id = ?"
    );
    $stmt_skills->execute([$user_id]);
    $skills = $stmt_skills->fetchAll(PDO::FETCH_COLUMN); // (جلب أسماء المهارات فقط)

} elseif ($user_role == 'client') {
    // --- (المنطق 2: إذا كان "عميلاً" 💼) ---
    
    // 3ب. جلب بروفايل العميل
    $stmt_profile = $conn->prepare("SELECT * FROM client_profiles WHERE user_id = ?");
    $stmt_profile->execute([$user_id]);
    $profile_data = $stmt_profile->fetch(PDO::FETCH_ASSOC);

    // 4ب. جلب تقييمات العميل (كم عدد التقييمات التي "تركها")
    $stmt_reviews = $conn->prepare("SELECT * FROM reviews WHERE reviewer_id = ?");
    $stmt_reviews->execute([$user_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
    $review_count = count($reviews); 
    
    // 5ب. جلب المشاريع "المفتوحة" 🚀 لهذا العميل
    $stmt_open_projects = $conn->prepare(
        "SELECT * FROM projects WHERE client_id = ? AND status = 'open'
         ORDER BY created_at DESC"
    );
    $stmt_open_projects->execute([$user_id]);
    $open_projects = $stmt_open_projects->fetchAll(PDO::FETCH_ASSOC);
    $completed_count = count($open_projects); // (عرض المشاريع المفتوحة)
}

if (!$profile_data) {
    $profile_data = []; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?php echo htmlspecialchars($user['name']); ?> - FreeLink</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">FreeLink</a>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): // (إذا كان مسجلاً) ?>
                    <li><a href="<?php echo ($_SESSION['user_role'] == 'client') ? 'dashboard-client.php' : 'dashboard-student.php'; ?>">Tableau de bord</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
                <?php else: // (إذا كان زائراً) ?>
                    <li><a href="login.html">Se connecter</a></li>
                    <li><a href="signup.php" class="btn btn-primary">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            
            <div class="profile-header-card">
                <div class="profile-avatar-container">
                    <img src="<?php echo htmlspecialchars($profile_data['profile_picture'] ?? 'images/default-avatar.png'); ?>" alt="Avatar" class="profile-avatar-large">
                </div>
                <div class="profile-info-main">
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                    
                    <?php if ($user_role == 'freelancer'): ?>
                        <p class="profile-headline"><?php echo htmlspecialchars($profile_data['headline'] ?? 'Nouveau talent sur FreeLink'); ?></p>
                    <?php else: ?>
                        <p class="profile-headline"><?php echo htmlspecialchars($profile_data['company_name'] ?? 'Client sur FreeLink'); ?></p>
                    <?php endif; ?>
                    
                    <div class="profile-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Tunisie</span>
                        <span><i class="fas fa-clock"></i> Membre depuis <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
                <div class="profile-stats-summary">
                    <?php if ($user_role == 'freelancer'): ?>
                        <div class="stat-item">
                            <strong><?php echo $avg_rating; ?></strong>
                            <span>⭐️ (<?php echo $review_count; ?> Avis)</span>
                        </div>
                        <div class="stat-item">
                            <strong><?php echo $completed_count; ?></strong>
                            <span>Projets terminés</span>
                        </div>
                    <?php else: ?>
                        <div class="stat-item">
                            <strong><?php echo $review_count; ?></strong>
                            <span>Avis laissés</span>
                        </div>
                        <div class="stat-item">
                            <strong><?php echo $completed_count; ?></strong>
                            <span>Projets ouverts</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-body-container">
                <div class="profile-sidebar">
                    <div class="profile-widget">
                        <h3><?php echo ($user_role == 'freelancer') ? 'À propos de moi' : 'À propos du client'; ?></h3>
                        <p class="profile-bio">
                            <?php echo nl2br(htmlspecialchars($profile_data['bio'] ?? 'Aucune biographie fournie.')); ?>
                        </p>
                    </div>
                    
                    <?php if ($user_role == 'freelancer'): ?>
                        <div class="profile-widget">
                            <h3 class="widget-title">Compétences ⭐️</h3>
                            <div class="profile-skills-list"> <?php if (empty($skills)): ?>
                                    <span style="font-size: 14px; color: var(--light-text);">Aucune compétence ajoutée.</span>
                                <?php else: ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="skill-pill"><?php echo htmlspecialchars($skill); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: // (إذا كان عميلاً 💼) ?>
                        <div class="profile-widget">
                            <h3 class="widget-title">Informations</h3>
                            <p class="profile-bio">
                                <strong>Site Web:</strong> 
                                <?php if (!empty($profile_data['website'])): ?>
                                    <a href="<?php echo htmlspecialchars($profile_data['website']); ?>" target="_blank"><?php echo htmlspecialchars($profile_data['website']); ?></a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="profile-main-content">
                    
                    <?php if ($user_role == 'freelancer'): ?>
                        <div class="profile-widget">
                            <h3 class="widget-title">Avis & Évaluations (<?php echo $review_count; ?>)</h3>
                            <?php if (empty($reviews)): ?>
                                <p style="color: var(--light-text);">Ce freelance n'a encore reçu aucun avis.</p>
                            <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-card">
                                        <div class="review-header">
                                            <span class="review-client-name"><?php echo htmlspecialchars($review['client_name']); ?></span>
                                            <span class="review-rating"><?php echo str_repeat('⭐️', $review['rating']); ?></span>
                                        </div>
                                        <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                        <span class="review-date">Publié le <?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: // (إذا كان عميلاً 💼) ?>
                        <div class="profile-widget">
                            <h3 class="widget-title">Projets Ouverts (<?php echo $completed_count; ?>)</h3>
                            <?php if (empty($open_projects)): ?>
                                <p style="color: var(--light-text);">Ce client n'a aucun projet ouvert actuellement.</p>
                            <?php else: ?>
                                <div class="project-feed">
                                    <?php foreach ($open_projects as $project): ?>
                                        <div class="project-feed-item">
                                            <div class="item-details">
                                                <h4><a href="project-details.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a></h4>
                                                <p class="item-meta">Budget: <strong><?php echo htmlspecialchars($project['budget'] ?? 'N/A'); ?> TND</strong></p>
                                            </div>
                                            <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-primary-solid btn-sm">Postuler</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </main>
</body>
</html>