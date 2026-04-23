<?php
require_once('../includes/fonctions-factures.php');

/* =========================
    DONNÉES DE TEST
========================= */

$articles = [
    [
        "nom" => "Produit A",
        "prix_unitaire_ht" => 1200,
        "quantite" => 2
    ],
    [
        "nom" => "Produit B",
        "prix_unitaire_ht" => 500,
        "quantite" => 3
    ]
];

/* =========================
    CALCUL FACTURE
========================= */

$resultat = calculerFacture($articles);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Test Calcul Facture</title>

<style>
body {
    font-family: Arial;
    background: #0f0f0f;
    color: white;
    padding: 20px;
}

.box {
    max-width: 600px;
    margin: auto;
    background: #1a1a1a;
    padding: 20px;
    border-radius: 10px;
}

h1 {
    color: #ff6600;
    text-align: center;
}

pre {
    background: #000;
    padding: 15px;
    border-radius: 8px;
    overflow-x: auto;
    border: 1px solid #333;
}

.result {
    margin-top: 20px;
    background: #111;
    padding: 15px;
    border-radius: 8px;
}

.line {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.total {
    font-weight: bold;
    font-size: 18px;
    color: #ff6600;
}
</style>

</head>

<body>

<div class="box">

    <h1>Test Calcul Facture</h1>

    <h3> Articles</h3>
    <pre><?php print_r($articles); ?></pre>

    <h3> Résultat brut</h3>
    <pre><?php print_r($resultat); ?></pre>

    <div class="result">

        <h3> Résumé</h3>

        <div class="line">
            <span>Total HT :</span>
            <span><?= number_format($resultat['total_ht'], 0, ',', ' ') ?> CDF</span>
        </div>

        <div class="line">
            <span>TVA :</span>
            <span><?= number_format($resultat['tva'], 0, ',', ' ') ?> CDF</span>
        </div>

        <div class="line total">
            <span>Total TTC :</span>
            <span><?= number_format($resultat['total_ttc'], 0, ',', ' ') ?> CDF</span>
        </div>

    </div>

</div>

</body>
</html>