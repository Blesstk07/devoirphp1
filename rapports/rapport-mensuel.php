<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'].'/TP/auth/session.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/TP/includes/fonctions-auth.php');

verifierConnexion();

/* Lecture des factures */
$factures = file_exists($_SERVER['DOCUMENT_ROOT'].'/TP/data/factures.json')
    ? json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/TP/data/factures.json'), true)
    : [];

if (!is_array($factures)) $factures = [];

$today = date('Y-m-d');
$total = 0;

foreach ($factures as $f) {

    if (!isset($f['date']) || !isset($f['total'])) continue;

    $date = substr($f['date'], 0, 10);

    if ($date === $today) {
        $total += (float)$f['total'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport Journalier</title>

<style>
body{
    background:#000;
    color:#fff;
    font-family:Arial;
    padding:20px;
}

.card{
    background:#111;
    padding:20px;
    border-radius:10px;
    border:1px solid #222;
    text-align:center;
}

h1{color:red;}
</style>

</head>
<body>

<h1>📊 Rapport journalier</h1>

<div class="card">
    <h2><?= number_format($total, 0, ',', ' ') ?> FC</h2>
    <p>Ventes du jour (<?= $today ?>)</p>
</div>

</body>
</html>