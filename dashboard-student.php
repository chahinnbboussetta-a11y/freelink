<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - "النسخة" النظيفة 🚀) ---
if (!$current_user_id || $user_role != 'freelancer') {
    header("Location: login.html"); // (اطرده 😈)
    exit();
}
// (نستخدم $current_user_id بدلاً من $student_id)
$student_id = $current_user_id; 
// ($conn و $user_name جاهزون 🚀 من config.php)

// --- (الخطوة 3: "العقل" 🧠 "الحقيقي" يبدأ هنا) ---

// 3. (الـ JOIN الوحش 🚀) جلب بروفايل الطالب
$stmt_profile = $conn->prepare(
    "SELECT u.name, fp.* FROM users u
     LEFT JOIN freelancer_profiles fp ON u.id = fp.user_id
     WHERE u.id = ?"
);
$stmt_profile->execute([$student_id]);
$profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);

// 4. (الحساب 🧮) حساب النسبة المئوية 💯
$profile_score = 0;
if ($profile) { // (تأمين 🛡️: تأكد أن البروفايل موجود)
    if (!empty($profile['profile_picture']) && $profile['profile_picture'] != 'images/default-avatar.png') $profile_score += 25;
    if (!empty($profile['headline']) && $profile['headline'] != 'Nouveau talent sur FreeLink') $profile_score += 25;
    if (!empty($profile['bio']) && $profile['bio'] != 'Mettez à jour votre biographie...') $profile_score += 25;
}
// (جلب المهارات لحساب آخر 25%)
$stmt_skills_count = $conn->prepare("SELECT COUNT(skill_id) as count FROM student_skill WHERE user_id = ?");
$stmt_skills_count->execute([$student_id]);
if ($stmt_skills_count->fetch(PDO::FETCH_ASSOC)['count'] > 0) $profile_score += 25;


// 5. (الحساب 🧮) حساب الإحصائيات 📊
// (أ. العروض المقدمة)
$stmt_proposals = $conn->prepare("SELECT COUNT(id) as count FROM proposals WHERE freelancer_id = ? AND status = 'pending'");
$stmt_proposals->execute([$student_id]);
$proposals_count = $stmt_proposals->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

// (ب. المشاريع قيد التنفيذ)
$stmt_inprogress = $conn->prepare("SELECT COUNT(id) as count FROM proposals WHERE freelancer_id = ? AND status = 'accepted'");
$stmt_inprogress->execute([$student_id]);
$inprogress_count = $stmt_inprogress->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

// (ج. الأرباح 💰)
$total_earnings = "0.00"; // (مؤقت 😈)

// 6. (الـ JOIN الأسطوري 😈) جلب "المشاريع المقترحة"
$stmt_recommended = $conn->prepare(
    "SELECT DISTINCT p.* FROM projects p
     JOIN project_skill ps ON p.id = ps.project_id
     JOIN student_skill ss ON ps.skill_id = ss.skill_id
     WHERE p.status = 'open'
     AND ss.user_id = ?
     ORDER BY p.created_at DESC
     LIMIT 3"
);
$stmt_recommended->execute([$student_id]);
$recommended_projects = $stmt_recommended->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de bord Étudiant - FreeLink</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <a href="dashboard-student.php" class="logo">FreeLink</a>
        <ul class="nav-links">
          <li><a href="dashboard-student.php">Tableau de bord</a></li>
          <li><a href="explore-projects.php">Explorer les projets</a></li>
          <li><a href="messages.php">Messages</a></li>
          <li><a href="profile-edit.php" class="nav-link-login">Mon Profil</a></li>
          <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
        </ul>
      </div>
    </nav>

    <main class="dashboard-page">
      <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'work_submitted'): ?>
            <div class="success-message">Succès ! Votre travail a été soumis au client pour révision.</div>
        <?php endif; ?>

        <h1 class="dashboard-title">Bienvenue, <?php echo htmlspecialchars($user_name); ?> !</h1>

        <section class="dashboard-section profile-completion">
          <div class="completion-info">
            <h2 class="section-title-sm">Complétez votre profil</h2>
            <p>
              Votre profil est complet à <strong><?php echo $profile_score; ?>%</strong>. Un profil complet
              attire plus de clients !
            </p>
            <a href="profile-edit.php" class="btn btn-primary-solid btn-sm"
              >Mettre à jour mon profil</a
            >
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: <?php echo $profile_score; ?>%"><?php echo $profile_score; ?>%</div>
          </div>
        </section>

        <section class="dashboard-section">
          <h2 class="section-title-sm">Aperçu des Activités</h2>
          <div class="overview-container">
            <div class="overview-card">
              <h3><?php echo $proposals_count; ?></h3>
              <p>Offres soumises</p>
            </div>
            <div class="overview-card">
              <h3><?php echo $inprogress_count; ?></h3>
              <p>Projets en cours</p>
            </div>
            <div class="overview-card">
              <h3><?php echo $total_earnings; ?> TND</h3>
              <p>Gains (Total)</p>
            </div>
          </div>
        </section>

        <section class="dashboard-section">
          <h2 class="section-title-sm">Nouveaux Projets (Pour vous)</h2>

          <div class="project-feed">
            <?php if (empty($recommended_projects)): ?>
                <p style="text-align: center; color: var(--light-text);">
                    Aucun projet ne correspond à vos compétences ⭐️ pour le moment.
                    <br>
                    <a href="profile-edit.php">Ajoutez plus de compétences</a> ou <a href="explore-projects.php">explorez tous les projets</a>.
                </p>
            <?php else: ?>
                <?php foreach ($recommended_projects as $project): ?>
                    <div class="project-feed-item">
                      <div class="item-details">
                        <h4><a href="project-details.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['title']); ?></a></h4>
                        <p class="item-meta">
                          Budget: <strong><?php echo htmlspecialchars($project['budget'] ?? 'N/A'); ?> TND</strong> - Publié le
                          <strong><?php echo date('d M Y', strtotime($project['created_at'])); ?></strong>
                        </p>
                        <p class="item-skills">
                          <span>Recommandé</span>
                        </p>
                      </div>
                      <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-primary-solid btn-sm">Voir Détails</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="feed-footer">
              <a href="explore-projects.php" class="btn btn-secondary"
                >Explorer tous les projets</a
              >
            </div>
          </div>
        </section>
      </div>
    </main>
  </body>
</html>