<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');
require_once('../../modules/facturation/calcul.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

session_start();

if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

/* =========================
   SCAN PRODUIT
========================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);

    $produit = trouverProduit($code);

    if (!$produit) {
        die("❌ Produit introuvable");
    }

    if (produitExpire($produit)) {
        die("❌ Produit expiré");
    }

    $found = false;

    foreach ($_SESSION['facture'] as &$item) {

        if ($item['code_barre'] === $code) {
            $item['quantite']++;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['facture'][] = [
            "code_barre" => $produit['code_barre'],
            "nom" => $produit['nom'],
            "prix_unitaire_ht" => $produit['prix_unitaire_ht'],
            "quantite" => 1
        ];
    }
}

/* =========================
   VIDER FACTURE
========================= */
if (isset($_POST['vider'])) {
    $_SESSION['facture'] = [];
}

/* =========================
   VALIDER FACTURE
========================= */
if (isset($_POST['valider'])) {

    // 1. vérification stock + expiration
    foreach ($_SESSION['facture'] as $item) {

        $produit = trouverProduit($item['code_barre']);

        if (!$produit) {
            die("❌ Produit introuvable");
        }

        if (produitExpire($produit)) {
            die("❌ Produit expiré : " . $produit['nom']);
        }

        if ($produit['quantite_stock'] < $item['quantite']) {
            die("❌ Stock insuffisant : " . $produit['nom']);
        }
    }

    // 2. décrément stock
    foreach ($_SESSION['facture'] as $item) {
        diminuerStock($item['code_barre'], $item['quantite']);
    }

    // 3. calcul
    $result = calculerFacture($_SESSION['facture']);

    // 4. enregistrer facture
    $id = enregistrerFacture($result['articles'], $result['total_ht'], $result['tva'], $result['total_ttc']);

    // 5. reset session
    $_SESSION['facture'] = [];

    // 6. redirection
    header("Location: afficher-facture.php?id=" . $id);
    exit;
}

/* =========================
   CALCUL AFFICHAGE
========================= */
$result = calculerFacture($_SESSION['facture']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle facture</title>

<style>
body {
    background: #000;
    color: #fff;
    font-family: Arial;
}

.container {
    max-width: 1000px;
    margin: auto;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #ff3c3c;
}

video {
    width: 320px;
    border-radius: 10px;
    border: 2px solid red;
}

.btn {
    background: #ff3c3c;
    color: #fff;
    border: none;
    padding: 10px;
    margin: 5px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-danger {
    background: #8b0000;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th {
    background: #ff3c3c;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #333;
    text-align: center;
}

.total {
    margin-top: 10px;
}
</style>

</head>

<body>

<div class="container">

<h2>🧾 Nouvelle facture</h2>

<!-- CAMERA -->
<video id="video"></video>

<br>

<button class="btn" onclick="startScanner()">📷 Scanner</button>
<button class="btn btn-danger" onclick="stopScanner()">⛔ Stop</button>

<!-- TABLE -->
<table>
<tr>
    <th>Produit</th>
    <th>PU</th>
    <th>Qté</th>
    <th>Total</th>
</tr>

<?php foreach ($_SESSION['facture'] as $item): ?>
<tr>
    <td><?= $item['nom'] ?></td>
    <td><?= $item['prix_unitaire_ht'] ?></td>
    <td><?= $item['quantite'] ?></td>
    <td><?= $item['prix_unitaire_ht'] * $item['quantite'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<!-- TOTAL -->
<div class="total">
<p>HT : <?= $result['total_ht'] ?? 0 ?> CDF</p>
<p>TVA : <?= $result['tva'] ?? 0 ?> CDF</p>
<p><strong>TOTAL : <?= $result['total_ttc'] ?? 0 ?> CDF</strong></p>
</div>

<!-- ACTIONS -->
<form method="POST">
    <button name="valider" class="btn">✔ Valider facture</button>
    <button name="vider" class="btn btn-danger">🗑 Vider</button>
</form>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>

<script>
let scanner;
let active = false;

function startScanner() {

    if (active) return;

    active = true;
    scanner = new ZXing.BrowserBarcodeReader();

    scanner.decodeFromVideoDevice(null, "video", (result) => {

        if (result) {

            stopScanner();

            window.location.href =
                "nouvelle-facture.php?code=" + encodeURIComponent(result.text);
        }
    });
}

function stopScanner() {
    if (scanner) scanner.reset();
    active = false;
}
</script>

</body>
</html>