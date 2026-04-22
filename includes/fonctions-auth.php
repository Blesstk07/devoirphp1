<?php
// Ici, je gère le test de connexion de l'utilisateur
function lireUtilisateurs() {
    return json_decode(file_get_contents('../data/utilisateurs.json'), true);
}

function verifierLogin($id, $mot_de_passe) {
    $utilisateur = lireUtilisateurs();

    foreach ($utilisateur as $U) {
        if ($U['identifiant'] === $id && password_verify($mot_de_passe, $U['mot_de_passe'])) {
            return $U;
        }
    }
    return null;
}
?>