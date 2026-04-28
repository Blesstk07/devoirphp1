<?php

require_once('../../auth/session.php');
verifierConnexion();

session_start();

$fileProduits = '../../data/produits.json';
$fileFactures = __DIR__ . '/../../data/factures.json';

$produits = file_exists($fileProduits) ? json_decode(file_get_contents($fileProduits), true) : [];
$factures = file_exists($fileFactures) ? json_decode(file_get_contents($fileFactures), true) : [];

if (!is_array($produits)) $produits = [];
if (!is_array($factures)) $factures = [];

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$code = $_GET['code'] ?? '';
$action = $_GET['action'] ?? '';

/* ================= SCAN ================= */
$produitTrouve = null;

if ($code) {
    foreach ($produits as $p) {
        if ($p['code_barre'] === $code) {
            $produitTrouve = $p;
            break;
        }
    }

    if ($produitTrouve) {
        $_SESSION['panier'][] = $produitTrouve;
        header("Location: facturation.php");
        exit;
    }
}

/* ================= ACTIONS ================= */
if ($action === 'vider') {
    $_SESSION['panier'] = [];
    header("Location: facturation.php");
    exit;
}

if ($action === 'valider') {

    $panier = $_SESSION['panier'];

    if (count($panier) > 0) {

        $total = 0;

        /* ================= STOCK ================= */
        foreach ($panier as $item) {

            foreach ($produits as &$p) {

                if ($p['code_barre'] === $item['code_barre']) {

                    if (!isset($p['quantite_stock'])) {
                        $p['quantite_stock'] = 0;
                    }

                    $p['quantite_stock'] -= 1;

                    if ($p['quantite_stock'] < 0) {
                        $p['quantite_stock'] = 0;
                    }
                }
            }

            $total += $item['prix_unitaire_ht'];
        }

        file_put_contents($fileProduits, json_encode($produits, JSON_PRETTY_PRINT));

        /* ================= FACTURE ID ================= */
        $idFacture = "FAC-" . date("Ymd") . "-" . str_pad(count($factures) + 1, 4, "0", STR_PAD_LEFT);

        $facture = [
            "id" => $idFacture,
            "date" => date("Y-m-d H:i:s"),
            "produits" => $panier,
            "total" => $total
        ];

        $factures[] = $facture;

        file_put_contents($fileFactures, json_encode($factures, JSON_PRETTY_PRINT));

        $_SESSION['last_facture'] = $facture;
        $_SESSION['panier'] = [];

        header("Location: facturation.php?ticket=1");
        exit;
    }
}

$panier = $_SESSION['panier'];
$total = 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facturation</title>

<style>

/* ================= BASE ================= */
body {
    margin: 0;
    font-family: Arial;
    background: #000;
    color: #fff;
}

.container {
    width: 650px;
    margin: auto;
    margin-top: 20px;
}

/* ================= TITRE ================= */
h1 {
    text-align: center;
    color: red;
}

/* ================= BLOCS ================= */
.box {
    background: #111;
    border: 1px solid #222;
    padding: 15px;
    margin-bottom: 12px;
    border-radius: 10px;
}

/* ================= BUTTONS ================= */
button {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

/* COULEURS UNIQUES */
.red {
    background: red;
    color: white;
}

.white {
    background: white;
    color: black;
}

.dark {
    background: #111;
    color: white;
    border: 1px solid #333;
}

/* ================= CAMERA ================= */
video {
    width: 100%;
    border-radius: 10px;
    border: 2px solid red;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

td, th {
    border: 1px solid #333;
    padding: 10px;
    text-align: center;
}

th {
    background: red;
}

/* ================= TICKET ================= */
.ticket {
    background: white;
    color: black;
    padding: 20px;
    border-radius: 8px;
}

/* PRINT CLEAN */
@media print {

    body * {
        visibility: hidden;
    }

    .ticket, .ticket * {
        visibility: visible;
    }

    .ticket {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }
}

</style>

</head>

<body>

<div class="container">

<h1>🧾 FACTURATION</h1>

<!-- CAMERA -->
<div class="box">

<button class="dark" onclick="startCamera()">▶ Activer caméra</button>
<button class="red" onclick="stopCamera()">⛔ Désactiver caméra</button>

<video id="video"></video>

</div>

<!-- SCAN -->
<div class="box">

<form method="GET">
<input name="code" placeholder="Scanner code-barres"
style="width:100%;padding:10px;">
<button class="white">Scanner</button>
</form>

</div>

<!-- ACTIONS -->
<div class="box">

<a href="facturation.php?action=valider">
<button class="red">✔ Valider facture</button>
</a>

<a href="facturation.php?action=vider">
<button class="dark">🧹 Vider facture</button>
</a>

</div>

<!-- PRODUIT INCONNU -->
<?php if ($code && !$produitTrouve): ?>

<div class="box" style="text-align:center;">
<h3 style="color:red;">Produit introuvable</h3>

<a href="../produits/enregistrer.php?code=<?= $code ?>">
    <button class="white">➕ Ajouter produit</button>
</a>

</div>

<?php endif; ?>

<!-- PANIER -->
<div class="box">

<table>

<tr>
<th>Produit</th>
<th>Prix</th>
</tr>

<?php foreach ($panier as $p):
$total += $p['prix_unitaire_ht'];
?>

<tr>
<td><?= $p['nom'] ?></td>
<td><?= $p['prix_unitaire_ht'] ?> FC</td>
</tr>

<?php endforeach; ?>

<tr>
<th>Total</th>
<th><?= $total ?> FC</th>
</tr>

</table>

</div>

<!-- TICKET -->
<?php if (isset($_GET['ticket']) && isset($_SESSION['last_facture'])): ?>

<div class="ticket">

<h2>🧾 CAISSE PRO</h2>
<hr>

<p><strong>ID :</strong> <?= $_SESSION['last_facture']['id'] ?></p>
<p><strong>Date :</strong> <?= $_SESSION['last_facture']['date'] ?></p>

<hr>

<?php foreach ($_SESSION['last_facture']['produits'] as $p): ?>
<p><?= $p['nom'] ?> - <?= $p['prix_unitaire_ht'] ?> FC</p>
<?php endforeach; ?>

<hr>

<h2>Total : <?= $_SESSION['last_facture']['total'] ?> FC</h2>

</div>

<button class="red" onclick="window.print()">🖨 Imprimer ticket</button>

<?php endif; ?>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>

<script>

let codeReader;

function startCamera() {
    codeReader = new ZXing.BrowserMultiFormatReader();

    codeReader.decodeFromVideoDevice(null, 'video', (result) => {
        if (result) {
            window.location.href = "facturation.php?code=" + result.text;
            stopCamera();
        }
    });
}

function stopCamera() {
    if (codeReader) {
        codeReader.reset();
        codeReader = null;
    }
}

</script>

</body>
</html>