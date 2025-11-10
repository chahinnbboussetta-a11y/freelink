<?php
// --- (الاتصال بـ "العقل" 🧠 الأكبر) ---
// (نحن نستخدم ".." 😈 لأننا "داخل" 📁 مجلد)
require_once '../config.php';

// --- (الحارس الأسطوري 😈) ---
// (إذا كنت مسجلاً 🕵️‍♂️ *و* كنت "مديراً" 👑، اذهب إلى "العرش" 👑)
if ($current_user_id && $user_role == 'admin') {
    header("Location: dashboard.php");
    exit();
}
// (إذا كنت مسجلاً 🕵️‍♂️ ولكنك "لست مديراً" 🚫، اذهب للداشبورد العادي)
elseif ($current_user_id) {
    header("Location: ../dashboard-student.php"); // (أو client)
    exit();
}
// (إذا كنت "زائراً" 👻، أظهر الاستمارة 📝)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur 👑 - FreeLink</title>
    <link rel="stylesheet" href="../style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <nav class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">FreeLink [Admin]</a>
        </div>
    </nav>

    <main class="auth-page">
        <div class="auth-container">
            <h1 class="auth-title">Connexion Admin 👑</h1>
            <p class="auth-subtitle">Accès réservé aux administrateurs.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    Email ou mot de passe incorrect.
                </div>
            <?php endif; ?>

            <form action="admin_login_process.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email admin" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Mot de passe admin" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary-solid btn-full">Entrer 🚀</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>