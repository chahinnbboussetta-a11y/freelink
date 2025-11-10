<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)
// ($conn, $current_user_id, $user_role جاهزون 🚀)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos de FreeLink - Notre Mission</title>
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

    <main class="dashboard-page">
        
        <header class="hero-section-small">
            <div class="container">
                <h1>Notre Mission 🚀</h1>
                <p>Connecter les talents étudiants 👨‍🎓 avec les opportunités 💼 en Tunisie 🇹🇳.</p>
            </div>
        </header>

        <div class="container" style="margin-top: 40px;">
            
            <div class="static-page-card">
                <h2>Qui Sommes-Nous ?</h2>
                <p><strong>FreeLink</strong> est la première "Mini-plateforme de Freelance pour étudiants" en Tunisie. Née d'une idée à l'ISET Kébili, notre mission est de combler le fossé entre le monde académique et le monde professionnel.</p>
                <p>Nous croyons que les étudiants tunisiens 👨‍🎓 possèdent un talent "أسطوري" 😈 (légendaire) qui mérite d'être vu. Nous offrons aux entreprises 💼 et aux particuliers un accès direct à cette énergie nouvelle, tout en permettant aux étudiants de financer leurs études, de gagner une expérience "réelle" 🔥, et de construire leur "المعرض" 🖼️ (Portfolio) avant même d'être diplômés.</p>

                <h2>Notre Vision  VISION  visionary </h2>
                <p>Notre vision est simple : devenir la référence 👑 N°1 en Tunisie 🇹🇳 pour le "travail étudiant". Nous voulons "allumer le feu" 🔥 de l'entrepreneuriat et de la compétence chez chaque étudiant, de Kébili à Tunis, de "ful stok developer" 🚀 à "injenieur securité resau" 🛡️.</p>
            </div>

            <section id="team" class="talents-section" style="padding-top: 40px; background: none;">
                <div class="container" style="padding: 0;">
                    <h2 class="section-title">L'Équipe Fondatrice 😈</h2>
                    <p class="section-subtitle">Les "Ghouls" 😈 derrière le code.</p>

                    <div class="talents-container" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                        
                        <div class="talent-card">
                            <img src="images/chahin.jpg" alt="Photo de Chahin B." class="talent-photo">
                            <h3 class="talent-name">Chahin Boussetta</h3>
                            <p class="talent-headline">Chef Projet & Dév. Full-Stack 🚀</p>
                            <p class="talent-university">ISET Kébili</p>
                        </div>
                        
                        <div class="talent-card">
                            <img src="images/rayen.jpg" alt="Photo de Rayen" class="talent-photo">
                            <h3 class="talent-name">Rayen (Exemple)</h3>
                            <p class="talent-headline">Ingénieur Sécurité Réseau 🛡️</p>
                            <p class="talent-university">ISET Kébili</p>
                        </div>
                        
                        <div class="talent-card">
                            <img src="images/anas.jpg" alt="Photo de Anas" class="talent-photo">
                            <h3 class="talent-name">Anas (Exemple)</h3>
                            <p class="talent-headline">Développeur Back-End 🧠</p>
                            <p class="talent-university">ISET Kébili</p>
                        </div>
                        
                        <div class="talent-card">
                            <img src="images/mlka.jpg" alt="Photo de Molka" class="talent-photo">
                            <h3 class="talent-name">Molka (Exemple)</h3>
                            <p class="talent-headline">Designer UI/UX 🎨</p>
                            <p class="talent-university">ISET Kébili</p>
                        </div>
                        
                    </div>
                </div>
            </section>
            
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-col">
                    <h4>FreeLink</h4>
                    <ul>
                        <li><a href="about.php">À Propos</a></li> <li><a href="privacy-policy.php">Politique de confidentialité</a></li>
                        <li><a href="contact.php">Contactez-nous</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 FreeLink (ISET Kébili). Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <script> window.CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>; </script>
    <script src="script.js"></script> 
</body>
</html>