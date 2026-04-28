<?php

function lireFactures() {

    if (!defined('DATA_FACTURES')) {
        die("DATA_FACTURES non défini");
    }

    if (!file_exists(DATA_FACTURES)) {
        return [];
    }

    $data = file_get_contents(DATA_FACTURES);
    return json_decode($data, true) ?? [];
}

function genererIdFacture() {
    return "FAC-" . date("Ymd") . "-" . rand(100, 999);
}

function calculerFacture($articles) {

    $total_ht = 0;
    $articles_calcules = [];

    foreach ($articles as $a) {

        $sous_total = $a['prix_unitaire_ht'] * $a['quantite'];

        $articles_calcules[] = [
            "code_barre" => $a['code_barre'],
            "nom" => $a['nom'],
            "prix_unitaire_ht" => $a['prix_unitaire_ht'],
            "quantite" => $a['quantite'],
            "sous_total_ht" => $sous_total
        ];

        $total_ht += $sous_total;
    }

    $tva = $total_ht * 0.18;
    $total_ttc = $total_ht + $tva;

    return [
        "articles" => $articles_calcules,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];
}

function enregistrerFacture($articles, $total_ht, $tva, $total_ttc) {

    if (!defined('DATA_FACTURES')) {
        die("DATA_FACTURES non défini");
    }

    $id = genererIdFacture();

    $file = DATA_FACTURES;

    $factures = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    if (!is_array($factures)) {
        $factures = [];
    }

    $factures[] = [
        "id_facture" => $id,
        "date" => date("Y-m-d H:i:s"),
        "articles" => $articles,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];

    file_put_contents($file, json_encode($factures, JSON_PRETTY_PRINT));

    return $id;
}

function trouverFacture($id) {

    $factures = lireFactures();

    foreach ($factures as $f) {
        if (($f['id_facture'] ?? null) === $id) {
            return $f;
        }
    }

    return null;
}