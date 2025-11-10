<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription - FreeLink</title>
    <link rel="stylesheet" href="style.css" />

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <a href="index.php" class="logo">FreeLink</a>

        <ul class="nav-links">
          <li><a href="index.php#about">À Propos</a></li>
          <li><a href="login.html" class="nav-link-login">Se connecter</a></li>
        </ul>
      </div>
    </nav>

    <main class="auth-page">
      <div class="auth-container">
        <h1 class="auth-title">Créer un compte</h1>
        <p class="auth-subtitle">
          Rejoignez notre communauté d'étudiants et de clients.
        </p>

        <form action="signup_process.php" method="POST" class="auth-form">
          <div class="form-group">
            <label for="name">Nom complet</label>
            <input
              type="text"
              id="name"
              name="name"
              placeholder="Entrez votre nom complet"
              required
            />
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Entrez votre email"
              required
            />
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Créez un mot de passe"
              required
            />
          </div>

          <div class="form-group">
            <label for="password_confirm">Confirmer le mot de passe</label>
            <input
              type="password"
              id="password_confirm"
              name="password_confirm"
              placeholder="Confirmez votre mot de passe"
              required
            />
          </div>

          <div class="form-group">
            <label for="role">Je suis...</label>
            <select id="role" name="role" required>
              <option value="" disabled selected>-- Choisir un rôle --</option>
              <option value="client">Un Client (Je veux embaucher)</option>
              <option value="freelancer">
                Un Étudiant (Je veux travailler)
              </option>
            </select>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary-solid btn-full">
              Créer mon compte
            </button>
          </div>
        </form>

        <div class="auth-footer">
          <p>
            Vous avez déjà un compte ? <a href="login.html">Se connecter</a>
          </p>
        </div>
      </div>
    </main>
  </body>
</html>
