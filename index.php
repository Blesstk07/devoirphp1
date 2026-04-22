<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('auth/session.php');
require_once('includes/fonctions-produits.php');
require_once('includes/fonctions-factures.php');

verifierConnexion();

$user = $_SESSION['user'];

$produits = lireProduits();
$produits = is_array($produits) ? $produits : [];

$factures = file_exists('data/factures.json')
    ? json_decode(file_get_contents('data/factures.json'), true)
    : [];
$factures = is_array($factures) ? $factures : [];

$nbProduits = count($produits);
$nbFactures = count($factures);

$totalVentes = 0;
$ventesJour = 0;
$today = date("Y-m-d");

$stockFaible = [];

foreach ($factures as $f) {
    $totalVentes += $f['total_ttc'] ?? 0;

    if (!empty($f['date']) && substr($f['date'], 0, 10) === $today) {
        $ventesJour += $f['total_ttc'] ?? 0;
    }
}

foreach ($produits as $p) {
    if (($p['quantite_stock'] ?? 0) <= 5) {
        $stockFaible[] = $p;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Caisse</title>

<style>
/* =======================
   GLOBAL
======================= */
body {
    margin: 0;
    font-family: Arial;
    background: #0f0f0f;
    color: white;
}

/* =======================
   LAYOUT
======================= */
.container {
    display: flex;
}

/* =======================
   SIDEBAR
======================= */
.sidebar {
    width: 250px;
    background: #1a1a1a;
    height: 100vh;
    padding: 20px;
    position: fixed;
}

.sidebar h2 {
    color: #ff6600;
}

.sidebar a {
    display: block;
    padding: 10px;
    margin: 5px 0;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: #ff6600;
}

/* =======================
   MAIN
======================= */
.main {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}

/* =======================
   CARDS
======================= */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.card {
    background: #1a1a1a;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 15px #ff6600;
}

/* =======================
   SECTION
======================= */
.section {
    background: #1a1a1a;
    padding: 15px;
    margin-top: 20px;
    border-radius: 12px;
}

/* =======================
   ALERTES
======================= */
.danger {
    background: rgba(255,0,0,0.2);
    border-left: 5px solid red;
    padding: 10px;
    margin: 5px 0;
}

.good {
    background: rgba(0,255,0,0.1);
    border-left: 5px solid green;
    padding: 10px;
}

/* =======================
   HEADER USER
======================= */
.topbar {
    background: #1a1a1a;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
}
</style>
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>💳 CAISSE</h2>

        <a href="index.php">🏠 Dashboard</a>
        <a href="modules/facturation/nouvelle_facture.php">🧾 Nouvelle facture</a>
        <a href="modules/produits/liste.php">📦 Produits</a>
        <a href="modules/produits/enregistrer.php">➕ Ajouter produit</a>
        <a href="rapports/rapport_journalier.php">📊 Journalier</a>
        <a href="rapports/rapport_mensuel.php">📈 Mensuel</a>

        <?php if ($user['role'] === 'super_admin'): ?>
            <a href="modules/admin/gestion_compte.php">⚙️ Admin</a>
        <?php endif; ?>

        <a href="auth/logout.php">🚪 Déconnexion</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="topbar">
            <h2>Bienvenue <?= htmlspecialchars($user['nom_complet']) ?></h2>
            <p><?= $user['role'] ?></p>
        </div>

        <!-- CARDS -->
        <div class="cards">

            <div class="card">
                <h2><?= $nbFactures ?></h2>
                <p>Factures</p>
            </div>

            <div class="card">
                <h2><?= $nbProduits ?></h2>
                <p>Produits</p>
            </div>

            <div class="card">
                <h2><?= number_format($totalVentes, 0, ',', ' ') ?> CDF</h2>
                <p>Total ventes</p>
            </div>

            <div class="card">
                <h2><?= number_format($ventesJour, 0, ',', ' ') ?> CDF</h2>
                <p>Ventes du jour</p>
            </div>

        </div>

        <!-- STOCK -->
        <div class="section">
            <h2>⚠️ Stock faible</h2>

            <?php if (!empty($stockFaible)): ?>
                <?php foreach ($stockFaible as $p): ?>
                    <div class="danger">
                        <?= $p['nom'] ?> — <?= $p['quantite_stock'] ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="good">✔ Aucun stock faible</div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>