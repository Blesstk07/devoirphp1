<?php
// ==============================
//  PRODUITS (VERSION PROPRE 🔥)
// ==============================

define('FICHIER_PRODUITS', __DIR__ . '/../data/produits.json');


/* =========================
   LIRE PRODUITS
========================= */
function lireProduits() {

    if (!file_exists(FICHIER_PRODUITS)) {
        file_put_contents(FICHIER_PRODUITS, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }

    $contenu = file_get_contents(FICHIER_PRODUITS);

    if (empty($contenu)) {
        return [];
    }

    $produits = json_decode($contenu, true);

    if (!is_array($produits)) {
        return [];
    }

    return $produits;
}


/* =========================
   SAUVEGARDER PRODUITS
========================= */
function sauvegarderProduits($produits) {

    if (!is_array($produits)) {
        return false;
    }

    return file_put_contents(
        FICHIER_PRODUITS,
        json_encode($produits, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}


/* =========================
   TROUVER PRODUIT
========================= */
function trouverProduit($code) {

    $produits = lireProduits();

    foreach ($produits as $p) {
        if (($p['code_barre'] ?? '') == $code) {
            return $p;
        }
    }

    return null;
}


/* =========================
   METTRE À JOUR STOCK
========================= */
function mettreAJourStock($code, $quantiteVendue) {

    $produits = lireProduits();

    foreach ($produits as &$p) {

        if (($p['code_barre'] ?? '') == $code) {

            $stock = $p['quantite_stock'] ?? 0;

            $p['quantite_stock'] = max(0, $stock - $quantiteVendue);

            break;
        }
    }

    sauvegarderProduits($produits);
}