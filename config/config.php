<?php

/* =========================
   CONFIGURATION GLOBALE
========================= */

/* 📊 TVA du projet (TP = 18%) */
define("TVA", 0.18);

/* 📁 CHEMINS DES DONNÉES */
define("DATA_PRODUITS", __DIR__ . "/../data/produits.json");
define("DATA_FACTURES", __DIR__ . "/../data/factures.json");
define("DATA_UTILISATEURS", __DIR__ . "/../data/utilisateurs.json");

/* 🧾 PREFIX FACTURE */
define("PREFIX_FACTURE", "FAC-");

/* ⏱ FORMAT DATE */
define("FORMAT_DATE", "Y-m-d");
define("FORMAT_HEURE", "H:i:s");

/* 🔐 SÉCURITÉ SESSION */
define("SESSION_TIMEOUT", 1800); // 30 minutes

/* 🏪 NOM DU SYSTÈME */
define("APP_NAME", "Super Marché CodeRunner");