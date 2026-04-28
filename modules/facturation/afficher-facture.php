<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-factures.php');


verifierRole(['caissier', 'manager', 'super_admin']);

/* =========================
   RECUP ID
========================= */
$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("❌ Aucune facture demandée");
}

/* =========================
   LECTURE FACTURES
========================= */
$file = '../../data/factures.json';

$factures = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

/* =========================
   RECHERCHE FACTURE
========================= */
$facture = null;

foreach ($factures as $f) {

    // 🔥 IMPORTANT : utiliser id_facture (format TP)
    if (isset($f['id_facture']) && trim($f['id_facture']) === trim($id)) {
        $facture = $f;
        break;
    }
}

if (!$facture) {
    die("❌ Facture introuvable<br>ID recherché : " . htmlspecialchars($id));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture</title>

<style>
body {
    background: #000;
    color: #fff;
    font-family: Arial;
    display: flex;
    justify-content: center;
}

.facture-box {
    background: #111;
    padding: 25px;
    margin-top: 30px;
    border-radius: 15px;
    width: 400px;
}

h2 {
    text-align: center;
    color: #ff3c3c;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #ff3c3c;
    padding: 8px;
}

td {
    padding: 8px;
    text-align: center;
    border-bottom: 1px solid #333;
}

.total {
    font-weight: bold;
}

.print-btn {
    width: 100%;
    padding: 10px;
    background: #ff3c3c;
    border: none;
    margin-top: 15px;
    color: white;
    cursor: pointer;
    border-radius: 8px;
}
</style>

</head>

<body>

<div class="facture-box">

<h2>🧾 Facture</h2>

<p><strong>ID :</strong> <?= $facture['id_facture'] ?></p>
<p><strong>Date :</strong> <?= $facture['date'] ?> <?= $facture['heure'] ?></p>
<p><strong>Caissier :</strong> <?= $facture['caissier'] ?></p>

<table>
<tr>
    <th>Produit</th>
    <th>PU</th>
    <th>Qté</th>
    <th>Total</th>
</tr>

<?php foreach ($facture['articles'] as $a): ?>
<tr>
    <td><?= $a['nom'] ?></td>
    <td><?= $a['prix_unitaire_ht'] ?></td>
    <td><?= $a['quantite'] ?></td>
    <td><?= $a['sous_total_ht'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<br>

<p class="total">HT : <?= $facture['total_ht'] ?> CDF</p>
<p class="total">TVA : <?= $facture['tva'] ?> CDF</p>
<p class="total">TOTAL : <?= $facture['total_ttc'] ?> CDF</p>

<button class="print-btn" onclick="window.print()">🖨️ Imprimer / PDF</button>

</div>

</body>
</html>