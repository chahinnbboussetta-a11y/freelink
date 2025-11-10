<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once '../config.php'; // (استخدم ".." 😈)

// --- (الخطوة 2: "الحارس" 🛡️ الأسطوري 😈) ---
if (!$current_user_id || $user_role != 'admin') {
    header("Location: ../login.html");
    exit();
}
// ($conn جاهز 🚀)

// --- (الكود الأسطوري الجديد 😈: جلب "كل" المهارات ⭐️) ---
$stmt_skills = $conn->prepare(
    "SELECT 
        s.*, 
        (SELECT COUNT(ss.user_id) FROM student_skill ss WHERE ss.skill_id = s.id) as student_count,
        (SELECT COUNT(ps.project_id) FROM project_skill ps WHERE ps.skill_id = s.id) as project_count
     FROM skills s
     ORDER BY s.name ASC" // (رتبها أبجدياً 🤓)
);
$stmt_skills->execute();
$all_skills = $stmt_skills->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN 👑 - Gérer les Compétences</title>
    <link rel="stylesheet" href="../style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="logo">FreeLink [ADMIN 👑]</a>
            <ul class="nav-links">
                <li><a href="dashboard.php">Projets en attente</a></li>
                <li><a href="manage_skills.php">Gérer les Compétences</a></li>
                <li><a href="../logout.php" class="btn btn-primary">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            <h1 class="dashboard-title">Gérer les Compétences 🤓</h1>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'skill_added'): ?>
                <div class="success-message">Succès ! La compétence ⭐️ a été ajoutée.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'skill_deleted'): ?>
                <div class="error-message" style="background-color: #fffbe6; color: #b88b00; border-color: #ffe58f;">
                    Succès ! La compétence 💣 a été supprimée.
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                 <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>


            <div class="admin-layout-container">

                <div class="admin-sidebar">
                    <div class="dashboard-section">
                        <h2 class="section-title-sm">Ajouter une Compétence 🚀</h2>
                        <form action="admin_action.php" method="POST" class="auth-form">
                            <input type="hidden" name="action" value="add_skill">
                            
                            <div class="form-group">
                                <label for="skill_name">Nom de la nouvelle compétence:</label>
                                <input type="text" id="skill_name" name="skill_name" placeholder="Ex: Ruby on Rails" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary-solid btn-full">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="admin-main-content">
                    <div class="dashboard-section">
                        <h2 class="section-title-sm">Toutes les Compétences (<?php echo count($all_skills); ?>)</h2>
                        
                        <div class="table-container">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Compétence ⭐️</th>
                                        <th>Nb. Étudiants 👨‍🎓</th>
                                        <th>Nb. Projets 💼</th>
                                        <th>Action 💣</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_skills as $skill): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($skill['name']); ?></strong></td>
                                            <td><?php echo $skill['student_count']; ?></td>
                                            <td><?php echo $skill['project_count']; ?></td>
                                            <td>
                                                <form action="admin_action.php" method="POST">
                                                    <input type="hidden" name="action" value="delete_skill">
                                                    <input type="hidden" name="skill_id" value="<?php echo $skill['id']; ?>">
                                                    <button type="submit" class="btn btn-secondary btn-sm" style="background: #c23934; color: white;">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> </div>
    </main>
</body>
</html>