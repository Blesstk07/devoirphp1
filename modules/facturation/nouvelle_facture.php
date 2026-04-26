<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

if (!isset($_SESSION['last_scan'])) {
    $_SESSION['last_scan'] = '';
}

/* ================= SCAN ================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);

    if ($_SESSION['last_scan'] !== $code) {

        $_SESSION['last_scan'] = $code;

        $produit = trouverProduit($code);

        if ($produit) {

            $found = false;

            foreach ($_SESSION['facture'] as &$item) {
                if ($item['code_barre'] === $produit['code_barre']) {
                    $item['quantite']++;
                    $found = true;
                    break;
                }
            }

            unset($item);

            if (!$found) {
                $_SESSION['facture'][] = [
                    "code_barre" => $produit['code_barre'],
                    "nom" => $produit['nom'],
                    "prix_unitaire_ht" => $produit['prix_unitaire_ht'],
                    "quantite" => 1
                ];
            }
        }
    }
}

$result = calculerFacture($_SESSION['facture']);
$total_ht = $result['total_ht'] ?? 0;
$tva = $result['tva'] ?? 0;
$total_ttc = $result['total_ttc'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle facture</title>

<style>

/* 🌑 BACKGROUND GLOBAL */
body {
    margin: 0;
    font-family: "Segoe UI", Arial;
    background: radial-gradient(circle at top, #1a1a1a, #000);
    color: white;
}

/* 📦 CONTAINER CENTRÉ */
.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    display: flex;
    gap: 20px;
}

/* 📌 GAUCHE */
.left {
    flex: 2;
    background: #111;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(255,102,0,0.2);
}

/* 📌 DROITE */
.right {
    flex: 1;
}

/* 🎥 CAMERA */
.camera-box {
    text-align: center;
    margin-bottom: 15px;
}

video {
    width: 320px;
    height: 240px;
    border-radius: 12px;
    border: 2px solid #ff6600;
    box-shadow: 0 0 15px rgba(255,102,0,0.4);
}

/* 🔘 BOUTONS */
.btn {
    background: linear-gradient(45deg, #ff6600, #ff3300);
    border: none;
    padding: 10px 15px;
    margin: 5px;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
}

.btn:hover {
    transform: scale(1.05);
}

.btn-danger {
    background: #ff1e1e;
}

/* 📊 TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    overflow: hidden;
    border-radius: 10px;
}

th {
    background: #ff6600;
    padding: 12px;
}

td {
    background: #1c1c1c;
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #333;
}

/* 🧾 TICKET */
.ticket {
    background: #fff;
    color: #000;
    padding: 20px;
    border-radius: 15px;
    font-family: monospace;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}

.ticket h3 {
    text-align: center;
}

.ticket hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 10px 0;
}

.ticket-line {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
}

.total {
    font-weight: bold;
    font-size: 16px;
}

</style>
</head>

<body>

<div class="container">

<!-- LEFT -->
<div class="left">

<h2 style="text-align:center;color:#ff6600;">🧾 Nouvelle facture</h2>

<div class="camera-box">

    <video id="video"></video>

    <div>
        <button class="btn" onclick="startScanner('/TP/modules/facturation/nouvelle_facture.php')">
            🎥 Activer caméra
        </button>

        <button class="btn btn-danger" onclick="stopScanner()">
            ⛔ Stop caméra
        </button>
    </div>

</div>

<table>
<tr>
    <th>Produit</th>
    <th>Prix</th>
    <th>Qté</th>
    <th>Total</th>
</tr>

<?php foreach ($_SESSION['facture'] as $i => $item): ?>
<tr>
    <td><?= htmlspecialchars($item['nom']) ?></td>
    <td><?= $item['prix_unitaire_ht'] ?></td>
    <td><?= $item['quantite'] ?></td>
    <td><?= $item['prix_unitaire_ht'] * $item['quantite'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

<!-- RIGHT -->
<div class="right">

<div class="ticket">

<h3>Super Marché CodeRunner</h3>
<p><?= date("d/m/Y H:i") ?></p>

<hr>

<?php foreach ($_SESSION['facture'] as $item): ?>
<div class="ticket-line">
    <span><?= $item['nom'] ?></span>
    <span><?= $item['quantite'] ?> x <?= $item['prix_unitaire_ht'] ?></span>
</div>
<?php endforeach; ?>

<hr>

<div class="ticket-line">
    <span>HT</span>
    <span><?= $total_ht ?></span>
</div>

<div class="ticket-line">
    <span>TVA</span>
    <span><?= $tva ?></span>
</div>

<div class="ticket-line total">
    <span>TOTAL</span>
    <span><?= $total_ttc ?> CDF</span>
</div>

<hr>

<p style="text-align:center;">Merci pour votre achat 🙏</p>

</div>

</div>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

</body>
</html>