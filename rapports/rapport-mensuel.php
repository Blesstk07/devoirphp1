<?php

require_once('../includes/fonctions-factures.php');

$mois = $_GET['mois'] ?? date("Y-m");

$factures = lireFactures();

$total_ht = 0;
$total_tva = 0;
$total_ttc = 0;
$nb_factures = 0;

foreach ($factures as $f) {

    if (strpos($f['date'], $mois) === 0) {

        $total_ht += $f['total_ht'];
        $total_tva += $f['tva'];
        $total_ttc += $f['total_ttc'];
        $nb_factures++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport Mensuel</title>

<style>
body {
    background: #000;
    color: white;
    font-family: Arial;
}

.container {
    max-width: 600px;
    margin: auto;
    margin-top: 50px;
    background: #111;
    padding: 20px;
    border-radius: 10px;
}

h2 {
    text-align: center;
    color: red;
}

.box {
    margin: 10px 0;
    padding: 10px;
    background: #222;
    border-radius: 8px;
}
</style>

</head>

<body>

<div class="container">

<h2>📊 Rapport Mensuel</h2>

<p>Mois : <?= $mois ?></p>

<div class="box">Nombre de factures : <?= $nb_factures ?></div>
<div class="box">Total HT : <?= $total_ht ?> CDF</div>
<div class="box">TVA : <?= $total_tva ?> CDF</div>
<div class="box"><strong>Total TTC : <?= $total_ttc ?> CDF</strong></div>

</div>

</body>
</html>