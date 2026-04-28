<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

$fileFactures = __DIR__ . '/../../data/factures.json';

$factures = file_exists($fileFactures)
    ? json_decode(file_get_contents($fileFactures), true)
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
}

.info {
    text-align: center;
    margin-bottom: 20px;
    color: #555;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #111827;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.total {
    margin-top: 20px;
    text-align: right;
}

.actions {
    text-align: center;
    margin-top: 20px;
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
</style>
</head>

<body>

<div class="facture">

    <h1>FACTURE</h1>

    <div class="info">
        <p><strong>ID :</strong> <?= htmlspecialchars($facture['id']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($facture['date']) ?></p>
        <p><strong>Caissier :</strong> <?= htmlspecialchars($facture['caissier']) ?></p>
    </div>

    <table>
        <tr>
            <th>Produit</th>
            <th>Prix</th>
            <th>Qté</th>
            <th>Sous-total</th>
        </tr>

        <?php foreach ($facture['articles'] as $a): ?>

            <?php
                $prix = $a['prix_unitaire_ht'] ?? 0;
                $qte = $a['quantite'] ?? 0;

                // 🔥 IMPORTANT : utiliser sous_total déjà calculé
                $sous_total = $a['sous_total_ht'] ?? ($prix * $qte);
            ?>

            <tr>
                <td><?= htmlspecialchars($a['nom']) ?></td>
                <td><?= $prix ?> CDF</td>
                <td><?= $qte ?></td>
                <td><?= $sous_total ?> CDF</td>
            </tr>

        <?php endforeach; ?>

    </table>

    <div class="total">
        <p><strong>Total HT :</strong> <?= $facture['total_ht'] ?> CDF</p>
        <p><strong>TVA :</strong> <?= $facture['tva'] ?> CDF</p>
        <h3>Total TTC : <?= $facture['total_ttc'] ?> CDF</h3>
    </div>

    <div class="actions">
        <button class="print" onclick="window.print()">🖨 Imprimer</button>
        <a class="back" href="../../index.php">Retour</a>
    </div>

</div>

</body>
</html>