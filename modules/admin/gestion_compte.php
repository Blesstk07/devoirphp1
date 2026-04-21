<?php
require_once('auth/session.php');
verifierConnexion();
?>
<?php
require_once('../../auth/session.php');
verifierRole(['super_admin']);

$users = json_decode(file_get_contents('../../data/utilisateurs.json'), true);
?>

<h1>Gestion des comptes des utilisateurs</h1>

<table border="1">
    <tr>
        <th>Identifiant</th>
        <th>Nom</th>
        <th>Rôle</th>
        <th>Actif</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['identifiant'] ?></td>
        <td><?= $u['nom_complet'] ?></td>
        <td><?= $u['role'] ?></td>
        <td><?= $u['actif'] ? 'Oui' : 'Non' ?></td>
    </tr>
    <?php endforeach; ?>
</table>