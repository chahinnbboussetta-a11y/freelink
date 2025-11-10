<?php
// --- (الخطوة 1: الاتصال بـ "العقل" 🧠 الأكبر) ---
require_once 'config.php'; 

// --- (الخطوة 2: "الحارس" 🛡️) ---
if (!$current_user_id || $user_role != 'client') {
    header("Location: login.html");
    exit();
}
$client_id = $current_user_id;

// 3. (جلب 🕵️‍♂️ "العملية" 💰)
if (!isset($_GET['tid']) || empty($_GET['tid'])) {
    die("خطأ: رقم العملية (Transaction ID) مفقود.");
}
$transaction_id = $_GET['tid'];

// 4. (الـ JOIN الوحش 🚀: جلب "العملية" 💰 + "المشروع" 📄)
$stmt = $conn->prepare(
    "SELECT t.*, p.title as project_title
     FROM transactions t
     JOIN projects p ON t.project_id = p.id
     WHERE t.id = ? AND t.client_id = ? AND t.status = 'pending'"
);
$stmt->execute([$transaction_id, $client_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die("خطأ: هذه العملية غير موجودة أو تم دفعها بالفعل. 😈");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé - FreeLink</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard-client.php" class="logo">FreeLink [Paiement 💳]</a>
            <ul class="nav-links">
                <li><a href="dashboard-client.php">Tableau de bord</a></li>
                <li><a href="logout.php" class="btn btn-primary">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <main class="dashboard-page">
        <div class="container">
            <h1 class="dashboard-title" style="text-align: center;">Paiement Sécurisé (Simulation 🤖)</h1>
            <p class="section-subtitle">Votre paiement sera gardé en "séquestre" (Escrow) 🛡️ jusqu'à ce que le projet soit `completed`.</p>

            <div class="payment-card">
                <h3>Résumé de la Commande</h3>
                
                <div class="payment-line-item">
                    <span>Projet:</span>
                    <strong><?php echo htmlspecialchars($transaction['project_title']); ?></strong>
                </div>
                <div class="payment-line-item">
                    <span>Montant (TND):</span>
                    <strong><?php echo htmlspecialchars($transaction['amount']); ?> TND</strong>
                </div>
                
                <hr class="payment-divider">
                
                <div class="payment-line-item total">
                    <span>Total à Payer:</span>
                    <strong><?php echo htmlspecialchars($transaction['amount']); ?> TND</strong>
                </div>
                
                <form action="pay_process.php" method="POST" class="auth-form" style="margin-top: 20px;">
                    <input type="hidden" name="transaction_id" value="<?php echo $transaction_id; ?>">
                    <input type="hidden" name="project_id" value="<?php echo $transaction['project_id']; ?>">
                    
                    <p style="color: var(--light-text); text-align: center; font-size: 14px;">
                        Ceci est une simulation 🤖. Aucun argent réel ne sera débité.
                    </p>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary-solid btn-full">
                            <i class="fas fa-shield-alt"></i> Payer <?php echo htmlspecialchars($transaction['amount']); ?> TND (Simulé)
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</body>
</html>