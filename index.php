<?php

require_once('auth/session.php');
require_once('includes/fonctions-auth.php');

verifierConnexion();

$user = getUser();
$role = $user['role'] ?? 'inconnu';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Caisse Pro</title>

<style>

/* ===== BASE ===== */
body{
    margin:0;
    font-family:Arial;
    background:#000;
    color:#fff;
    display:flex;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    height:100vh;
    background:#111;
    border-right:2px solid red;
    padding:15px;
}

.logo{
    text-align:center;
    color:red;
    font-weight:bold;
    margin-bottom:20px;
}

.user{
    background:#1a1a1a;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}

/* ===== MENU ===== */
.menu a{
    display:block;
    padding:12px;
    margin:8px 0;
    text-decoration:none;
    color:white;
    background:#1a1a1a;
    border-radius:6px;
    transition:0.2s;
}

.menu a:hover{
    background:red;
}

/* ===== MAIN ===== */
.main{
    flex:1;
    padding:20px;
}

/* ===== CARDS ===== */
.cards{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.card{
    flex:1;
    min-width:200px;
    background:#111;
    padding:20px;
    border-radius:10px;
    border:1px solid #222;
    text-align:center;
}

.card h2{
    color:red;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="logo">🛒 SYSTÈME DE FACTURATION</div>

<div class="user">
<strong><?= $user['nom_complet'] ?? 'Utilisateur' ?></strong><br>
<small><?= $role ?></small>
</div>

<div class="menu">

<a href="/TP/index.php">🏠 Accueil</a>

<?php if ($role === 'caissier'): ?>

    <a href="/TP/modules/facturation/facturation.php">🧾 Facturation</a>
    <a href="/TP/rapports/rapport-journalier.php">📊 Rapport journalier</a>
    <a href="/TP/rapports/rapport-mensuel.php">📅 Rapport mensuel</a>

<?php elseif ($role === 'manager'): ?>

    <a href="/TP/modules/produits/liste.php">📦 Produits</a>
    <a href="/TP/modules/produits/enregistrer.php">➕ Ajouter produit</a>
    <a href="/TP/modules/facturation/facturation.php">🧾 Facturation</a>
    <a href="/TP/rapports/rapport-journalier.php">📊 Rapport journalier</a>
    <a href="/TP/rapports/rapport-mensuel.php">📅 Rapport mensuel</a>

<?php elseif ($role === 'super_admin'): ?>

    <a href="/TP/index.php">📊 Dashboard</a>
    <a href="/TP/modules/produits/liste.php">📦 Produits</a>
    <a href="/TP/modules/produits/enregistrer.php">➕ Ajouter produit</a>
    <a href="/TP/modules/facturation/facturation.php">🧾 Facturation</a>

    <!-- GESTION DES COMPTES -->
    <a href="/TP/modules/admin/gestion-compte.php">👤 Gestion des comptes</a>
    <a href="/TP/rapports/rapport-journalier.php">📊 Rapport journalier</a>
    <a href="/TP/rapports/rapport-mensuel.php">📅 Rapport mensuel</a>

<?php endif; ?>

<a href="/TP/auth/logout.php">🚪 Déconnexion</a>

</div>

</div>

<!-- MAIN -->
<div class="main">

<div class="card">
    <h1>Bienvenue <?= $user['nom_complet'] ?? '' ?></h1>
    <p>Système de caisse prêt à l’utilisation</p>
</div>

<?php if ($role === 'super_admin'): ?>

<div class="cards">

<div class="card">
<h2>📦 Produits</h2>
<p>Gérer le stock</p>
</div>

<div class="card">
<h2>🧾 Facturation</h2>
<p>Gérer les ventes</p>
</div>

<div class="card">
<h2>👤 Comptes</h2>
<p>Créer / supprimer utilisateurs</p>
</div>

<div class="card">
<h2>📊 Rapports</h2>
<p>Suivi des ventes</p>
</div>

</div>

<?php endif; ?>

</div>

</body>
</html>