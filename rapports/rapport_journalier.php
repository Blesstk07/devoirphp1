<?php
require_once('../auth/session.php');
require_once('../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['manager', 'super_admin', 'caissier']);

//  date du jour
$aujourdhui = date("Y-m-d");

//  récupérer les factures
$factures = lireFactures();

$total_ventes = 0;
$total_tva = 0;
$nb_factures = 0;

$factures_du_jour = [];

//  filtrage
foreach ($factures as $f) {

    if ($f['date'] === $aujourdhui) {

        $factures_du_jour[] = $f;

        $total_ventes += $f['total_ttc'];
        $total_tva += $f['tva'];
        $nb_factures++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport journalier</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1> Rapport journalier</h1>

<p><strong>Date :</strong> <?= $aujourdhui ?></p>

<hr>

<h3> Nombre de factures : <?= $nb_factures ?></h3>
<h3> Total TVA : <?= $total_tva ?> CDF</h3>
<h2> Total ventes : <?= $total_ventes ?> CDF</h2>

<hr>

<h2> Détail des factures</h2>

<table border="1">
    <tr>
        <th>ID Facture</th>
        <th>Heure</th>
        <th>Total TTC</th>
        <th>Caissier</th>
    </tr>

    <?php foreach ($factures_du_jour as $f): ?>
    <tr>
        <td><?= $f['id_facture'] ?></td>
        <td><?= $f['heure'] ?></td>
        <td><?= $f['total_ttc'] ?></td>
        <td><?= $f['caissier'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>