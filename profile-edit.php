<?php
// --- (حارس البوابة 😈 - النسخة النهائية) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. (الحارس 🛡️ الصحيح)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'freelancer') {
    header("Location: login.html");
    exit();
}
$student_id = $_SESSION['user_id']; 

// 2. الاتصال
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// 3. (جلب 🚀) بيانات الطالب
$stmt_profile = $conn->prepare(
    "SELECT u.name, u.email, u.created_at as member_since, fp.* FROM users u
     LEFT JOIN freelancer_profiles fp ON u.id = fp.user_id
     WHERE u.id = ? AND u.role = 'freelancer'"
);
$stmt_profile->execute([$student_id]);
$profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die("خطأ: هذا الطالب غير موجود.");
}

// 4. (جلب "كل" 📚 المهارات ⭐️)
$stmt_all_skills = $conn->prepare("SELECT * FROM skills ORDER BY name ASC");
$stmt_all_skills->execute();
$all_skills = $stmt_all_skills->fetchAll(PDO::FETCH_ASSOC);

// 5. (جلب 🕵️‍♂️ مهارات "هذا" الطالب)
$stmt_student_skills = $conn->prepare("SELECT skill_id FROM student_skill WHERE user_id = ?");
$stmt_student_skills->execute([$student_id]);
$student_skill_ids = $stmt_student_skills->fetchAll(PDO::FETCH_COLUMN); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon Profil - FreeLink</title>
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
            <h1 class="dashboard-title">Modifier mon Profil</h1>

            <div class="publish-form-container">
                
                <form action="profile_edit_process.php" method="POST" enctype="multipart/form-data">
                    
                    <fieldset class="form-step">
                        <legend>Informations de base</legend>
                        <div class="form-group profile-picture-preview">
                            <label>Photo de profil actuelle</label>
                            <img src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'images/default-avatar.png'); ?>" alt="Avatar" class="proposal-avatar" style="width: 100px; height: 100px;">
                        </div>
                        <div class="form-group">
                            <label for="profile_picture">Changer la photo de profil</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="input-file">
                        </div>
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email (non modifiable)</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>
                        </div>
                    </fieldset>

                    <fieldset class="form-step">
                        <legend>Profil Freelance</legend>
                        <div class="form-group">
                            <label for="headline">Titre Professionnel (Headline)</label>
                            <input type="text" id="headline" name="headline" value="<?php echo htmlspecialchars($profile['headline']); ?>" placeholder="Ex: Développeur Full-Stack Laravel & React">
                        </div>
                        <div class="form-group">
                            <label for="bio">Biographie (Bio)</label>
                            <textarea id="bio" name="bio" rows="8" placeholder="Parlez de vous, vos compétences et vos expériences..."><?php echo htmlspecialchars($profile['bio']); ?></textarea>
                        </div>
                        <div class="form-group-half">
                            <div class="form-group">
                                <label for="university">Université / Institut</label>
                                <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($profile['university']); ?>" placeholder="Ex: ISET Kébili">
                            </div>
                            <div class="form-group">
                                <label for="major">Spécialité</label>
                                <input type="text" id="major" name="major" value="<?php echo htmlspecialchars($profile['major']); ?>" placeholder="Ex: Technologie de l'informatique">
                            </div>
                        </div>
                    </fieldset>
                        
                    <fieldset class="form-step">
                        <legend>Vos Compétences ⭐️</legend>
                        <div class="multiselect-container" id="skills-multiselect-profile">
                            <div class="multiselect-display" id="multiselect-display">
                                <span>Sélectionnez vos compétences...</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="multiselect-options" id="multiselect-options">
                                <?php if (empty($all_skills)): ?>
                                    <p style="padding: 10px; color: var(--light-text);">Aucune compétence n'a été ajoutée.</p>
                                <?php else: ?>
                                    <?php foreach ($all_skills as $skill): ?>
                                        <div class="skill-checkbox-item">
                                            <input 
                                                type="checkbox" 
                                                name="skills[]" 
                                                id="skill_<?php echo $skill['id']; ?>" 
                                                value="<?php echo $skill['id']; ?>"
                                                <?php if (in_array($skill['id'], $student_skill_ids)) echo 'checked'; // (التحقق الأسطوري 😈) ?>
                                            >
                                            <label for="skill_<?php echo $skill['id']; ?>"><?php echo htmlspecialchars($skill['name']); ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-step">
                        <button type="submit" class="btn btn-primary-solid btn-full">Enregistrer les modifications</button>
                    </fieldset>

                </form>
            </div>
        </div>
    </main>

    <script src="script.js"></script> 
</body>
</html>