<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)
// ($conn, $current_user_id, $user_name, $user_role, $user_email جاهزون 🚀)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - FreeLink</title>
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
        <div class="container">
            <h1 class="dashboard-title" style="text-align: center;">Contactez-nous 📞</h1>
            <p class="section-subtitle">Nous sommes là pour vous aider. Contactez-nous à tout moment.</p>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'contact_success'): ?>
                <div class="success-message">
                    Succès ! Votre message a été envoyé. Nous vous répondrons bientôt.
                </div>
            <?php endif; ?>
            
            <div class="contact-container">
                
                <div class="contact-info">
                    <h3>Informations de Contact</h3>
                    <p>Remplissez le formulaire ou contactez-nous directement via ces canaux :</p>
                    
                    <div class="info-box">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <p>freelinkee@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Téléphone</strong>
                            <p>+216 29235256 </p>
                            <p>+216 xxxxxx</p>
                            <p>+216 xxxxxx</p>
                        </div>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong>
                            <p>ISET Kébili, Tunisie</p>
                        </div>
                    </div>
                    
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/in/free-freelink-a01777389?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <form action="contact_process.php" method="POST" class="auth-form">
                        
                        <div class="form-group-half">
                            <div class="form-group">
                                <label for="name">Votre Nom</label>
                                <input type="text" id="name" name="name" placeholder="Votre nom complet" 
                                       value="<?php echo htmlspecialchars($user_name ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Votre Email</label>
                                <input type="email" id="email" name="email" placeholder="Votre email" 
                                       value="<?php echo htmlspecialchars($user_email ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Sujet</label>
                            <input type="text" id="subject" name="subject" placeholder="Sujet de votre message" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Votre Message</label>
                            <textarea id="message" name="message" rows="8" placeholder="Écrivez votre message ici..." required></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary-solid btn-full">Envoyer le Message</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <footer class="footer">
        </footer>

    <script> window.CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>; </script>
    <script src="script.js"></script> 
</body>
</html>