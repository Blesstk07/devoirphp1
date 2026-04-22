<?php
require_once('../auth/session.php');

verifierConnexion();
verifierRole(['super_admin']);

$file = '../data/utilisateurs.json';

$utilisateurs = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $utilisateurs[] = [
        "identifiant" => $_POST['identifiant'],
        "mot_de_passe" => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
        "role" => $_POST['role'],
        "nom_complet" => $_POST['nom_complet'],
        "date_creation" => date("Y-m-d"),
        "actif" => true
    ];

    file_put_contents($file, json_encode($utilisateurs, JSON_PRETTY_PRINT));

    header("Location: gestion-comptes.php");
    exit;
}
?>

<h1> Ajouter utilisateur</h1>

<form method="POST">

    <input type="text" name="identifiant" placeholder="Identifiant" required><br><br>

    <input type="text" name="nom_complet" placeholder="Nom complet" required><br><br>

    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br><br>

    <select name="role">
        <option value="caissier">Caissier</option>
        <option value="manager">Manager</option>
        <option value="super_admin">Super Admin</option>
    </select><br><br>

    <button type="submit">Créer</button>

</form>