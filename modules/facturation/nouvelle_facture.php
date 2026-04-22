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

/* ================= SCAN ================= */
if (!empty($_GET['code'])) {

    $code = trim($_GET['code']);
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

/* ================= CALCUL ================= */
$result = calculerFacture($_SESSION['facture']);

$total_ht = $result['total_ht'];
$tva = $result['tva'];
$total_ttc = $result['total_ttc'];

/* ================= VALIDATION ================= */
if (isset($_POST['valider_facture'])) {

    $factures = file_exists('../../data/factures.json')
        ? json_decode(file_get_contents('../../data/factures.json'), true)
        : [];

    $idFacture = uniqid("FAC-");

    foreach ($_SESSION['facture'] as $item) {
        mettreAJourStock($item['code_barre'], $item['quantite']);
        ajouterLog("VENTE", [
            "produit" => $item['nom'],
            "quantite" => $item['quantite'],
            "facture" => $idFacture
        ]);
    }

    $factures[] = [
        "id" => $idFacture,
        "date" => date("Y-m-d H:i:s"),
        "articles" => $result['articles'],
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc,
        "caissier" => $_SESSION['user']['identifiant']
    ];

    file_put_contents('../../data/factures.json', json_encode($factures, JSON_PRETTY_PRINT));

    $_SESSION['facture'] = [];

    header("Location: afficher-facture.php?id=" . $idFacture);
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
    padding: 20px;
}

h1 {
    text-align: center;
    color: #ff6600;
}

table {
    width: 100%;
    background: #111;
    border-collapse: collapse;
}

th {
    background: #ff6600;
    padding: 10px;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #333;
}

.btn {
    background: linear-gradient(45deg,#ff6600,#ff3300);
    color: white;
    padding: 10px;
    border-radius: 8px;
    border: none;
}

.btn:hover {
    box-shadow: 0 0 15px #ff6600;
}
</style>

</head>

<body>

<h1>🧾 Nouvelle facture</h1>

<h3>📷 Scanner produit</h3>

<video id="video" width="300"></video>

<button class="btn" onclick="startScanner('/TP/modules/facturation/nouvelle_facture.php')">
    🎥 Activer caméra
</button>

<button onclick="stopScanner()" style="background:red;color:white;">
    ⛔ Stop caméra
</button>

<hr>

<table>

<tr>
    <th>Produit</th>
    <th>Prix</th>
    <th>Qté</th>
    <th>Total</th>
    <th>Action</th>
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
    <td><a href="?delete=<?= $i ?>">❌</a></td>
</tr>

<?php endforeach; ?>

</table>

<h3>Total HT : <?= $total_ht ?></h3>
<h3>TVA : <?= $tva ?></h3>
<h2>Total TTC : <?= $total_ttc ?></h2>

<form method="POST">
    <button class="btn" type="submit" name="valider_facture">
        💾 Valider facture
    </button>
</form>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/TP/assets/js/scanner.js"></script>

</body>
</html>