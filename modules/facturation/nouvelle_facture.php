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

/* 🔒 ANTI DOUBLE SCAN (SESSION) */
if (!isset($_SESSION['last_scan'])) {
    $_SESSION['last_scan'] = [
        "code" => null,
        "time" => 0
    ];
}

/* ================= SCAN ================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);
    $now = time();

    // 🔥 BLOQUE DOUBLE SCAN
    if (
        $_SESSION['last_scan']['code'] === $code &&
        ($now - $_SESSION['last_scan']['time']) < 2
    ) {
        // on ignore
    } else {

        $_SESSION['last_scan'] = [
            "code" => $code,
            "time" => $now
        ];

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

    /* 🔥 ID PERSONNALISÉ */
    $dateJour = date("Ymd");
    $compteur = 1;

    foreach ($factures as $f) {
        if (strpos($f['id'], "FAC-$dateJour") === 0) {
            $compteur++;
        }
    }

    $idFacture = "FAC-$dateJour-" . str_pad($compteur, 3, "0", STR_PAD_LEFT);

    /* STOCK */
    foreach ($_SESSION['facture'] as $item) {
        mettreAJourStock($item['code_barre'], $item['quantite']);
    }

    /* SAVE */
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

<style>
body {
    background: #000;
    color: white;
    font-family: Arial;
    display: flex;
    justify-content: center;
}

.container {
    width: 1000px;
    margin-top: 30px;
    display: flex;
    gap: 20px;
}

.left {
    flex: 2;
    background: #111;
    padding: 15px;
    border-radius: 10px;
}

.right {
    flex: 1;
}

video {
    width: 250px;
    border: 2px solid orange;
}

.btn {
    background: orange;
    padding: 10px;
    border: none;
    margin: 5px;
    cursor: pointer;
}

.ticket {
    background: white;
    color: black;
    padding: 10px;
    font-family: monospace;
}
</style>
</head>

<body>

<div class="container">

<div class="left">

<h2>Nouvelle facture</h2>

<video id="video"></video><br>

<button class="btn" onclick="startScanner('/TP/modules/facturation/nouvelle_facture.php')">
Scanner
</button>

<button class="btn" onclick="stopScanner()">Stop</button>

<table border="1" width="100%">
<tr>
<th>Produit</th>
<th>Qté</th>
<th>Total</th>
</tr>

<?php foreach ($_SESSION['facture'] as $item): ?>
<tr>
<td><?= $item['nom'] ?></td>
<td><?= $item['quantite'] ?></td>
<td><?= $item['prix_unitaire_ht'] * $item['quantite'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<form method="POST">
<button name="valider_facture">Valider</button>
<button name="vider">Vider</button>
</form>

</div>

<div class="right">

<div class="ticket">

<h3>Ticket</h3>

<?php foreach ($_SESSION['facture'] as $item): ?>
<p><?= $item['nom'] ?> x<?= $item['quantite'] ?></p>
<?php endforeach; ?>

<hr>

<p>Total: <?= $total_ttc ?> CDF</p>

</div>

</div>

</div>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

</body>
</html>