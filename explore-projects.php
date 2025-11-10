<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - "النسخة" النظيفة 🚀) ---
if (!$current_user_id || $user_role != 'freelancer') {
    header("Location: login.html"); // (اطرده 😈)
    exit();
}
$student_id = $current_user_id; // (استخدم $current_user_id 🚀)
// ($conn و $user_name جاهزون 🚀 من config.php)

// --- (الخطوة 3: "العقل" 🧠 "الحقيقي" يبدأ هنا) ---

// 3. (جلب 📚 "كل" المهارات ⭐️ للفلتر)
// (نحن "نفترض" 😈 أن $conn موجود)
$stmt_all_skills = $conn->prepare("SELECT * FROM skills ORDER BY name ASC");
$stmt_all_skills->execute();
$all_skills = $stmt_all_skills->fetchAll(PDO::FETCH_ASSOC);


// --- (العقل الأسطوري 😈: بناء SQL "الديناميكي") ---
// 4. (قراءة 📖 الفلاتر من $_GET)
$keyword = $_GET['keyword'] ?? ''; 
$skills_filter_ids = $_GET['skills'] ?? []; 
$min_budget = $_GET['min_budget'] ?? null; 
$max_budget = $_GET['max_budget'] ?? null;

// 5. (بناء "الوحش" 😈 SQL)
$sql = "SELECT DISTINCT p.*, u.name as client_name 
        FROM projects p
        JOIN users u ON p.client_id = u.id
        LEFT JOIN project_skill ps ON p.id = ps.project_id 
        WHERE p.status = 'open'"; 
$params = []; 

if (!empty($keyword)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if (!empty($min_budget)) {
    $sql .= " AND p.budget >= ?";
    $params[] = $min_budget;
}
if (!empty($max_budget)) {
    $sql .= " AND p.budget <= ?";
    $params[] = $max_budget;
}
if (!empty($skills_filter_ids) && is_array($skills_filter_ids)) {
    $safe_skill_ids = array_filter($skills_filter_ids, 'is_numeric');
    if (!empty($safe_skill_ids)) {
        $placeholders = implode(',', array_fill(0, count($safe_skill_ids), '?'));
        $sql .= " AND ps.skill_id IN ($placeholders)";
        $params = array_merge($params, $safe_skill_ids); 
    }
}
$sql .= " ORDER BY p.created_at DESC";

// 6. (تنفيذ "الوحش" 😈)
$stmt_projects = $conn->prepare($sql);
$stmt_projects->execute($params);
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);
// --- (نهاية "العقل" 🧠) ---
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorer les Projets - FreeLink</title>
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

    <main class="explore-page">
        <div class="container">
            
            <div class="explore-header">
                <h1>Explorer les Projets</h1>
                <form action="explore-projects.php" method="GET" class="search-bar">
                    <input type="text" name="keyword" placeholder="Rechercher par mot-clé..." value="<?php echo htmlspecialchars($keyword); ?>">
                    <button type="submit" class="btn btn-primary-solid">Rechercher</button>
                </form>
            </div>

            <div class="explore-container">
                
                <form action="explore-projects.php" method="GET" class="filters-sidebar">
                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                    
                    <h3 class="filter-title">Filtrer par</h3>

                    <div class="filter-group">
                        <h4>Compétences</h4>
                        <div class="skills-checkbox-container" style="grid-template-columns: 1fr; gap: 10px; max-height: 200px; overflow-y: auto;"> 
                            <?php foreach ($all_skills as $skill): ?>
                                <div class="skill-checkbox-item" style="padding: 5px;">
                                    <input 
                                        type="checkbox" 
                                        name="skills[]" 
                                        id="skill_filter_<?php echo $skill['id']; ?>" 
                                        value="<?php echo $skill['id']; ?>"
                                        <?php if (in_array($skill['id'], $skills_filter_ids)) echo 'checked'; // (تذكر 😈 الاختيار) ?>
                                    >
                                    <label for="skill_filter_<?php echo $skill['id']; ?>"><?php echo htmlspecialchars($skill['name']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h4>Budget (TND)</h4>
                        <div class="filter-budget">
                            <input type="number" name="min_budget" placeholder="Min" min="0" value="<?php echo htmlspecialchars($min_budget); ?>">
                            <span>-</span>
                            <input type="number" name="max_budget" placeholder="Max" min="0" value="<?php echo htmlspecialchars($max_budget); ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary-solid btn-full">Appliquer les filtres</button>
                    </div>
                </form>

                <section class="project-list">
                    
                    <?php if (empty($projects)): ?>
                        <div class="project-card">
                            <p style="text-align: center; color: var(--light-text);">
                                <strong>Aucun projet trouvé.</strong><br>
                                Essayez d'ajuster vos filtres de recherche.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            
                            <?php
                            // --- (الكود الأسطوري 😈: "العملية الجراحية" 👨‍⚕️) ---
                            $current_project_id = $project['id'];

                            // (أ. جلب المهارات ⭐️ "الحية" 😈)
                            $stmt_skills = $conn->prepare(
                                "SELECT s.name FROM skills s
                                 JOIN project_skill ps ON s.id = ps.skill_id
                                 WHERE ps.project_id = ?"
                            );
                            $stmt_skills->execute([$current_project_id]);
                            $project_skills = $stmt_skills->fetchAll(PDO::FETCH_COLUMN);

                            // (ب. حساب العروض 📊 "الحية" 😈)
                            $stmt_offers = $conn->prepare("SELECT COUNT(id) as count FROM proposals WHERE project_id = ?");
                            $stmt_offers->execute([$current_project_id]);
                            $offer_count = $stmt_offers->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                            // --- (نهاية "العملية الجراحية" 👨‍⚕️) ---
                            ?>

                            <div class="project-card">
                                <div class="card-header">
                                    <h3>
                                        <a href="project-details.php?id=<?php echo $project['id']; ?>">
                                            <?php echo htmlspecialchars($project['title']); ?>
                                        </a>
                                    </h3>
                                    <?php if ($project['budget']): ?>
                                        <span class="project-budget"><?php echo htmlspecialchars($project['budget']); ?> TND</span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-date">Publié par: <strong><?php echo htmlspecialchars($project['client_name']); ?></strong> le <?php echo date('d M Y', strtotime($project['created_at'])); ?></p>
                                <p class="card-description">
                                    <?php echo htmlspecialchars(substr($project['description'], 0, 150)); ?>...
                                </p>
                                
                                <div class="card-skills">
                                    <?php if (empty($project_skills)): ?>
                                        <span>(Aucune compétence listée)</span>
                                    <?php else: ?>
                                        <?php foreach ($project_skills as $skill_name): ?>
                                            <span><?php echo htmlspecialchars($skill_name); ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="card-footer">
                                    <span class="offer-count"><strong><?php echo $offer_count; ?></strong> Offre(s)</span>
                                    <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-primary-solid btn-sm">Voir Détails</a>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>

                </section>
            </div>
        </div>
    </main>
    
    <script src="script.js"></script> 
</body>
</html>