<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

// =========================
// SESSION FACTURE
// =========================
if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

// =========================
// AJOUT PRODUIT (SCAN)
// =========================
if (isset($_GET['code'])) {

    $code = $_GET['code'];
    $produit = trouverProduit($code);

    if ($produit) {

        $existe = false;

        foreach ($_SESSION['facture'] as &$item) {
            if ($item['code_barre'] === $code) {
                $item['quantite'] += 1;
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $_SESSION['facture'][] = [
                "code_barre" => $produit['code_barre'],
                "nom" => $produit['nom'],
                "prix_unitaire_ht" => $produit['prix_unitaire_ht'],
                "quantite" => 1
            ];
        }

    } else {
        echo "<p style='color:red;'>Le produit est introuvable </p>";
    }
}

// =========================
// MODIFIER QUANTITÉ
// =========================
if (isset($_POST['update'])) {

    $index = $_POST['index'];
    $quantite = (int)$_POST['quantite'];

    if ($quantite > 0) {
        $_SESSION['facture'][$index]['quantite'] = $quantite;
    } else {
        unset($_SESSION['facture'][$index]);
        $_SESSION['facture'] = array_values($_SESSION['facture']);
    }
}

// =========================
// SUPPRIMER PRODUIT
// =========================
if (isset($_GET['delete'])) {

    $index = $_GET['delete'];
    unset($_SESSION['facture'][$index]);
    $_SESSION['facture'] = array_values($_SESSION['facture']);
}

// =========================
// VALIDATION FACTURE (SAUVEGARDE)
// =========================
if (isset($_POST['valider_facture'])) {

    $factures = file_exists('../../data/factures.json')
        ? json_decode(file_get_contents('../../data/factures.json'), true)
        : [];

    $total_ht = 0;

    foreach ($_SESSION['facture'] as $item) {
        $total_ht += $item['prix_unitaire_ht'] * $item['quantite'];
    }

    $tva = $total_ht * 0.18;
    $total_ttc = $total_ht + $tva;

    $factures[] = [
        "id" => uniqid(),
        "date" => date("Y-m-d H:i:s"),
        "articles" => $_SESSION['facture'],
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc,
        "caissier" => $_SESSION['user']['identifiant']
    ];

    file_put_contents('../../data/factures.json', json_encode($factures, JSON_PRETTY_PRINT));

    // VIDER PANIER
    $_SESSION['facture'] = [];

    echo "<script>alert('Facture validée avec succès');</script>";
}

// =========================
// CALCUL EN TEMPS RÉEL
// =========================
$total_ht = 0;

foreach ($_SESSION['facture'] as &$item) {
    $item['sous_total_ht'] = $item['prix_unitaire_ht'] * $item['quantite'];
    $total_ht += $item['sous_total_ht'];
}

$tva = $total_ht * 0.18;
$total_ttc = $total_ht + $tva;
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
<h2> Scanner le produit</h2>

<video id="video" width="300"></video>

<button onclick="stopScanner()">Stopper la caméra</button>

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
<h2> Articles</h2>

<table border="1" width="100%">
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Sous-total</th>
        <th>Action</th>
    </tr>

    <?php foreach ($_SESSION['facture'] as $index => $item): ?>
        <tr>
            <td><?= $item['nom'] ?></td>
            <td><?= $item['prix_unitaire_ht'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1">
                    <button name="update">✔</button>
                </form>
            </td>
            <td><?= $item['sous_total_ht'] ?></td>
            <td>
                <a href="?delete=<?= $index ?>">❌</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- TOTAL -->
<h3>Total HT : <?= $total_ht ?> CDF</h3>
<h3>TVA (18%) : <?= $tva ?> CDF</h3>
<h2>Total TTC : <?= $total_ttc ?></h2>

<!-- VALIDATION -->
<form method="POST">
    <button type="submit" name="valider_facture">
        Valider la facture
    </button>
</form>

</body>
</html>