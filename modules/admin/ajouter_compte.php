<?php
require_once('auth/session.php');
verifierConnexion();
?>
<?php
require_once('../../auth/session.php');
verifierRole(['super_admin']);

$users = json_decode(file_get_contents('../../data/utilisateurs.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nouvelUser = [
        "identifiant" => $_POST['identifiant'],
        "mot_de_passe" => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
        "role" => $_POST['role'],
        "nom_complet" => $_POST['nom_complet'],
        "date_creation" => date("Y-m-d"),
        "actif" => true
    ];

    $users[] = $nouvelUser;

    file_put_contents('../../data/utilisateurs.json', json_encode($users, JSON_PRETTY_PRINT));

    echo "Utilisateur ajouté avec succès";
}
?>

<form method="POST">
    <input name="identifiant" placeholder="Identifiant" required>
    <input name="nom_complet" placeholder="Nom complet" required>
    <input name="mot_de_passe" type="password" placeholder="Mot de passe" required>

    <select name="role">
        <option value="caissier">Caissier</option>
        <option value="manager">Manager</option>
        <option value="super_admin">Super Admin</option>
    </select>

    <button type="submit">Créer un nouvel utilisateur</button>
</form>