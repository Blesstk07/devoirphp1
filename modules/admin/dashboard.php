<?php

require_once('../auth/session.php');
require_once('../includes/fonctions-produits.php');
require_once('../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

$produits = lireProduits();
$factures = lireFactures();

$nb_produits = count($produits);
$nb_factures = count($factures);

$total_ventes = 0;
$total_stock = 0;

foreach ($factures as $f) {
    $total_ventes += $f['total_ttc'];
}

foreach ($produits as $p) {
    $total_stock += $p['quantite_stock'];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>
body {
    background: #000;
    color: white;
    font-family: Arial;
}

.container {
    text-align: center;
    margin-top: 60px;
}

h1 {
    color: red;
}

.cards {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.card {
    background: #111;
    padding: 20px;
    width: 200px;
    border-radius: 10px;
    border: 1px solid red;
}

.value {
    font-size: 22px;
    margin-top: 10px;
    color: #ff4444;
}
</style>

</head>

<body>

<div class="container">

<h1>📊 Dashboard Admin</h1>

<div class="cards">

<div class="card">
    <h3>Produits</h3>
    <div class="value"><?= $nb_produits ?></div>
</div>

<div class="card">
    <h3>Factures</h3>
    <div class="value"><?= $nb_factures ?></div>
</div>

<div class="card">
    <h3>Ventes</h3>
    <div class="value"><?= $total_ventes ?> CDF</div>
</div>

<div class="card">
    <h3>Stock total</h3>
    <div class="value"><?= $total_stock ?></div>
</div>

</div>

</body>
</html>