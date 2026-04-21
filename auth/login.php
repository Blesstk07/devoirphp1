
<?php
// Ici, je définis le fichier du login de l'utilisateur
session_start();
require_once('../includes/fonctions-auth.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['identifiant'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $user = verifierLogin($id, $mot_de_passe);

    if ($user) {
        $_SESSION['user'] = $user;
        header("Location: ../index.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects, tu t'es trompé!";
    }
}
?>

<form method="POST">
    <input type="text" name="identifiant" placeholder="Identifiant" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <button type="submit">Se connecter ici</button>
</form>

<?php if(isset($erreur)) echo $erreur; ?>