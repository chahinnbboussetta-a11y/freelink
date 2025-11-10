<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - "النسخة" النظيفة 🚀) ---
// (نستخدم المتغيرات 🔑 من config.php)
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html"); // (اطرده 😈)
    exit();
}

// (لم نعد بحاجة لجلب $user_name، فهو موجود 🚀 في config.php)

// --- (الخطوة 3: "العقل" 🧠 "الحقيقي" يبدأ هنا) ---
// (نحن "نفترض" 😈 أن $conn موجود من config.php)

// 4. جلب "كل" مشاريع هذا العميل (JOIN احترافي 😈)
$stmt = $conn->prepare(
    "SELECT 
        p.*, 
        COUNT(pr.id) as proposal_count 
     FROM projects p
     LEFT JOIN proposals pr ON p.id = pr.project_id
     WHERE p.client_id = ?
     GROUP BY p.id
     ORDER BY p.created_at DESC"
);
$stmt->execute([$current_user_id]); // (استخدم $current_user_id 🚀)
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. (حساب الإحصائيات 🧮)
$projects_in_progress = 0;
$projects_open = 0;
$projects_completed = 0;
foreach ($projects as $project) {
    if ($project['status'] == 'in_progress') {
        $projects_in_progress++;
    } elseif ($project['status'] == 'open') {
        $projects_open++;
    } elseif ($project['status'] == 'completed') {
        $projects_completed++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de bord Client - FreeLink</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <a href="dashboard-client.php" class="logo">FreeLink</a>
        <ul class="nav-links">
          <li><a href="dashboard-client.php">Tableau de bord</a></li>
          <li><a href="messages.php">Messages</a></li>
          <li><a href="profile-client-edit.php" class="nav-link-login">Mon Profil</a></li> 
          <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
        </ul>
      </div>
    </nav>

    <main class="dashboard-page">
      <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'project_success'): ?>
            <div class="success-message">Succès ! Votre projet a été publié et est en attente de révision.</div>
        <?php endif; ?>

        <h1 class="dashboard-title">Bienvenue, <?php echo htmlspecialchars($user_name); ?> !</h1>
        
        <section class="dashboard-section">
          <h2 class="section-title-sm">Aperçu des Projets</h2>
          <div class="overview-container">
           <div class="overview-card">
             <h3><?php echo $projects_in_progress; ?></h3> <p>Projets en cours</p>
             </div>
             <div class="overview-card">
           <h3><?php echo $projects_open; ?></h3> <p>Projets en attente</p>
           </div>
           <div class="overview-card">
           <h3><?php echo $projects_completed; ?></h3> <p>Projets terminés</p>
           </div>
          </div>
        </section>

        <section class="dashboard-section">
          <h2 class="section-title-sm">Raccourcis Rapides</h2>
          <div class="quick-links">
            <a href="publish-project.php" class="btn btn-primary-solid"
              >Publier un nouveau projet</a
            >
            <a href="messages.php" class="btn btn-secondary"
              >Voir tous les messages</a
            >
            <a href="#" class="btn btn-secondary"
              >Gérer les paiements</a
            >
          </div>
        </section>

        <section class="dashboard-section">
          <h2 class="section-title-sm">Mes Projets Récents</h2>
          <div class="table-container">
            <table class="dashboard-table">
              <thead>
                <tr>
                  <th>Titre du projet</th>
                  <th>Statut</th>
                  <th>Offres reçues</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($projects)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--light-text);">Vous n'avez encore publié aucun projet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <a href="manage-project.php?id=<?php echo $project['id']; ?>" style="font-weight: 700;">
                                    <?php echo htmlspecialchars($project['title']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($project['status']); ?>">
                                    <?php echo htmlspecialchars($project['status']); ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo $project['proposal_count']; ?></strong>
                            </td>
                            <td>
                                <a href="manage-project.php?id=<?php echo $project['id']; ?>" class="btn btn-primary-solid btn-sm">
                                    Gérer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </body>
</html>