<?php
// hash.php
// Générateur de mots de passe hashés pour utilisateurs.json

echo "<h2>🔐 Générateur de hash de mots de passe</h2>";

$users = [
    "Le Caissier" => "123456",
    "La Manager" => "123456",
    "Le Super Admin" => "123456"
];

echo "<pre>";

foreach ($users as $role => $password) {

    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "Utilisateur : $role\n";
    echo "Mot de passe : $password\n";
    echo "Hash : $hash\n";
    echo "-----------------------------\n";
}

echo "</pre>";
?>