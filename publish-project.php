<?php
// --- (حارس البوابة المطور 😈) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. (الحارس 🛡️ الصحيح)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $_SESSION['user_id']; 

// 2. الاتصال
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// 3. (جلب "كل" المهارات ⭐️ للفلتر)
$stmt_all_skills = $conn->prepare("SELECT * FROM skills ORDER BY name ASC");
$stmt_all_skills->execute();
$all_skills = $stmt_all_skills->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier un Projet - FreeLink</title>
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
                <li><a href="profile-client-edit.php" class="nav-link-login">Mon Profil</a></li>
                <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            <h1 class="dashboard-title">Publier un nouveau projet</h1>

            <div class="publish-form-container">
                <form action="publish_process.php" method="POST" enctype="multipart/form-data">
                    
                    <fieldset class="form-step">
                        <legend>Étape 1: Description</legend>
                        <div class="form-group">
                            <label for="title">Titre du projet</label>
                            <input type="text" id="title" name="title" placeholder="Ex: Création de logo pour startup" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description détaillée</label>
                            <textarea id="description" name="description" rows="8" placeholder="Décrivez en détail ce dont vous avez besoin..." required></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="form-step">
                        <legend>Étape 2: Détails</legend>

                        <div class="form-group">
                            <label for="skills-multiselect">Compétences requises ⭐️</label>
                            <div class="multiselect-container" id="skills-multiselect"> 
                                <div class="multiselect-display" id="multiselect-display">
                                    <span>Sélectionnez les compétences...</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="multiselect-options" id="multiselect-options">
                                    <?php foreach ($all_skills as $skill): ?>
                                        <div class="skill-checkbox-item">
                                            <input type="checkbox" name="skills[]" id="skill_<?php echo $skill['id']; ?>" value="<?php echo $skill['id']; ?>">
                                            <label for="skill_<?php echo $skill['id']; ?>"><?php echo htmlspecialchars($skill['name']); ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-half">
                            <div class="form-group">
                                <label for="budget">Votre budget (en TND)</label>
                                <input type="number" id="budget" name="budget" placeholder="Ex: 200" min="0">
                            </div>
                            <div class="form-group">
                                <label for="duration">Durée estimée (en jours)</label>
                                <input type="number" id="duration" name="duration" placeholder="Ex: 7" min="1">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="file">Joindre des fichiers (Optionnel)</label>
                            <input type="file" id="file" name="file" class="input-file">
                            <small>Fichiers autorisés (10MB Max): PDF, ZIP, DOCX, TXT</small>
                        </div>

                        <div class="form-group">
                            <label for="additional_notes">Commentaires / Instructions (Optionnel) 📝</label>
                            <textarea id="additional_notes" name="additional_notes" rows="4" placeholder="Avez-vous des instructions spéciales, des liens, ou des notes pour le freelance?"></textarea>
                        </div>

                    </fieldset>

                    <fieldset class="form-step">
                        <legend>Étape 3: Vérification</legend>
                        <p>En cliquant sur "Publier", votre projet sera soumis à l'équipe d'administration pour approbation.</p>
                        <button type="submit" class="btn btn-primary-solid btn-full">Publier le projet</button>
                    </fieldset>

                </form>
            </div>
        </div>
    </main>
    
    <script src="script.js"></script> 
</body>
</html>