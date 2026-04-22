<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../../auth/session.php');
require_once(__DIR__ . '/../../includes/fonctions-auth.php');

verifierConnexion();
verifierRole(['manager', 'super_admin']);

$file = __DIR__ . '/../../data/utilisateurs.json';

$utilisateurs = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($utilisateurs)) {
    $utilisateurs = [];
}

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

    header("Location: gestion_compte.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion comptes</title>

    <link rel="stylesheet" href="/TP/assets/css/style.css">

    <style>

        body {
            background: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .box {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,102,0,0.2);
        }

        h2 {
            text-align: center;
            color: #ff6600;
            margin-bottom: 20px;
        }

        /* ================= BUTTONS ================= */
        .top-actions {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            color: #000;
            background: #ff6600;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            background: #ff3300;
            color: #fff;
        }

        /* ================= TABLE ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: #111;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #ff6600;
            color: #000;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #222;
            color: #fff;
            text-align: center;
        }

        tr:hover {
            background: rgba(255,102,0,0.08);
        }

        /* ================= ACTION BUTTONS ================= */
        .actions a {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            font-size: 13px;
            margin: 2px;
            transition: 0.3s;
        }

        .delete {
            background: #ef4444;
        }

        .delete:hover {
            background: #dc2626;
        }

        .switch {
            background: #10b981;
        }

        .switch:hover {
            background: #059669;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="box">

        <h2>⚙️ Gestion des utilisateurs</h2>

        <div class="top-actions">
            <a href="ajouter_compte.php" class="btn">➕ Ajouter utilisateur</a>
            <a href="/TP/index.php" class="btn">🏠 Dashboard</a>
        </div>

        <table>

            <tr>
                <th>Identifiant</th>
                <th>Nom complet</th>
                <th>Rôle</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>

            <?php if (!empty($utilisateurs)): ?>

                <?php foreach ($utilisateurs as $u): ?>
                    <tr>

                        <td><?= htmlspecialchars($u['identifiant']) ?></td>
                        <td><?= htmlspecialchars($u['nom_complet']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['date_creation']) ?></td>

                        <td class="actions">

                            <a class="delete"
                               href="?delete=<?= $u['identifiant'] ?>"
                               onclick="return confirm('Supprimer cet utilisateur ?')">
                               🗑
                            </a>

                            <a class="switch"
                               href="/TP/modules/admin/switch-user.php?id=<?= $u['identifiant'] ?>">
                               🔄
                            </a>

                        </td>

                    </tr>
                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">Aucun utilisateur</td>
                </tr>

            <?php endif; ?>

        </table>

    </div>

</div>

</body>
</html>