<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

// =========================
// CHARGER FACTURES
// =========================
$file = '../../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

// =========================
// ID FACTURE
// =========================
$id = $_GET['id'] ?? null;

$facture = null;

foreach ($factures as $f) {
    if ($f['id'] === $id) {
        $facture = $f;
        break;
    }
}

if (!$facture) {
    echo "<h2> Facture introuvable</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $facture['id'] ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        .facture-box {
            max-width: 700px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .print-btn {
            margin-top: 20px;
            text-align: center;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="facture-box">

    <h1> FACTURE DE VENTE</h1>

    <p><strong>ID :</strong> <?= $facture['id'] ?></p>
    <p><strong>Date :</strong> <?= $facture['date'] ?></p>
    <p><strong>Caissier :</strong> <?= $facture['caissier'] ?></p>

    <hr>

    <table>
        <tr>
            <th>Désignation</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Sous-total</th>
        </tr>

        <?php foreach ($facture['articles'] as $a): ?>
            <tr>
                <td><?= $a['nom'] ?></td>
                <td><?= $a['prix_unitaire_ht'] ?></td>
                <td><?= $a['quantite'] ?></td>
                <td><?= $a['prix_unitaire_ht'] * $a['quantite'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="total">
        <p><strong>Total HT :</strong> <?= $facture['total_ht'] ?> CDF</p>
        <p><strong>TVA (18%) :</strong> <?= $facture['tva'] ?> CDF</p>
        <h3><strong>Net à payer :</strong> <?= $facture['total_ttc'] ?> CDF</h3>
    </div>

    <div class="print-btn">
        <button onclick="window.print()"> Imprimer facture</button>
    </div>

</div>

</body>
</html>