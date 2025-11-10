<?php
// --- (حارس البوابة 😈 - نسخة العميل 💼) ---
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. (الحارس 🛡️ الصحيح) - (هذه الصفحة للعميل فقط)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $_SESSION['user_id']; // ID العميل

// 2. الاتصال
$servername = "localhost"; $username = "root"; $password = ""; $dbname = "freelink_db";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// 3. (الـ JOIN الوحش 🚀) جلب بيانات العميل الحالية
$stmt_profile = $conn->prepare(
    "SELECT u.name, u.email, cp.* FROM users u
     LEFT JOIN client_profiles cp ON u.id = cp.user_id
     WHERE u.id = ? AND u.role = 'client'"
);
$stmt_profile->execute([$client_id]);
$profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    // (هذا يحدث إذا فشل `signup_process.php` في إنشاء البروفايل)
    die("خطأ: لم يتم العثور على بروفايل العميل. (حاول إنشاء حساب عميل جديد)");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil Client - FreeLink</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="dashboard-client.php" class="logo">FreeLink</a>
            <ul class="nav-links">
                <li><a href="dashboard-client.php">Tableau de bord</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="profile-client-edit.php" class="nav-link-login">Mon Profil</a></li> <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            <h1 class="dashboard-title">Modifier Profil Client</h1>

            <div class="publish-form-container">
                
                <form action="profile_client_process.php" method="POST" enctype="multipart/form-data">
                    
                    <fieldset class="form-step">
                        <legend>Informations de base</legend>

                        <div class="form-group profile-picture-preview">
                            <label>Logo / Photo de profil</label>
                            <img src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'images/default-avatar.png'); ?>" alt="Avatar" class="proposal-avatar" style="width: 100px; height: 100px;">
                        </div>

                        <div class="form-group">
                            <label for="profile_picture">Changer la photo</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="input-file">
                        </div>

                        <div class="form-group">
                            <label for="name">Votre Nom Complet</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email (non modifiable)</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>
                        </div>
                    </fieldset>

                    <fieldset class="form-step">
                        <legend>Profil Client / Entreprise</legend>

                        <div class="form-group">
                            <label for="company_name">Nom de l'entreprise (Optionnel)</label>
                            <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>" placeholder="Ex: FreeLink SARL">
                        </div>

                        <div class="form-group">
                            <label for="website">Site Web (Optionnel)</label>
                            <input type="text" id="website" name="website" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>" placeholder="Ex: https://www.mon-site.com">
                        </div>

                        <div class="form-group">
                            <label for="bio">À propos de vous / Votre entreprise</label>
                            <textarea id="bio" name="bio" rows="6" placeholder="Parlez de votre entreprise ou de vos projets..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                        </div>
                        
                    </fieldset>

                    <fieldset class="form-step">
                        <button type="submit" class="btn btn-primary-solid btn-full">Enregistrer les modifications</button>
                    </fieldset>

                </form>
            </div>
        </div>
    </main>

</body>
</html>