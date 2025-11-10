<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once '../config.php'; // (استخدم ".." 😈)

// --- (الخطوة 2: "الحارس" 🛡️ الأسطوري 😈) ---
if (!$current_user_id || $user_role != 'admin') {
    header("Location: ../login.html");
    exit();
}
// ($conn, $current_user_id, $user_name جاهزون 🚀)

// --- (الكود الأسطوري الجديد 😈: جلب المشاريع "المعلقة" ⏳) ---
$stmt_pending = $conn->prepare(
    "SELECT p.*, u.name as client_name 
     FROM projects p
     JOIN users u ON p.client_id = u.id
     WHERE p.status = 'pending_approval'
     ORDER BY p.created_at ASC" // (الأقدم أولاً 😈)
);
$stmt_pending->execute();
$pending_projects = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN 👑 - Tableau de bord</title>
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
            <h1 class="dashboard-title">Bienvenue, "Admin" <?php echo htmlspecialchars($user_name); ?>!</h1>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'approved'): ?>
                <div class="success-message">Succès ! Le projet a été "approuvé" 👍 et est maintenant "open".</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'rejected'): ?>
                <div class="error-message" style="background-color: #fffbe6; color: #b88b00; border-color: #ffe58f;">
                    Succès ! Le projet a été "rejeté" 👎 et supprimé.
                </div>
            <?php endif; ?>

            <div class="dashboard-section">
                <h2 class="section-title-sm">Projets en attente d'approbation (<?php echo count($pending_projects); ?>)</h2>
                
                <div class="table-container">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Titre du Projet</th>
                                <th>Budget (TND)</th>
                                <th>Date</th>
                                <th>Actions 😈</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_projects)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--light-text);">Aucun projet en attente. "نار" 🔥!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pending_projects as $project): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($project['client_name']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['budget'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('d M Y', strtotime($project['created_at'])); ?></td>
                                        <td>
                                            <form action="admin_action.php" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-primary-solid btn-sm" style="background: #2b7d4a;">
                                                    <i class="fas fa-check"></i> Approuver
                                                </button>
                                            </form>
                                            <form action="admin_action.php" method="POST" style="display: inline-block;">
                                                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" name="action" value="reject" class="btn btn-secondary btn-sm" style="background: #c23934; color: white;">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>