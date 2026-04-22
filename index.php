<?php
require_once('auth/session.php');
require_once('includes/fonctions-produits.php');
require_once('includes/fonctions-factures.php');

verifierConnexion();

$user = $_SESSION['user'];

// =========================
// DONNÉES
// =========================
$produits = lireProduits();

$factures = file_exists('data/factures.json')
    ? json_decode(file_get_contents('data/factures.json'), true)
    : [];

// =========================
// STATS GÉNÉRALES
// =========================
$nbProduits = count($produits);
$nbFactures = count($factures);

$totalVentes = 0;
$ventesAujourdhui = 0;
$aujourdhui = date("Y-m-d");

$derniereFacture = null;

// =========================
// ANALYSE FACTURES
// =========================
foreach ($factures as $f) {

    $totalVentes += $f['total_ttc'];

    if (substr($f['date'], 0, 10) === $aujourdhui) {
        $ventesAujourdhui += $f['total_ttc'];
    }

    $derniereFacture = $f;
}

// =========================
// STOCK FAIBLE (ALERTE)
// =========================
$stockFaible = [];

foreach ($produits as $p) {
    if ($p['quantite_stock'] <= 5) {
        $stockFaible[] = $p;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }

        .container { max-width:1100px; margin:auto; }

        .cards { display:flex; gap:15px; }

        .card {
            flex:1;
            background:white;
            padding:15px;
            border-radius:10px;
            text-align:center;
        }

        .section {
            background:white;
            padding:15px;
            margin-top:20px;
            border-radius:10px;
        }

        .danger {
            background:#ffdddd;
            padding:10px;
            border-left:5px solid red;
        }

        .good {
            background:#ddffdd;
            padding:10px;
            border-left:5px solid green;
        }
    </style>
</head>

<body>

<div class="container">

    <h1> Dashboard Admin</h1>

    <p>Bienvenue <strong><?= $user['nom_complet'] ?></strong></p>

    <!-- STATS -->
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
            <h2><?= $totalVentes ?> CDF</h2>
            <p>Total ventes</p>
        </div>

        <div class="card">
            <h2><?= $ventesAujourdhui ?> CDF</h2>
            <p>Ventes du jour</p>
        </div>

    </div>

    <!-- STOCK FAIBLE -->
    <div class="section">
        <h2> Alertes stock faible</h2>

        <?php if (count($stockFaible) > 0): ?>

            <?php foreach ($stockFaible as $p): ?>
                <div class="danger">
                    <?= $p['nom'] ?> — Stock : <?= $p['quantite_stock'] ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="good">✔ Aucun stock faible</div>
        <?php endif; ?>
    </div>

    <!-- DERNIÈRE FACTURE -->
    <div class="section">
        <h2> Dernière facture</h2>

        <?php if ($derniereFacture): ?>
            <p><strong>ID :</strong> <?= $derniereFacture['id'] ?></p>
            <p><strong>Date :</strong> <?= $derniereFacture['date'] ?></p>
            <p><strong>Total :</strong> <?= $derniereFacture['total_ttc'] ?> CDF</p>
        <?php else: ?>
            <p>Aucune facture disponible</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>