<?php

require_once('auth/session.php');
require_once('includes/fonctions-auth.php');

verifierConnexion();

$user = getUser();
$role = $user['role'] ?? '';

$page = $_GET['page'] ?? 'home';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>CAISSE PRO</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #0a0a0a;
    color: white;
    display: flex;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    background: #111;
    border-right: 2px solid red;
    padding: 15px;
}

.logo {
    text-align: center;
    color: red;
    font-weight: bold;
    margin-bottom: 20px;
}

.user {
    background: #1a1a1a;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* MENU */
.menu a {
    display: block;
    padding: 12px;
    margin: 8px 0;
    text-decoration: none;
    color: white;
    background: #1a1a1a;
    border-radius: 6px;
    transition: 0.2s;
}

.menu a:hover {
    background: red;
}

/* MAIN */
.main {
    flex: 1;
    padding: 20px;
}

/* CARDS */
.cards {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.card {
    flex: 1;
    min-width: 200px;
    background: #111;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #222;
}

.card h2 {
    color: red;
}
</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="logo">🛒 CAISSE PRO</div>

<div class="user">
<strong><?= $user['nom_complet'] ?></strong><br>
<small><?= $role ?></small>
</div>

<div class="menu">

<a href="index.php?page=home">🏠 Accueil</a>

<?php if ($role === 'caissier'): ?>

    <a href="modules/facturation/facturation.php">🧾 Facturation</a>
    <a href="index.php?page=rapports">📊 Rapports</a>

<?php elseif ($role === 'manager'): ?>

    <a href="modules/produits/liste.php">📦 Produits</a>
    <a href="modules/produits/enregistrer.php">➕ Ajouter produit</a>
    <a href="modules/facturation/facturation.php">🧾 Facturation</a>
    <a href="index.php?page=rapports">📊 Rapports</a>

<?php elseif ($role === 'super_admin'): ?>

    <a href="index.php?page=dashboard">📊 Dashboard</a>
    <a href="modules/produits/liste.php">📦 Produits</a>
    <a href="modules/produits/enregistrer.php">➕ Ajouter produit</a>
    <a href="modules/facturation/facturation.php">🧾 Facturation</a>
    <a href="index.php?page=rapports">📊 Rapports</a>

<?php endif; ?>

<a href="auth/logout.php">🚪 Déconnexion</a>

</div>

</div>

<!-- MAIN -->
<div class="main">

<?php if ($page === 'home'): ?>

<div class="card">
<h1>Bienvenue <?= $user['nom_complet'] ?></h1>
<p>Système de caisse opérationnel</p>
</div>

<?php elseif ($page === 'dashboard'): ?>

<?php
require_once('includes/fonctions-produits.php');
require_once('includes/fonctions-factures.php');

$produits = lireProduits();
$factures = lireFactures();
?>

<h1 style="color:red;">📊 Dashboard</h1>

<div class="cards">

<div class="card">
<h2><?= count($produits) ?></h2>
<p>Produits</p>
</div>

<div class="card">
<h2><?= count($factures) ?></h2>
<p>Factures</p>
</div>

</div>

<?php elseif ($page === 'rapports'): ?>

<?php
require_once('includes/fonctions-factures.php');

$factures = lireFactures();

$today = date('Y-m-d');
$currentMonth = date('Y-m');

$totalJour = 0;
$totalMois = 0;

foreach ($factures as $f) {

    $date = substr($f['date'] ?? '', 0, 10);
    $mois = substr($f['date'] ?? '', 0, 7);

    if ($date === $today) {
        $totalJour += $f['total'] ?? 0;
    }

    if ($mois === $currentMonth) {
        $totalMois += $f['total'] ?? 0;
    }
}
?>

<h1 style="color:red;">📊 Rapports</h1>

<div class="cards">

<div class="card">
<h2><?= $totalJour ?> FC</h2>
<p>Rapport journalier</p>
</div>

<div class="card">
<h2><?= $totalMois ?> FC</h2>
<p>Rapport mensuel</p>
</div>

</div>

<?php endif; ?>

</div>

</body>
</html>