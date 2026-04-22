<?php
require_once('../../auth/session.php');

verifierConnexion();

/*
===============================
    FONCTION DE CALCUL FACTURE
===============================
*/

function calculerFacture($articles) {

    $total_ht = 0;

    foreach ($articles as &$item) {

        // sous-total par article
        $item['sous_total_ht'] = $item['prix_unitaire_ht'] * $item['quantite'];

        $total_ht += $item['sous_total_ht'];
    }

    $tva = $total_ht * 0.18;
    $total_ttc = $total_ht + $tva;

    return [
        "articles" => $articles,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];
}
?>