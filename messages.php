<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; // (هذا هو "السحر" 😈)

// --- (الخطوة 2: "الحارس" 🛡️ - "النسخة" النظيفة 🚀) ---
// (هذه الصفحة "مشتركة" 💬، لذا نتأكد فقط أنه مسجل 🕵️‍♂️)
if (!$current_user_id) {
    header("Location: login.html"); // (اطرده 😈)
    exit();
}
// ($conn, $current_user_id, $user_name, $user_role جاهزون 🚀)

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Messagerie - FreeLink</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
</head>
<body>
    <nav class="navbar">
      <div class="container">
        <a href="<?php echo ($user_role == 'client') ? 'dashboard-client.php' : 'dashboard-student.php'; ?>" class="logo">FreeLink</a>
        <ul class="nav-links">
          <li><a href="<?php echo ($user_role == 'client') ? 'dashboard-client.php' : 'dashboard-student.php'; ?>">Tableau de bord</a></li>
          
          <?php if ($user_role == 'freelancer'): // (إذا كان "طالباً" 👨‍🎓) ?>
            <li><a href="explore-projects.php">Explorer les projets</a></li>
            <li><a href="profile-edit.php" class="nav-link-login">Mon Profil</a></li>
          <?php else: // (إذا كان "عميلاً" 💼) ?>
            <li><a href="publish-project.php">Publier un projet</a></li>
            <li><a href="profile-client-edit.php" class="nav-link-login">Mon Profil</a></li> 
          <?php endif; ?>
          
          <li><a href="messages.php">Messages</a></li>
          <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
        </ul>
      </div>
    </nav>

    <main class="chat-page">
      <div class="container">
        <div class="chat-container">
          
          <aside class="conversations-sidebar">
            <div class="chat-header">
              <h3>Messagerie</h3>
            </div>
            <div class="convo-list" id="convo-list-container">
              </div>
          </aside>

          <section class="chat-window">
            
            <header class="chat-window-header" id="chat-window-header">
                <div class="header-info-wrapper">
                     <h4>Sélectionnez une conversation</h4>
                </div>
                <div class="header-actions">
                    <form action="submit_work_process.php" method="POST" id="submit-work-form">
                        <input type="hidden" id="submit-project-id" name="project_id" value="">
                        <button type="submit" class="btn btn-primary-solid btn-sm">
                            <i class="fas fa-check-circle"></i> Soumettre le travail final
                        </button>
                    </form>
                </div>
            </header>

            <main class="chat-body" id="chat-body">
                </main>

            <form class="chat-input-area" id="chat-send-form">
                <button type="button" class="btn-icon"><i class="fas fa-paperclip"></i></button>
                <input type="text" id="chat-message-input" placeholder="Écrire un message..." autocomplete="off">
                <button type="submit" class="btn-icon btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

          </section>
        </div>
      </div>
    </main> 
    
    <script>
        // (إرسال ID المستخدم من PHP إلى "الوحش" 😈)
        window.CURRENT_USER_ID = <?php echo json_encode($current_user_id); ?>;
    </script>
    <script src="script.js"></script> 

  </body>
</html>