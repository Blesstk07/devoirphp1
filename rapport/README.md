#  Système de Gestion de Caisse (TP PHP)

##  Description du projet

Ce projet est une application web de gestion de caisse développée en PHP.  
Il permet de gérer les produits, les ventes, les factures ainsi que les utilisateurs avec différents rôles.
Il a été crée par le groupe CODE RUNNER, constitué par les étudiants Tshimanga Kalala Bless, Mumpubi Elam Rubis et Civava Litsani Esther.
Le système fonctionne avec des fichiers JSON comme base de données légère.

---

##  Fonctionnalités principales

###  Gestion des produits
- Ajout de produits avec le scan de code-barres
- Liste des produits disponibles
- Gestion automatique du stock
- Alerte stock faible

###  Facturation
- Création de factures en temps réel
- Scan de code-barres pour ajouter des produits
- Modification des quantités
- Suppression d’articles
- Calcul automatique :
  - Total HT
  - TVA
  - Total TTC
- Génération de facture enregistrée

###  Rapports
- Rapport journalier des ventes
- Rapport mensuel
- Analyse du chiffre d’affaires
- Statistiques des caissiers actifs

###  Gestion des utilisateurs
- Système de login sécurisé
- Rôles :
  - super_admin
  - manager
  - caissier
- Activation de session utilisateur
- Switch des utilisateurs (uniquement pour le super_admin)

---


##  Technologies utilisées

- PHP (backend)
- HTML / CSS / JavaScript
- JSON (base de données locale)
- JavaScript avec ZXing (scan code-barres)

---

##  Structure du projet

TP/
--rapport
    -README.md
    -Système de Gestion de Caisse.docx
--hash.php
--index.php
--config/
    -config.php
--auth/
    -login.php
    -logout.php
    -session.php
--modules/
    -produits/
        -enregistrer.php
        -lire.php
        -liste.php
    -facturation/
        -nouvelle-facture.php
        -calcul.php
        -afficher-facture.php
    -admin/
        -gestion-comptes.php
        -ajouter-compte.php
        -supprimer-compte.php
        -dashboard.php
--data/
    -produits.json
    -factures.json
    -utilisateurs.json
-includes/
    -header.php
    -footer.php
    -fonctions-produits.php
    -fonctions-factures.php
    -fonctions-auth.php
--assets/
    -css/
        -style.css
    -js/
        -scanner.js
--rapports/
    -rapport-journalier.php
    -rapport-mensuel.php