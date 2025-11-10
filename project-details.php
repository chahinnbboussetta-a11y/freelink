<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - "النسخة" النظيفة 🚀) ---
if (!$current_user_id || $user_role != 'freelancer') {
    header("Location: login.html"); // (اطرده 😈)
    exit();
}
$student_id = $current_user_id; // (إعادة تسمية لسهولة القراءة 😈)
// ($conn جاهز 🚀 من config.php)

// --- (الخطوة 3: "العقل" 🧠 "الحقيقي" يبدأ هنا) ---

// 3. (جلب 🕵️‍♂️ ID المشروع من العنوان)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("خطأ: لم يتم تحديد المشروع!");
}
$project_id = $_GET['id'];

// 4. (الـ JOIN الوحش 🚀: جلب المشروع + اسم العميل 💼)
$stmt_project = $conn->prepare(
    "SELECT p.*, u.name as client_name 
     FROM projects p
     JOIN users u ON p.client_id = u.id
     WHERE p.id = ? AND p.status = 'open'" // (فقط المشاريع المفتوحة 😈)
);
$stmt_project->execute([$project_id]);
$project = $stmt_project->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("خطأ: المشروع غير موجود أو لم يعد مفتوحاً.");
}

// 5. (الـ JOIN الأسطوري 😈: جلب المهارات ⭐️)
$stmt_skills = $conn->prepare(
    "SELECT s.name 
     FROM skills s
     JOIN project_skill ps ON s.id = ps.skill_id
     WHERE ps.project_id = ?"
);
$stmt_skills->execute([$project_id]);
$skills = $stmt_skills->fetchAll(PDO::FETCH_COLUMN); // (جلب أسماء المهارات ⭐️)

// 6. (الـ JOIN الأسطوري 😈: جلب العروض 📊)
// (جلب كل الـ freelancer_ids الذين قدموا عروضاً)
$stmt_proposals = $conn->prepare("SELECT freelancer_id FROM proposals WHERE project_id = ?");
$stmt_proposals->execute([$project_id]);
$proposals = $stmt_proposals->fetchAll(PDO::FETCH_COLUMN);
$proposal_count = count($proposals); // (حساب 📊 العدد)

// (هل أنا 👨‍🎓 قدمت عرضاً بالفعل؟)
$has_already_proposed = false;
if (in_array($student_id, $proposals)) {
    $has_already_proposed = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails: <?php echo htmlspecialchars($project['title']); ?> - FreeLink</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            
            <div class="project-details-container">
                
                <section class="project-details-content">
                    <div class="project-full-details">
                        <span class="card-date">Publié le: <?php echo date('d M Y', strtotime($project['created_at'])); ?></span>
                        <h1 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h1>
                        
                        <div class="card-skills" style="margin-bottom: 20px;">
                            <?php if (empty($skills)): ?>
                                <span>Aucune compétence listée</span>
                            <?php else: ?>
                                <?php foreach ($skills as $skill): ?>
                                    <span><?php echo htmlspecialchars($skill); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="details-section-title">Description du Projet</h3>
                        <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                        
                        <?php if (!empty($project['additional_notes'])): ?>
                            <h3 class="details-section-title">Instructions supplémentaires 📝</h3>
                            <p><?php echo nl2br(htmlspecialchars($project['additional_notes'])); ?></p>
                        <?php endif; ?>

                        <?php if ($project['file_path']): ?>
                            <h3 class="details-section-title">Fichiers Attachés</h3>
                            <p><a href="<?php echo htmlspecialchars($project['file_path']); ?>" target="_blank" class="btn btn-secondary btn-sm">Télécharger le fichier joint</a></p>
                        <?php endif; ?>

                        <h3 class="details-section-title">À propos du Client</h3>
                        <p><strong><a href="profile.php?id=<?php echo $project['client_id']; ?>" target="_blank"><?php echo htmlspecialchars($project['client_name']); ?></a></strong></p>
                    </div>
                </section>

                <aside class="project-proposal-sidebar">
                    <div class="proposal-card">

                        <?php if ($has_already_proposed): ?>
                            <h3 class="proposal-title" style="color: var(--primary-color);">Vous avez déjà postulé</h3>
                            <p style="text-align: center; color: var(--light-text); line-height: 1.6;">
                                Votre proposition est en attente d'examen par le client.
                            </p>
                            
                        <?php else: ?>
                            <h3 class="proposal-title">Soumettre ma proposition</h3>
                            <p class="project-budget-aside">
                                Budget: <strong><?php echo htmlspecialchars($project['budget'] ?? 'N/A'); ?> TND</strong>
                                <span style="display: block; font-size: 14px; margin-top: 5px; color: var(--light-text);"><?php echo $proposal_count; ?> Offre(s) déjà reçue(s)</span>
                            </p>

                            <form action="proposal_process.php" method="POST" enctype="multipart/form-data" class="auth-form">
                                
                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">

                                <div class="form-group">
                                    <label for="amount">Votre offre (en TND)</label>
                                    <input type="number" id="amount" name="amount" placeholder="Votre montant" required>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Durée estimée (en jours)</label>
                                    <input type="number" id="duration" name="duration" placeholder="Ex: 7" required>
                                </div>
                                <div class="form-group">
                                    <label for="cover_letter">Lettre de motivation</label>
                                    <textarea id="cover_letter" name="cover_letter" rows="6" placeholder="Pourquoi êtes-vous le meilleur pour ce projet ?" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="file">Joindre des fichiers (Optionnel)</label>
                                    <input type="file" id="file" name="file" class="input-file"> </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary-solid btn-full">Soumettre</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                    </div>
                </aside>
            </div>
        </div>
    </main>
    
    <script> const CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>; </script>
    <script src="script.js"></script> 
</body>
</html>