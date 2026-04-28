<?php

<<<<<<< HEAD
// ==============================
// CALCUL FACTURE (SAFE)
// ==============================
=======
require_once(__DIR__ . '/../config/config.php');
>>>>>>> 2c5154d (modif 39)

/* =========================
   LIRE FACTURES
========================= */
function lireFactures() {

    if (!file_exists(DATA_FACTURES)) {
        return [];
    }

    $data = file_get_contents(DATA_FACTURES);
    return json_decode($data, true) ?? [];
}

/* =========================
   GENERER ID FACTURE
========================= */
function genererIdFacture() {

    return PREFIX_FACTURE . date("Ymd") . "-" . rand(100, 999);
}

/* =========================
   CALCUL FACTURE
========================= */
function calculerFacture($articles) {

    $total_ht = 0;

    foreach ($articles as &$a) {

<<<<<<< HEAD
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
=======
        $a['sous_total_ht'] = $a['prix_unitaire_ht'] * $a['quantite'];
        $total_ht += $a['sous_total_ht'];
    }

    $tva = $total_ht * TVA;
>>>>>>> 2c5154d (modif 39)
    $total_ttc = $total_ht + $tva;

    return [
        "articles" => $articles,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
    ];
}

/* =========================
   ENREGISTRER FACTURE
========================= */
function enregistrerFacture($articles, $total_ht, $tva, $total_ttc) {

<<<<<<< HEAD
// ==============================
// LOG SYSTEM
// ==============================
=======
    $factures = lireFactures();
>>>>>>> 2c5154d (modif 39)

    $id = genererIdFacture();

<<<<<<< HEAD
    $file = __DIR__ . '/../data/logs.json';

    $logs = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    $logs[] = [
        "date" => date("Y-m-d H:i:s"),
        "action" => $action,
        "details" => $details
=======
    $factures[] = [
        "id_facture" => $id,
        "date" => date(FORMAT_DATE),
        "heure" => date(FORMAT_HEURE),
        "caissier" => $_SESSION['user']['nom_complet'] ?? 'inconnu',
        "articles" => $articles,
        "total_ht" => $total_ht,
        "tva" => $tva,
        "total_ttc" => $total_ttc
>>>>>>> 2c5154d (modif 39)
    ];

    file_put_contents(DATA_FACTURES, json_encode($factures, JSON_PRETTY_PRINT));

    return $id;
}

/* =========================
   TROUVER FACTURE
========================= */
function trouverFacture($id) {

    $factures = lireFactures();

    foreach ($factures as $f) {
        if ($f['id_facture'] === $id) {
            return $f;
        }
    }

    return null;
}