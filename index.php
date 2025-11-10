<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)
// ($conn, $current_user_id, $user_role جاهزون 🚀)

// --- (الخطوة 2: "العقل" 🧠 "الحقيقي" يبدأ هنا) ---

// (أ. "الـ JOIN الوحش 1" 😈: جلب أفضل 8 مهارات ⭐️ + عدد الطلاب 🧮)
$stmt_categories = $conn->prepare(
    "SELECT 
        s.id, 
        s.name, 
        COUNT(ss.user_id) as student_count
     FROM 
        skills s
     LEFT JOIN 
        student_skill ss ON s.id = ss.skill_id
     GROUP BY 
        s.id, s.name
     ORDER BY 
        student_count DESC
     LIMIT 8" // (فقط أفضل 8 😈)
);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);


// (ب. "الـ JOIN الوحش 2" 😈: جلب أفضل 4 طلاب 👨‍🎓 ⭐️)
$stmt_talents = $conn->prepare(
    "SELECT 
        u.id, 
        u.name, 
        fp.headline, 
        fp.university,
        fp.profile_picture,
        AVG(r.rating) as avg_rating,
        COUNT(r.id) as review_count
     FROM 
        users u
     JOIN 
        freelancer_profiles fp ON u.id = fp.user_id
     LEFT JOIN 
        reviews r ON u.id = r.reviewed_id
     WHERE 
        u.role = 'freelancer'
     GROUP BY 
        u.id, u.name, fp.headline, fp.profile_picture, fp.university
     ORDER BY 
        avg_rating DESC, review_count DESC
     LIMIT 4" // (فقط أفضل 4 😈)
);
$stmt_talents->execute();
$featured_talents = $stmt_talents->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreeLink - Plateforme Freelance pour Étudiants</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">FreeLink</a>
            <ul class="nav-links">
                <?php if ($current_user_id): // (إذا كان مسجلاً 🕵️‍♂️) ?>
                    <li><a href="<?php echo ($user_role == 'client') ? 'dashboard-client.php' : 'dashboard-student.php'; ?>">Tableau de bord</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
                <?php else: // (إذا كان زائراً 👻) ?>
                    <li><a href="about.php">À Propos</a></li>
                    <li><a href="login.html" class="nav-link-login">Se connecter</a></li>
                    <li><a href="signup.php" class="btn btn-primary">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1>Les meilleurs talents étudiants de Tunisie pour réaliser vos projets</h1>
            <div class="hero-buttons">
                <a href="publish-project.php" class="btn btn-primary-solid">Publier un projet</a>
                <a href="explore-projects.php" class="btn btn-secondary">Explorer les compétences</a>
            </div>
        </div>
    </header>

    <section id="how-it-works" class="how-it-works-section">
        <div class="container">
            <h2 class="section-title">Comment ça marche ?</h2>
            <p class="section-subtitle">Commencez en 4 étapes simples.</p>
            
            <div class="steps-container">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Publiez votre projet</h3>
                    <p>Décrivez vos besoins en quelques clics grâce à notre formulaire simple.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Recevez des offres</h3>
                    <p>Recevez des propositions de nos meilleurs talents étudiants qualifiés.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Collaborez en sécurité</h3>
                    <p>Discutez, partagez des fichiers et payez en toute sécurité via notre plateforme.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3>Validez le travail</h3>
                    <p>Libérez le paiement uniquement lorsque vous êtes 100% satisfait du travail.</p>
                </div>
            </div>
        </div>
    </section> <section id="categories" class="categories-section">
        <div class="container">
            <h2 class="section-title">Explorer par catégorie</h2>
            <p class="section-subtitle">Trouvez le service dont vous avez besoin pour votre projet.</p>

            <div class="categories-container">
                
                <?php if (empty($categories)): ?>
                    <p style="text-align: center; color: var(--light-text);">Aucune catégorie à afficher pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <a href="explore-projects.php?skills[]=<?php echo $category['id']; ?>" class="category-card">
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            <p>(<?php echo $category['student_count']; ?> Étudiants)</p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </section> <section id="featured-talents" class="talents-section">
        <div class="container">
            <h2 class="section-title">Découvrez nos talents à la une</h2>
            <p class="section-subtitle">Des étudiants vérifiés et prêts à transformer vos idées en réalité.</p>

            <div class="talents-container">
                
                <?php if (empty($featured_talents)): ?>
                    <p style="text-align: center; color: var(--light-text);">Aucun talent à afficher pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($featured_talents as $talent): ?>
                        <div class="talent-card">
                            <img src="<?php echo htmlspecialchars($talent['profile_picture'] ?? 'images/default-avatar.png'); ?>" alt="Photo de <?php echo htmlspecialchars($talent['name']); ?>" class="talent-photo">
                            <h3 class="talent-name">
                                <a href="profile.php?id=<?php echo $talent['id']; ?>"><?php echo htmlspecialchars($talent['name']); ?></a>
                            </h3>
                            <p class="talent-headline"><?php echo htmlspecialchars($talent['headline'] ?? 'Nouveau talent'); ?></p>
                            <p class="talent-university"><?php echo htmlspecialchars($talent['university'] ?? 'Université non spécifiée'); ?></p>
                            <div class="talent-rating">
                                <?php echo str_repeat('⭐️', round($talent['avg_rating'] ?? 0)); // (التقييم "الحقيقي" ⭐️) ?>
                                <span class="rating-text">(<?php echo round($talent['avg_rating'] ?? 0, 1); ?>)</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
            </div>
        </div>
    </section> <footer class="footer">
        <div class="container">
            <div class="footer-container">
                
                <div class="footer-col">
                    <a href="index.php" class="logo footer-logo">FreeLink</a>
                    <p class="footer-desc">La première plateforme tunisienne de freelance dédiée aux étudiants.</p>
                </div>
                <div class="footer-col">
                    <h4>Pour les Clients</h4>
                    <ul>
                        <li><a href="publish-project.php">Publier un projet</a></li>
                        <li><a href="#how-it-works">Comment ça marche ?</a></li>
                        <li><a href="explore-projects.php">Explorer les compétences</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Pour les Étudiants</h4>
                    <ul>
                        <li><a href="explore-projects.php">Trouver des projets</a></li>
                        <li><a href="signup.php">S'inscrire</a></li>
                        <li><a href="login.html">Mon profil</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="terms-of-service.php">Conditions d'utilisation</a></li>
                        <li><a href="privacy-policy.php">Politique de confidentialité</a></li>
                        <li><a href="contact.php">Contactez-nous</a></li>
                    </ul>
                </div>

            </div>
            <div class="footer-bottom">
                <p>© 2025 FreeLink (ISET Kébili). Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <script> const CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>; </script>
    <script src="script.js"></script> 
</body>
</html>