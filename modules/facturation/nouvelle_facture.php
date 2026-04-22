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

/* =========================
    AJOUT PRODUIT (SCAN)
========================= */
if (isset($_GET['code'])) {

    $produit = trouverProduit($_GET['code']);

    if ($produit) {

        $trouve = false;

        foreach ($_SESSION['facture'] as &$item) {
            if ($item['code_barre'] === $produit['code_barre']) {
                $item['quantite']++;
                $trouve = true;
                break;
            }
        }

        if (!$trouve) {
            $_SESSION['facture'][] = [
                "code_barre" => $produit['code_barre'],
                "nom" => $produit['nom'],
                "prix_unitaire_ht" => $produit['prix_unitaire_ht'],
                "quantite" => 1
            ];
        }
    }
}

/* =========================
    UPDATE / DELETE PANIER
========================= */
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

if (isset($_GET['delete'])) {
    unset($_SESSION['facture'][$_GET['delete']]);
    $_SESSION['facture'] = array_values($_SESSION['facture']);
}

/* =========================
    STOCK UPDATE FUNCTION CALL
========================= */
function mettreAJourStock($code, $quantiteVendue) {

    $file = '../../data/produits.json';

    $produits = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    foreach ($produits as &$p) {

        if ($p['code_barre'] === $code) {

            $p['quantite_stock'] -= $quantiteVendue;

            if ($p['quantite_stock'] < 0) {
                $p['quantite_stock'] = 0;
            }
        }
    }

    file_put_contents($file, json_encode($produits, JSON_PRETTY_PRINT));
}
//  LOG VENTE
foreach ($_SESSION['facture'] as $item) {

    ajouterLog("VENTE", [
        "produit" => $item['nom'],
        "code_barre" => $item['code_barre'],
        "quantite" => $item['quantite'],
        "caissier" => $_SESSION['user']['identifiant']
    ]);
}

/* =========================
    VALIDATION FACTURE + STOCK
========================= */
if (isset($_POST['valider_facture'])) {

    $factures = file_exists('../../data/factures.json')
        ? json_decode(file_get_contents('../../data/factures.json'), true)
        : [];

    $result = calculerFacture($_SESSION['facture']);

    // 🔥 DECREMENT STOCK
    foreach ($_SESSION['facture'] as $item) {
        mettreAJourStock($item['code_barre'], $item['quantite']);
    }

    $idFacture = uniqid("FAC-");

    $factures[] = [
        "id" => $idFacture,
        "date" => date("Y-m-d H:i:s"),
        "articles" => $result['articles'],
        "total_ht" => $result['total_ht'],
        "tva" => $result['tva'],
        "total_ttc" => $result['total_ttc'],
        "caissier" => $_SESSION['user']['identifiant']
    ];

    file_put_contents('../../data/factures.json', json_encode($factures, JSON_PRETTY_PRINT));

    $_SESSION['facture'] = [];

    header("Location: afficher-facture.php?id=" . $idFacture);
    exit;
}

/* =========================
    CALCUL TEMPS RÉEL
========================= */
$result = calculerFacture($_SESSION['facture']);

$total_ht = $result['total_ht'];
$tva = $result['tva'];
$total_ttc = $result['total_ttc'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle facture</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1> Nouvelle facture</h1>

<!-- SCANNER -->
<h2> Scanner produit</h2>

<video id="video" width="300"></video>

<button onclick="stopScanner()"> Stop caméra</button>

<p id="result"></p>

<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="../../assets/js/scanner.js"></script>

<script>
window.onload = function () {
    startScanner("../../modules/facturation/nouvelle_facture.php");
};
</script>

<hr>

<!-- PANIER -->
<h2> Panier</h2>

<table border="1" width="100%">
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Qté</th>
        <th>Sous-total</th>
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
            <td><?= $item['sous_total_ht'] ?></td>
            <td><a href="?delete=<?= $i ?>">❌</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- TOTAL -->
<h3>HT : <?= $total_ht ?> CDF</h3>
<h3>TVA : <?= $tva ?> CDF</h3>
<h2>TTC : <?= $total_ttc ?> CDF</h2>

<!-- VALIDATION -->
<form method="POST">
    <button type="submit" name="valider_facture">💾 Valider facture</button>
</form>

</body>
</html>