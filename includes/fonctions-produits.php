<?php

require_once(__DIR__ . '/../config/config.php');

/* =========================
   LIRE PRODUITS
========================= */
function lireProduits() {

    if (!file_exists(DATA_PRODUITS)) {
        return [];
    }

    $data = file_get_contents(DATA_PRODUITS);
    return json_decode($data, true) ?? [];
}

/* =========================
   TROUVER PRODUIT PAR CODE BARRE
========================= */
function trouverProduit($code) {

    $produits = lireProduits();

    foreach ($produits as $p) {
        if ($p['code_barre'] === $code) {
            return $p;
        }
    }

    return null;
}

/* =========================
   VERIFIER EXPIRATION
========================= */
function produitExpire($produit) {

    if (!isset($produit['date_expiration'])) {
        return false;
    }

    return strtotime($produit['date_expiration']) < time();
}

/* =========================
   DECREMENTER STOCK
========================= */
function diminuerStock($code, $quantite) {

    $produits = lireProduits();

    foreach ($produits as &$p) {

        if ($p['code_barre'] === $code) {

            $p['quantite_stock'] -= $quantite;

            if ($p['quantite_stock'] < 0) {
                $p['quantite_stock'] = 0;
            }

            break;
        }
    }

    file_put_contents(DATA_PRODUITS, json_encode($produits, JSON_PRETTY_PRINT));
}