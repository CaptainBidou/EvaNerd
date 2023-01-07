<?php
include_once "baseController.php";
include_once "model/model.php";

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
