<?php
require_once('../auth/session.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

$file = '../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

$aujourdhui = date("Y-m-d");

$ventesJour = [];
$totalJour = 0;

foreach ($factures as $f) {

    $dateFacture = substr($f['date'], 0, 10);

    if ($dateFacture === $aujourdhui) {
        $ventesJour[] = $f;
        $totalJour += $f['total_ttc'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Journalier</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>📊 Rapport Journalier</h1>
<h3>Date : <?= $aujourdhui ?></h3>

<p><strong>Total ventes du jour :</strong> <?= $totalJour ?> CDF</p>

<table border="1" width="100%">
    <tr>
        <th>ID Facture</th>
        <th>Date</th>
        <th>Total TTC</th>
    </tr>

    <?php foreach ($ventesJour as $f): ?>
        <tr>
            <td><?= $f['id'] ?></td>
            <td><?= $f['date'] ?></td>
            <td><?= $f['total_ttc'] ?> CDF</td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>