<?php
require_once(__DIR__ . '/../auth/session.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Système de caisse</title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="/TP/assets/css/style.css">

    <!-- STYLE HEADER -->
    <style>

        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        header {
            background: #111827;
            color: white;
            padding: 15px 20px;
        }

        header h2 {
            margin: 0;
            font-size: 18px;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.3s;
        }

        nav a:hover {
            background: #2563eb;
        }

        .user-info {
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.9;
        }

        .container {
            padding: 20px;
        }

    </style>
</head>

<body>

<header>

    <h2>🧾 Système de gestion de caisse</h2>

    <nav>

        <a href="/TP/index.php">Accueil</a>

        <?php if (isset($_SESSION['user'])): ?>

            <a href="/TP/modules/facturation/nouvelle_facture.php">Facturation</a>
            <a href="/TP/modules/produits/liste.php">Produits</a>

            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                <a href="/TP/modules/admin/gestion_compte.php">Admin</a>
            <?php endif; ?>

            <a href="/TP/auth/logout.php">Déconnexion</a>

        <?php else: ?>

            <a href="/TP/auth/login.php">Connexion</a>

        <?php endif; ?>

    </nav>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="user-info">
            👤 <?= htmlspecialchars($_SESSION['user']['nom_complet']) ?>
            (<?= $_SESSION['user']['role'] ?>)
        </div>
    <?php endif; ?>

</header>

<div class="container"></div>