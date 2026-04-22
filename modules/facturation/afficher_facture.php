<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

$file = '../../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../../index.php");
    exit;
}

$facture = null;

foreach ($factures as $f) {
    if ($f['id'] === $id) {
        $facture = $f;
        break;
    }
}

if (!$facture) {
    echo "<h2 style='text-align:center;color:red;'>❌ Facture introuvable</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= htmlspecialchars($facture['id']) ?></title>

    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>

        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .facture {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #111827;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
        }

        tr:hover {
            background: #f3f4f6;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .total h3 {
            color: #111827;
        }

        .actions {
            margin-top: 20px;
            text-align: center;
        }

        button, a {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            margin: 5px;
        }

        .print {
            background: #2563eb;
            color: white;
        }

        .back {
            background: #111827;
            color: white;
        }

        .print:hover {
            background: #1d4ed8;
        }

        .back:hover {
            background: #374151;
        }

    </style>
</head>

<body>

<div class="facture">

    <h1>🧾 FACTURE DE VENTE</h1>

    <div class="info">
        <p><strong>ID :</strong> <?= htmlspecialchars($facture['id']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($facture['date']) ?></p>
        <p><strong>Caissier :</strong> <?= htmlspecialchars($facture['caissier']) ?></p>
    </div>

    <table>

        <tr>
            <th>Désignation</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Sous-total</th>
        </tr>

        <?php if (!empty($facture['articles'])): ?>

            <?php foreach ($facture['articles'] as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nom']) ?></td>
                    <td><?= $a['prix_unitaire_ht'] ?></td>
                    <td><?= $a['quantite'] ?></td>
                    <td><?= $a['prix_unitaire_ht'] * $a['quantite'] ?></td>
                </tr>
            <?php endforeach; ?>

        <?php endif; ?>

    </table>

    <div class="total">
        <p><strong>Total HT :</strong> <?= $facture['total_ht'] ?> CDF</p>
        <p><strong>TVA :</strong> <?= $facture['tva'] ?> CDF</p>
        <h3>Net à payer : <?= $facture['total_ttc'] ?> CDF</h3>
    </div>

    <div class="actions">
        <button class="print" onclick="window.print()">🖨 Imprimer</button>
        <a class="back" href="../../index.php">⬅ Retour</a>
    </div>

</div>

</body>
</html>