<?php
include_once "baseController.php";
include_once "model/model.php";
include_once "includes/maLibSecurisation.php";

/**
 * Permet à un utilisateur de s'identifier
 */
function authUser($data, $queryString) {
    if($tel = valider("tel", $queryString))
    if($password = valider("password", $queryString)) {
        if($idUser = checkUser($tel, $password)) {
            $data["authToken"] = generateAuthToken($tel);
            $data["user"] = updateAuthToken($idUser, $data["authToken"]);
            sendResponse($data, [getStatusHeader(200)]);
            return;
        }

        sendError("identifiant invalide !", [getStatusHeader(403)]);
    }

    return sendError("Paramètres invalide !", [getStatusHeader(401)]);
}

/**
 * Liste les utilisateur et renvoie la réponse sous format JSON dans le flux STDIN
 */
function listUsers($data, $queryString) {
    $idRole = null;

    if($idRole = valider("idRole", $queryString)) {
        if(!is_id($idRole)) {
            sendError("identifiant role attendu !", 400);
            return;
        }
    }
    $data["users"] = getUsers($idRole);
    if(count($data["users"]) == 0) {
        sendError("Aucun enregistrement trouvé : idRole invalide !", 400);
        return;
    }

    sendResponse($data, [getStatusHeader(201)]);
}
/**
 * Renvoie l'utilisateur sous format JSON dans le flux STDIN
 */
function sendUser($data, $idTabs, $authKey) {
    $me = false;

    if($authKey)
    if(authToId($authKey) == $idTabs[0]) {
        $me = true;
    }
    $user = getUser($idTabs[0], $me);
    if(count($user) == 0) {
        sendError("Aucun enregistrement trouvé : id invalide !", 400);
        return;
    }
    
    $data["user"] = $user[0];
    sendResponse($data, [getStatusHeader(200)]);
    return;
}

/**
 * Créer un utilisateur est renvoie la réponses sous format JSON dans le flux STDIN
 * @param $data Tableau de réponse
 * @param $querystring chaine de requête
 * @return void
 */
function postUser($data, $queryString) {
    sendError("NOT IMPLEMENTED YET", 404);
}

function notAction($data) {
    sendResponse($data, [getStatusHeader(200)]);
}
