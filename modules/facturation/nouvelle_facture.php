<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

/* ================= INIT ================= */
if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

/* ================= SCAN ================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);
    $produit = trouverProduit($code);

    if ($produit) {

        $found = false;

        foreach ($_SESSION['facture'] as $index => $item) {

            if ($item['code_barre'] === $produit['code_barre']) {

                $_SESSION['facture'][$index]['quantite']++;
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
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $i = $_POST['index'];
    $q = (int)$_POST['quantite'];

    if ($q > 0) {
        $_SESSION['facture'][$i]['quantite'] = $q;
    } else {
        unset($_SESSION['facture'][$i]);
        $_SESSION['facture'] = array_values($_SESSION['facture']);
    }
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {

    unset($_SESSION['facture'][$_GET['delete']]);
    $_SESSION['facture'] = array_values($_SESSION['facture']);
}

/* ================= VIDER ================= */
if (isset($_POST['vider'])) {
    $_SESSION['facture'] = [];
}

/* ================= CALCUL ================= */
$result = calculerFacture($_SESSION['facture']);

$total_ht = $result['total_ht'];
$tva = $result['tva'];
$total_ttc = $result['total_ttc'];

/* ================= VALIDATION ================= */
if (isset($_POST['valider_facture'])) {

    $file = '../../data/factures.json';

    $factures = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    if (!is_array($factures)) $factures = [];

    /* ===== ID PERSONNALISÉ ===== */
    $dateJour = date("Ymd");
    $compteur = 1;

    foreach ($factures as $f) {
        if (strpos($f['id'], "FAC-$dateJour") === 0) {
            $compteur++;
        }
    }

    $idFacture = "FAC-$dateJour-" . str_pad($compteur, 3, "0", STR_PAD_LEFT);

    /* ===== STOCK ===== */
    foreach ($_SESSION['facture'] as $item) {
        mettreAJourStock($item['code_barre'], $item['quantite']);
    }

    /* ===== ENREGISTREMENT ===== */
    $factures[] = [
        "id" => $idFacture,
        "date" => date("Y-m-d H:i:s"),
        "articles" => $result['articles'],
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc,
        "caissier" => $_SESSION['user']['identifiant']
    ];

    file_put_contents($file, json_encode($factures, JSON_PRETTY_PRINT));

    $_SESSION['facture'] = [];

    header("Location: /TP/modules/facturation/afficher_facture.php?id=" . $idFacture);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouvelle facture</title>

<link rel="stylesheet" href="/TP/assets/css/style.css">

<style>
body {
    background: #000;
    color: white;
    font-family: Arial;
    display: flex;
    justify-content: center;
}

.container {
    max-width: 1000px;
    width: 100%;
    margin-top: 30px;
    display: flex;
    gap: 20px;
}

/* GAUCHE */
.left {
    flex: 2;
    background: #111;
    padding: 15px;
    border-radius: 10px;
}

/* DROITE */
.right {
    flex: 1;
}

/* CAMERA */
.camera-box {
    text-align: center;
    margin-bottom: 20px;
}

video {
    width: 250px;
    height: 200px;
    border: 2px solid #ff6600;
    border-radius: 10px;
}

/* BUTTON */
.btn {
    background: linear-gradient(45deg,#ff6600,#ff3300);
    color: white;
    padding: 10px;
    border-radius: 8px;
    border: none;
    margin: 5px;
    cursor: pointer;
}

.btn-danger {
    background: red;
}

/* TABLE */
table {
    width: 100%;
    background: #111;
    border-collapse: collapse;
}

th {
    background: #ff6600;
}

td, th {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #333;
}

/* TICKET */
.ticket {
    background: white;
    color: black;
    padding: 15px;
    border-radius: 10px;
    font-family: monospace;
}

.ticket-line {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
}

.total {
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">

<!-- GAUCHE -->
<div class="left">

<h1 style="color:#ff6600;text-align:center;">🧾 Nouvelle facture</h1>

<div class="camera-box">

    <video id="video"></video><br>

    <button class="btn" onclick="startScanner('/TP/modules/facturation/nouvelle_facture.php')">
        🎥 Activer
    </button>

    <button class="btn btn-danger" onclick="stopScanner()">
        ⛔ Stop
    </button>

</div>

<table>
<tr>
    <th>Produit</th>
    <th>Prix</th>
    <th>Qté</th>
    <th>Total</th>
    <th></th>
</tr>

<?php foreach ($_SESSION['facture'] as $i => $item): ?>
<tr>
    <td><?= $item['nom'] ?></td>
    <td><?= $item['prix_unitaire_ht'] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="index" value="<?= $i ?>">
            <input type="number" name="quantite" value="<?= $item['quantite'] ?>">
            <button name="update">✔</button>
        </form>
    </td>
    <td><?= $item['prix_unitaire_ht'] * $item['quantite'] ?></td>
    <td><a href="?delete=<?= $i ?>" style="color:red;">❌</a></td>
</tr>
<?php endforeach; ?>

</table>

<form method="POST">
    <button class="btn" name="valider_facture">💾 Valider</button>
    <button class="btn btn-danger" name="vider">🗑 Vider</button>
</form>

</div>

<!-- DROITE -->
<div class="right">

<div class="ticket">

<h3 style="text-align:center;">Super Marché</h3>
<p style="text-align:center;"><?= date("d/m/Y H:i") ?></p>

<hr>

<?php foreach ($_SESSION['facture'] as $item): ?>
<div class="ticket-line">
    <span><?= substr($item['nom'],0,10) ?></span>
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

</div>

</div>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

</body>
</html>