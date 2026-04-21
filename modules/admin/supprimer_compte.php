<?php
require_once('auth/session.php');
verifierConnexion();
?>
<?php
require_once('../../auth/session.php');
verifierRole(['super_admin']);

$users = json_decode(file_get_contents('../../data/utilisateurs.json'), true);

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $users = array_filter($users, function($u) use ($id) {
        return $u['identifiant'] !== $id;
    });

    file_put_contents('../../data/utilisateurs.json', json_encode(array_values($users), JSON_PRETTY_PRINT));

    echo "Utilisateur supprimé";
}
?>