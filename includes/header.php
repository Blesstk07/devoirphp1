<?php
require_once('../auth/session.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Système de caisse</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<header>
    <h2>Système de gestion de caisse de Super Marché</h2>

    <nav>
        <a href="../index.php">Accueil</a>

        <?php if (isset($_SESSION['user'])): ?>
            <a href="../modules/facturation/nouvelle_facture.php">Facturation</a>
            <a href="../modules/produits/liste.php">Produits</a>

            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                <a href="../modules/admin/gestion_compte.php">Admin</a>
            <?php endif; ?>

            <a href="../auth/logout.php">Déconnexion</a>

        <?php else: ?>
            <a href="../auth/login.php">Connexion</a>
        <?php endif; ?>
    </nav>

    <?php if (isset($_SESSION['user'])): ?>
        <p> Connecté : <?= $_SESSION['user']['nom_complet'] ?> (<?= $_SESSION['user']['role'] ?>)</p>
    <?php endif; ?>
</header>

<hr>