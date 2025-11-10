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
    <title>Conditions d'Utilisation - FreeLink</title>
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
            <h1 class="dashboard-title" style="text-align: center;">Conditions d'Utilisation ⚖️</h1>

            <div class="static-page-card">
                <p><strong>Dernière mise à jour :</strong> 10 Novembre 2025</p>

                <h2>Article 1 : Acceptation des Conditions</h2>
                <p>Bienvenue sur FreeLink. En vous inscrivant (`signup.php`) ou en utilisant notre plateforme, vous ("l'Utilisateur", "le Client" 💼, "l'Étudiant" 👨‍🎓) acceptez d'être lié par ces Conditions d'Utilisation ("CGU"). Si vous n'acceptez pas ces règles "أسطورية" 😈 (légendaires), n'utilisez pas la plateforme.</p>

                <h2>Article 2 : Rôle de FreeLink 👑</h2>
                <p>FreeLink est une "place de marché" (Marketplace) 🖼️. Notre mission est de connecter 🤝 les Clients 💼 (qui publient des `projects`) avec les Étudiants 👨‍🎓 (qui soumettent des `proposals`).</p>
                <p><strong>FreeLink n'est PAS un employeur.</strong> Nous ne sommes pas partie au contrat direct entre le Client et l'Étudiant. Nous fournissons "l'arène" 🏟️ (la plateforme), le "Chat" 💬, et le système de "Confiance" 🛡️ (Paiement et Avis).</p>

                <h2>Article 3 : Obligations de l'Utilisateur (Le "Hadhra" 😈)</h2>
                <p>En utilisant FreeLink, vous vous engagez à :</p>
                <ul>
                    <li>Fournir des informations "réelles" ✅ (vraies) lors de l'inscription et dans votre profil (`profile-edit.php`).</li>
                    <li>Ne pas utiliser le Chat 💬 (`messages.php`) pour harceler, insulter, ou envoyer du spam.</li>
                    <li>Ne pas tenter de contourner  circumvent 😈 la plateforme (par exemple, demander un paiement "hors ligne" 💸 avant que le projet ne soit `completed` 🏁).</li>
                    <li>Garder votre mot de passe "secret" 🤫.</li>
                    <li>(Pour les Étudiants 👨‍🎓): Livrer un travail "Proffitionale" 🚀 et respecter les délais convenus.</li>
                    <li>(Pour les Clients 💼): Fournir des descriptions de projet "claires" 📝 et payer pour le travail accepté.</li>
                </ul>

                <h2>Article 4 : Le Cycle de Vie du Projet (La "Loop" 🔄)</h2>
                <p>Le "cycle de vie" 🏁 (Lifecycle) d'un projet est le suivant :</p>
                <ol>
                    <li>Le Client 💼 publie un projet (`status = 'open'`).</li>
                    <li>L'Étudiant 👨‍🎓 envoie une offre (`proposals.status = 'pending'`).</li>
                    <li>Le Client 💼 accepte l'offre (`accept_proposal.php`). Le statut du projet devient `in_progress` et le statut de l'offre devient `accepted` 🚀. (Une conversation 💬 est créée).</li>
                    <li>L'Étudiant 👨‍🎓 termine le travail et le soumet (`submit_work_process.php`). Le statut du projet devient `in_review` 🕵️‍♂️.</li>
                    <li>Le Client 💼 examine le travail et (s'il est satisfait) accepte le paiement (`complete_project_process.php`). Le statut du projet devient `completed` 🏁.</li>
                </ol>

                <h2>Article 5 : Paiements et Frais 💰 (Le "Futur" 😈)</h2>
                <p>FreeLink est actuellement en "Mode Test" 😈 (localhost). Les paiements sont "simulés" 🤖.</p>
                <p>Dans le futur (lors du "Déploiement" 🌐), le Client 💼 devra déposer le montant (`budget`) dans un compte "séquestre" (Escrow) 🛡️ lors de l'acceptation (Étape 3). FreeLink prendra une "commission" (frais de service 💸) sur chaque transaction `completed` 🏁 pour financer "le Goul" 😈 (le serveur).</p>

                <h2>Article 6 : Avis et Évaluations ⭐️</h2>
                <p>Le système d'avis (`reviews`) est le cœur ❤️ de la confiance sur FreeLink. Vous vous engagez à laisser des avis "honnêtes" (honnêtes) et "réels" ✅ basés sur votre expérience.</p>
                <p>Les avis "faux" 👻 (Fake reviews) ou "calomnieux" (insultants) seront supprimés 🗑️.</p>

                <h2>Article 7 : Suspension et Résiliation 🚫</h2>
                <p>Nous (les "Admins" 👑: Chahin, Rayen, Anas) nous réservons le droit de "bannir" 🚫 (suspendre ou supprimer) votre compte, sans préavis, si vous violez ces règles 📜 (le "Hadhra" 😈 de l'Article 3).</p>
                
                <h2>Article 8 : Limitation de Responsabilité ⚖️</h2>
                <p>FreeLink est fourni "tel quel" (As Is). Nous ne sommes pas responsables des "problèmes" 🐞 (Bugs) techniques, ni de la "qualité" 🎨 du travail fourni par l'Étudiant 👨‍🎓, ni du "retard" ⏰ de paiement du Client 💼. Nous sommes seulement "l'intermédiaire" 🤝 (the middle-man).</p>
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