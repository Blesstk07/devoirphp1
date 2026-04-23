<?php
require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');

verifierConnexion();

/* =========================
LECTURE PRODUITS 
========================= */
$produits = lireProduits();

if (!is_array($produits)) {
    $produits = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste produits</title>

<link rel="stylesheet" href="/TP/assets/css/style.css">

<style>
body {
    font-family: Arial;
    background: #000;
    margin: 0;
    padding: 20px;
    color: white;
}

.container {
    max-width: 1100px;
    margin: auto;
}

h2 {
    text-align: center;
    color: #ff6600;
    margin-bottom: 20px;
}

.box {
    background: #111;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(255,102,0,0.15);
}

/* BUTTONS */
.btn {
    display: inline-block;
    padding: 10px 15px;
    background: linear-gradient(45deg, #ff6600, #ff3300);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    margin-right: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 15px #ff6600;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #1a1a1a;
    border-radius: 10px;
    overflow: hidden;
}

th {
    background: #ff6600;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #333;
    text-align: center;
}

tr:hover {
    background: #222;
}

/* STOCK COLORS */
.danger { color: #ff3b3b; font-weight: bold; }
.warning { color: #ffb020; font-weight: bold; }
.ok { color: #00ff88; font-weight: bold; }

/* TOP */
.top {
    margin-bottom: 15px;
    text-align: center;
}

/* DEBUG */
.debug {
    background: #222;
    padding: 10px;
    margin-top: 10px;
    font-size: 12px;
    color: #aaa;
}
</style>

</head>

<body>

<div class="container">

<div class="box">

<h2>📦 Liste des produits</h2>

<div class="top">
    <a href="/TP/index.php" class="btn">🏠 Dashboard</a>
    <a href="enregistrer.php" class="btn">➕ Ajouter produit</a>
</div>

<table>

<tr>
    <th>Code barre</th>
    <th>Nom</th>
    <th>Prix HT</th>
    <th>Stock</th>
</tr>

<?php if (!empty($produits)): ?>

    <?php foreach ($produits as $p): ?>

        <?php
        $code = $p['code_barre'] ?? '';
        $nom = $p['nom'] ?? '';
        $prix = $p['prix_unitaire_ht'] ?? 0;
        $stock = $p['quantite_stock'] ?? 0;
        ?>

        <tr>
            <td><?= htmlspecialchars($code) ?></td>
            <td><?= htmlspecialchars($nom) ?></td>
            <td><?= number_format($prix, 0, ',', ' ') ?> CDF</td>

            <td>
                <?php if ($stock <= 0): ?>
                    <span class="danger">Rupture</span>

                <?php elseif ($stock <= 5): ?>
                    <span class="warning"><?= $stock ?></span>

                <?php else: ?>
                    <span class="ok"><?= $stock ?></span>
                <?php endif; ?>
            </td>
        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="4">Aucun produit disponible</td>
    </tr>

<?php endif; ?>

</table>

<!-- DEBUG -->
<div class="debug">
    Nb produits : <?= count($produits) ?>
</div>

</div>

</div>

</body>
</html>