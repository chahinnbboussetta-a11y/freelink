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
    <title>Politique de Confidentialité - FreeLink</title>
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
            <h1 class="dashboard-title" style="text-align: center;">Politique de Confidentialité 🛡️</h1>

            <div class="static-page-card">
                <p><strong>Dernière mise à jour :</strong> 10 Novembre 2025</p>

                <h2>Article 1 : Introduction</h2>
                <p>Bienvenue sur FreeLink. Cette politique de confidentialité vise à vous informer sur la manière dont nous collectons, utilisons, et protégeons vos données personnelles lorsque vous utilisez notre plateforme, conformément aux lois en vigueur en Tunisie 🇹🇳.</p>

                <h2>Article 2 : Les Données que nous Collectons 💾</h2>
                <p>Nous collectons les informations que vous nous fournissez directement :</p>
                <ul>
                    <li><strong>Informations d'Identité :</strong> Votre nom (`name`), adresse email (`email`), et mot de passe (crypté 😈 `password_hashed`).</li>
                    <li><strong>Informations de Profil (Étudiant 👨‍🎓) :</strong> Votre photo (`profile_picture`), titre (`headline`), biographie (`bio`), université, spécialité, et vos compétences (`skills`).</li>
                    <li><strong>Informations de Profil (Client 💼) :</strong> Votre nom d'entreprise (`company_name`), site web (`website`), et biographie (`bio`).</li>
                    <li><strong>Données de Projet :</strong> Les détails des projets (`projects`) que vous publiez, les offres (`proposals`) que vous faites, les fichiers 📎 que vous partagez (`file_path`), et les messages 💬 échangés (`messages`).</li>
                    <li><strong>Données d'Avis :</strong> Les évaluations (`rating`) ⭐️ et commentaires (`comment`) que vous laissez.</li>
                </ul>
                <p>Nous collectons aussi automatiquement certaines données techniques, comme votre cookie de session (`PHPSESSID`) 🍪 pour vous garder connecté.</p>

                <h2>Article 3 : Pourquoi nous Collectons vos Données 🧠</h2>
                <p>Nous utilisons vos données "uniquement" 😈 pour faire fonctionner "le Goul" (le site) :</p>
                <ul>
                    <li>Pour vous authentifier 🛡️ (Login).</li>
                    <li>Pour connecter les Clients 💼 et les Étudiants 👨‍🎓.</li>
                    <li>Pour permettre la gestion des projets (`in_progress`, `completed` 🏁).</li>
                    <li>Pour permettre la communication via le Chat 💬.</li>
                    <li>Pour afficher votre profil 🖼️ et vos évaluations ⭐️ au public (Clients/Étudiants).</li>
                </ul>

                <h2>Article 4 : Partage de vos Données 🤝</h2>
                <p>Nous ne vendons **jamais** vos données. Nous ne les partageons qu'aux personnes suivantes :</p>
                <ul>
                    <li><strong>Autres Utilisateurs :</strong> Votre profil (nom, photo, compétences ⭐️) est visible par les autres utilisateurs pour permettre à la plateforme de fonctionner.</li>
                    <li><strong>(Futur 💰) Fournisseurs de Paiement :</strong> (Comme Stripe ou PayPal) lorsque nous activerons "le المال" 😈.</li>
                    <li><strong>La Loi ⚖️ :</strong> Si la loi tunisienne 🇹🇳 nous y oblige.</li>
                </ul>

                <h2>Article 5 : Sécurité de vos Données 🛡️</h2>
                <p>Nous prenons la sécurité au sérieux !</p>
                <ul>
                    <li>Vos mots de passe sont "cryptés" (Hashed) 😈. Nous ne pouvons **pas** les voir.</li>
                    <li>Nous avons sécurisé 🛡️ le téléversement de fichiers (File Uploads) pour n'accepter que les types sûrs (PDF, PNG, etc.).</li>
                    <li>Votre session (`Session`) est protégée.</li>
                </ul>

                <h2>Article 6 : Vos Droits ✊</h2>
                <p>Conformément à la loi, vous avez le droit de :</p>
                <ul>
                    <li><strong>Accéder 🕵️‍♂️ :</strong> Voir vos données (via `profile-edit.php` 🛠️).</li>
                    <li><strong>Rectifier ✍️ :</strong> Modifier vos données (via `profile-edit.php` 🛠️).</li>
                    <li><strong>Supprimer 💣 :</strong> Demander la suppression de votre compte en nous contactant.</li>
                </ul>

                <h2>Article 7 : Cookies 🍪</h2>
                <p>Nous utilisons un seul cookie "essentiel" 😈: `PHPSESSID`. Ce cookie est nécessaire pour vous garder connecté 🛡️ (garder votre session ouverte). C'est tout.</p>

                <h2>Article 8 : Contactez-nous 📞</h2>
                <p>Si vous avez des questions sur cette politique, veuillez nous contacter via notre page <a href="contact.php">Contactez-nous</a>.</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-container">
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
    
    <script> window.CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>; </script>
    <script src="script.js"></script> 
</body>
</html>