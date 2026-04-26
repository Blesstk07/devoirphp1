<?php

// ==============================
// CALCUL FACTURE (SAFE)
// ==============================

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


// ==============================
// LOG SYSTEM
// ==============================

function ajouterLog($action, $details) {

    $file = __DIR__ . '/../data/logs.json';

    $logs = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    $logs[] = [
        "date" => date("Y-m-d H:i:s"),
        "action" => $action,
        "details" => $details
    ];

    file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
}