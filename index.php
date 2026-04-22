<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require_once('auth/session.php');
require_once('includes/fonctions-factures.php');
require_once('includes/fonctions-produits.php');

verifierConnexion();

$user = userConnecte();

//  données pour stats rapides
$factures = lireFactures();
$produits = lireProduits();

$nb_factures = count($factures);
$nb_produits = count($produits);

//  total ventes globales
$total_ventes = 0;

foreach ($factures as $f) {
    $total_ventes += $f['total_ttc'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Caisse</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<h1> Système de Caisse</h1>

<!--  UTILISATEUR -->
<p>Bienvenue <strong><?= $user['nom_complet'] ?></strong></p>
<p>Rôle : <strong><?= $user['role'] ?></strong></p>

<hr>

<!--  STATS -->
<h2> Statistiques rapides</h2>

<ul>
    <li> Factures : <?= $nb_factures ?></li>
    <li> Produits : <?= $nb_produits ?></li>
    <li> Ventes totales : <?= $total_ventes ?> CDF</li>
</ul>

<hr>

<!--  MENU -->
<h2> Modules</h2>

<ul>
    <li><a href="modules/facturation/nouvelle-facture.php"> Nouvelle facture</a></li>
    <li><a href="modules/produits/liste.php"> Les produits</a></li>
    <li><a href="modules/produits/enregistrer.php"> Ajouter un produit</a></li>
    <li><a href="rapports/rapport-journalier.php"> Rapport journalier</a></li>
    <li><a href="rapports/rapport-mensuel.php">Rapport mensuel</a></li>
</ul>

<hr>

<!--  LOGOUT -->
<a href="auth/logout.php" style="color:red;">Se déconnecter</a>

</body>
</html>