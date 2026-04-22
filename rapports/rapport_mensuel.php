<?php
require_once('../auth/session.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

$file = '../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

$moisActuel = date("Y-m");

$ventesMois = [];
$totalMois = 0;

foreach ($factures as $f) {

    $dateFacture = substr($f['date'], 0, 7);

    if ($dateFacture === $moisActuel) {
        $ventesMois[] = $f;
        $totalMois += $f['total_ttc'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1> Rapport Mensuel</h1>
<h3>Mois : <?= $moisActuel ?></h3>

<p><strong>Total ventes du mois :</strong> <?= $totalMois ?> CDF</p>

<table border="1" width="100%">
    <tr>
        <th>ID Facture</th>
        <th>Date</th>
        <th>Total TTC</th>
    </tr>

    <?php foreach ($ventesMois as $f): ?>
        <tr>
            <td><?= $f['id'] ?></td>
            <td><?= $f['date'] ?></td>
            <td><?= $f['total_ttc'] ?> CDF</td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>