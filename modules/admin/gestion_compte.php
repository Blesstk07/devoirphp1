<?php
require_once('../auth/session.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

$file = '../data/utilisateurs.json';

$utilisateurs = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

/* =========================
    SUPPRESSION UTILISATEUR
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    foreach ($utilisateurs as $index => $u) {
        if ($u['identifiant'] === $id) {
            unset($utilisateurs[$index]);
        }
    }

    $utilisateurs = array_values($utilisateurs);

    file_put_contents($file, json_encode($utilisateurs, JSON_PRETTY_PRINT));

    header("Location: gestion-comptes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        .box { max-width: 1000px; margin:auto; background:white; padding:20px; border-radius:10px; }

        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:center; }
        th { background:#007bff; color:white; }

        .btn { padding:5px 10px; text-decoration:none; color:white; border-radius:5px; }
        .delete { background:red; }

        .top a { margin-right:10px; }
    </style>
</head>

<body>

<div class="box">

    <div class="top">
        <a href="../index.php"> Dashboard</a>
        <a href="ajouter-compte.php"> Ajouter un nouvel utilisateur</a>
    </div>

    <h1> Gestion des utilisateurs</h1>

    <table>
        <tr>
            <th>Identifiant</th>
            <th>Nom</th>
            <th>Rôle</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['identifiant'] ?></td>
                <td><?= $u['nom_complet'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['date_creation'] ?></td>
                <td>
                    <a class="btn delete"
                    href="?delete=<?= $u['identifiant'] ?>"
                    onclick="return confirm('Supprimer cet utilisateur ?')">
                    
                    </a>

                    <a class="btn" style="background:green;"
                    href="../auth/switch-user.php?id=<?= $u['identifiant'] ?>">
                    
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>