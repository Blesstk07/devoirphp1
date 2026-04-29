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
/* =========================
   CYBERPUNK DASHBOARD INDEX
========================= */

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#050505;
    color:#fff;
    display:flex;
    min-height:100vh;
    overflow-x:hidden;
}

/* =========================
   BACKGROUND FX
========================= */
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:
        linear-gradient(rgba(0,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,0,120,0.05) 1px, transparent 1px);
    background-size:50px 50px;
    animation:moveGrid 8s linear infinite;
    z-index:-2;
}

@keyframes moveGrid{
    from{transform:translateY(0);}
    to{transform:translateY(50px);}
}

body::after{
    content:"";
    position:fixed;
    width:600px;
    height:600px;
    top:20%;
    left:50%;
    transform:translateX(-50%);
    background:radial-gradient(circle, rgba(255,0,120,0.25), transparent 60%);
    filter:blur(80px);
    z-index:-1;
    animation:pulse 5s infinite alternate;
}

@keyframes pulse{
    from{transform:translateX(-50%) scale(1); opacity:0.5;}
    to{transform:translateX(-50%) scale(1.3); opacity:1;}
}

/* =========================
   SIDEBAR
========================= */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#0d0d0d,#111);
    border-right:2px solid #ff0077;
    padding:15px;
    box-shadow:0 0 20px rgba(255,0,120,0.2);
    position:relative;
}

.logo{
    text-align:center;
    color:#00fff2;
    font-weight:bold;
    margin-bottom:20px;
    text-shadow:0 0 10px #00fff2;
    font-size:18px;
}

.user{
    background:#151515;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
    border:1px solid rgba(255,0,120,0.2);
}

/* =========================
   MENU
========================= */
.menu a{
    display:block;
    padding:12px;
    margin:8px 0;
    text-decoration:none;
    color:white;
    background:#1a1a1a;
    border-radius:8px;
    transition:0.25s;
    border-left:3px solid transparent;
    position:relative;
    overflow:hidden;
}

.menu a::before{
    content:"";
    position:absolute;
    top:0;
    left:-100%;
    width:100%;
    height:100%;
    background:linear-gradient(90deg, transparent, rgba(255,0,120,0.3), transparent);
    transition:0.4s;
}

.menu a:hover::before{
    left:100%;
}

.menu a:hover{
    background:#ff0077;
    border-left:3px solid #00fff2;
    transform:translateX(6px);
}

/* =========================
   MAIN
========================= */
.main{
    flex:1;
    padding:25px;
    background:radial-gradient(circle at top,#111,transparent 60%);
}

/* =========================
   CARD SYSTEM
========================= */
.card{
    background:rgba(20,20,20,0.8);
    padding:20px;
    border-radius:12px;
    border:1px solid rgba(255,0,120,0.2);
    box-shadow:0 0 20px rgba(255,0,120,0.1);
    margin-bottom:15px;
    animation:fadeIn 0.6s ease;
    backdrop-filter:blur(8px);
}

.card h1{
    color:#00fff2;
    text-shadow:0 0 10px #00fff2;
}

.card h2{
    color:#ff0077;
    text-shadow:0 0 8px #ff0077;
}

/* =========================
   GRID CARDS
========================= */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
    margin-top:20px;
}

.cards .card{
    text-align:center;
}

/* =========================
   ANIMATION
========================= */
@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width:768px){
    .sidebar{
        width:200px;
    }
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