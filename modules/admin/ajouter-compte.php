<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../auth/session.php');
require_once('../../includes/fonctions-auth.php');

verifierRole(['super_admin']);

/* =========================
   MESSAGE
========================= */
$message = "";

/* =========================
   TRAITEMENT FORMULAIRE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifiant = trim($_POST['identifiant'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $role = $_POST['role'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($identifiant) || empty($nom) || empty($role) || empty($mot_de_passe)) {
        $message = "❌ Tous les champs sont obligatoires";
    } else {

        $file = '../../data/utilisateurs.json';

        $users = file_exists($file)
            ? json_decode(file_get_contents($file), true)
            : [];

        // 🔍 vérifier si utilisateur existe déjà
        foreach ($users as $u) {
            if ($u['identifiant'] === $identifiant) {
                $message = "❌ Identifiant déjà utilisé";
                break;
            }
        }

        if (empty($message)) {

            $nouveau = [
                "identifiant" => $identifiant,
                "mot_de_passe" => password_hash($mot_de_passe, PASSWORD_DEFAULT),
                "role" => $role,
                "nom_complet" => $nom,
                "date_creation" => date("Y-m-d"),
                "actif" => true
            ];

            $users[] = $nouveau;

            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

            $message = "✅ Compte créé avec succès";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter un compte</title>

<link rel="stylesheet" href="../../assets/css/style.css">

<style>
.form-box {
    max-width: 500px;
    margin: 50px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 8px;
    border: none;
}

.btn {
    width: 100%;
    margin-top: 10px;
}
.message {
    text-align: center;
    margin-bottom: 10px;
}
</style>

</head>

<body>

<div class="form-box">

<h2>➕ Ajouter un compte</h2>

<?php if (!empty($message)): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

    <input type="text" name="identifiant" placeholder="Identifiant">

    <input type="text" name="nom" placeholder="Nom complet">

    <select name="role">
        <option value="">-- Choisir un rôle --</option>
        <option value="caissier">Caissier</option>
        <option value="manager">Manager</option>
    </select>

    <input type="password" name="mot_de_passe" placeholder="Mot de passe">

    <button class="btn">Créer le compte</button>

</form>

</div>

</body>
</html>