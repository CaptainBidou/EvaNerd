<?php

include_once "maLibUtils.php";
include_once "model/model.php";

/**
 * Cette fonction vérifie si le login/passe passés en paramètre sont légaux
 * @pre login et passe ne doivent pas être vides
 * @param string $login
 * @param string $password
 */
function checkUser($tel,$password) {
	$userCredentials = getUserCredentials($tel);
	if (!$userCredentials) return false;
	if(!password_verify($password, $userCredentials[0]["password"])) return false;
    if(!$userCredentials[0]["activation"]) return false;

    return $userCredentials[0]["id"];	
}

/**
 * Génére un token d'authentification
 * @param $tel numéro de téléphone du compte
 * @return string
 */
function generateAuthToken($tel) {
    $plainToken = $tel . date("H:i:s");
    return md5($plainToken);
}