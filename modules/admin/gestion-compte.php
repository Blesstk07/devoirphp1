<?php

require_once(__DIR__ . '/../../auth/session.php');
require_once(__DIR__ . '/../../includes/fonctions-auth.php');

verifierConnexion();

// on récupère les utilisateurs depuis le fichier JSON
$usersFile = __DIR__ . '/../../data/utilisateurs.json';

$users = [];

if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des comptes</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #0a0a0a;
    color: white;
}

.container {
    padding: 30px;
}

h1 {
    color: red;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #111;
}

th, td {
    padding: 12px;
    border: 1px solid #222;
    text-align: center;
}

th {
    background: #1a1a1a;
    color: red;
}

tr:hover {
    background: #1c1c1c;
}

.badge {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
}

.caissier { background: #444; }
.manager { background: #b30000; }
.super_admin { background: #ff0000; }
</style>

</head>

<body>

<div class="container">

<h1>👤 Gestion des Comptes</h1>

<table>

<tr>
    <th>Nom complet</th>
    <th>Identifiant</th>
    <th>Rôle</th>
    <th>Actif</th>
    <th>Date création</th>
</tr>

<?php if (!empty($users)): ?>

    <?php foreach ($users as $u): ?>

    <tr>
        <td><?= $u['nom_complet'] ?></td>
        <td><?= $u['identifiant'] ?></td>
        <td>
            <span class="badge <?= $u['role'] ?>">
                <?= $u['role'] ?>
            </span>
        </td>
        <td><?= $u['actif'] ? 'Oui' : 'Non' ?></td>
        <td><?= $u['date_creation'] ?></td>
    </tr>

    <?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="5">Aucun utilisateur trouvé</td>
</tr>

<?php endif; ?>

</table>

</div>

</body>
</html>