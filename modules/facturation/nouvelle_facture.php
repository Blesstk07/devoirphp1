<?php
require_once('../../auth/session.php');
require_once('../../includes/fonctions-produits.php');
require_once('../../includes/fonctions-factures.php');

verifierConnexion();
verifierRole(['caissier', 'manager', 'super_admin']);

session_start();

// =========================
//  INITIALISER FACTURE
// =========================
if (!isset($_SESSION['facture'])) {
    $_SESSION['facture'] = [];
}

/* =========================
    ➕ AJOUT PRODUIT (SCAN)
========================= */
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
        echo "<p style='color:red;'>Produit introuvable </p>";
    }
}

/* =========================
    MODIFIER QUANTITÉ
========================= */
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

/* =========================
    SUPPRIMER PRODUIT
========================= */
if (isset($_GET['delete'])) {

    $index = $_GET['delete'];
    unset($_SESSION['facture'][$index]);

    $_SESSION['facture'] = array_values($_SESSION['facture']);
}

/* =========================
    CALCUL TOTAL FACTURE
========================= */
$total_ht = 0;

foreach ($_SESSION['facture'] as &$item) {
    $item['sous_total_ht'] = $item['prix_unitaire_ht'] * $item['quantite'];
    $total_ht += $item['sous_total_ht'];
}

$tva = $total_ht * 0.18;
$total_ttc = $total_ht + $tva;
?>