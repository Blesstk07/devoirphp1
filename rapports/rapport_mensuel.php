<?php
require_once('../auth/session.php');

verifierConnexion();
verifierRole(['super_admin','manager']);

$file = '../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($factures)) {
    $factures = [];
}

$moisActuel = date("Y-m");

$ventesMois = [];
$totalMois = 0;
$nbFactures = 0;

$caissiers = [];
$produitsVendues = [];

foreach ($factures as $f) {

    $dateFacture = substr($f['date'], 0, 7);

    if ($dateFacture === $moisActuel) {

        $ventesMois[] = $f;
        $totalMois += $f['total_ttc'] ?? 0;
        $nbFactures++;

        $caissiers[] = $f['caissier'] ?? 'inconnu';

        foreach ($f['articles'] ?? [] as $a) {
            $produitsVendues[] = $a['nom'] ?? '';
        }
    }
}

$caissiers = array_unique($caissiers);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport mensuel</title>

    <link rel="stylesheet" href="/TP/assets/css/style.css">

    <style>

        body {
            background: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .box {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,102,0,0.2);
        }

        h1 {
            text-align: center;
            color: #ff6600;
        }

        p {
            text-align: center;
            color: #bbb;
        }

        /* ================= STATS ================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .card {
            background: #111;
            border: 1px solid rgba(255,102,0,0.3);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px #ff6600;
        }

        .card h2 {
            color: #ff6600;
        }

        /* ================= TABLE ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #111;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #ff6600;
            color: #000;
            padding: 12px;
        }

        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #222;
            color: #fff;
        }

        tr:hover {
            background: rgba(255,102,0,0.08);
        }

        /* ================= BUTTON ================= */
        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #ff6600;
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .back:hover {
            background: #ff3300;
            color: #fff;
        }

    </style>

</head>

<body>

<div class="container">

<div class="box">

    <h1>📊 Rapport mensuel</h1>
    <p>Mois : <strong><?= $moisActuel ?></strong></p>

    <!-- STATS -->
    <div class="stats">

        <div class="card">
            <h2><?= $nbFactures ?></h2>
            <p>Factures</p>
        </div>

        <div class="card">
            <h2><?= number_format($totalMois, 0, ',', ' ') ?> CDF</h2>
            <p>Chiffre d’affaires</p>
        </div>

        <div class="card">
            <h2><?= count($caissiers) ?></h2>
            <p>Caissiers actifs</p>
        </div>

    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Caissier</th>
            <th>Total TTC</th>
        </tr>

        <?php if (!empty($ventesMois)): ?>

            <?php foreach ($ventesMois as $f): ?>

                <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= $f['date'] ?></td>
                    <td><?= $f['caissier'] ?? 'N/A' ?></td>
                    <td><?= number_format($f['total_ttc'] ?? 0, 0, ',', ' ') ?> CDF</td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="4">Aucune vente ce mois</td>
            </tr>

        <?php endif; ?>

    </table>

    <a class="back" href="/TP/index.php">⬅ Retour dashboard</a>

</div>

</div>

</body>
</html>