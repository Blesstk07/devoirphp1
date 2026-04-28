<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../includes/fonctions-produits.php');

/* =========================
   PARAMÈTRE CODE BARRE
========================= */
if (!isset($_GET['code'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Code barre manquant"
    ]);
    exit;
}

$code = trim($_GET['code']);

/* =========================
   RECHERCHE PRODUIT
========================= */
$produit = trouverProduit($code);

if (!$produit) {
    echo json_encode([
        "status" => "error",
        "message" => "Produit introuvable"
    ]);
    exit;
}

/* =========================
   VERIFICATION EXPIRATION
========================= */
if (produitExpire($produit)) {
    echo json_encode([
        "status" => "error",
        "message" => "Produit expiré",
        "produit" => $produit
    ]);
    exit;
}

/* =========================
   RETOUR SUCCÈS
========================= */
echo json_encode([
    "status" => "success",
    "produit" => [
        "code_barre" => $produit['code_barre'],
        "nom" => $produit['nom'],
        "prix_unitaire_ht" => $produit['prix_unitaire_ht'],
        "quantite_stock" => $produit['quantite_stock']
    ]
]);
exit;