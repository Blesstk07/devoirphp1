<?php
session_start();
require_once('../includes/fonctions-auth.php');

//  si déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$erreur = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST['identifiant'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $user = verifierLogin($id, $mot_de_passe);

    if ($user) {

        //  connexion propre
        connecterUtilisateur($user);

        header("Location: ../index.php");
        exit;

    } else {
        $erreur = " Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1> Connexion au système de caisse</h1>

<?php if ($erreur): ?>
    <p style="color:red;"><?= $erreur ?></p>
<?php endif; ?>

<form method="POST">

    <input type="text" name="identifiant" placeholder="Identifiant" required><br><br>

    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br><br>

    <button type="submit">Se connecter</button>

</form>

</body>
</html>