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
            sendResponse($data, [getStatusHeader()]);
            return;
        }

        sendError("identifiant invalide !", [getStatusHeader(HTTP_FORBIDDEN)]);
    }

    return sendError("Paramètres invalide !", [getStatusHeader(HTTP_BAD_REQUEST)]);
}

/**
 * Liste les utilisateur et renvoie la réponse sous format JSON dans le flux STDIN
 */
function listUsers($data, $queryString) {
    $idRole = null;

    if($idRole = valider("idRole", $queryString)) {
        if(!is_id($idRole)) {
            sendError("identifiant role attendu !", HTTP_BAD_REQUEST);
            return;
        }
    }
    $data["users"] = getUsers($idRole);
    if(count($data["users"]) == 0) {
        sendError("Aucun enregistrement trouvé : idRole invalide !", HTTP_NOT_FOUND);
        return;
    }

    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
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
        sendError("Aucun enregistrement trouvé : id invalide !", HTTP_NOT_FOUND);
        return;
    }
    
    $data["user"] = $user[0];
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
    return;
}

/**
 * Créer un utilisateur est renvoie la réponses sous format JSON dans le flux STDIN
 * @param $data Tableau de réponse
 * @param $querystring chaine de requête
 * @return void
 */
function postUser($data, $queryString) {
    $image = "default.png";

    if($firstName = htmlspecialchars(valider("firstName", $queryString)))
    if($lastName = htmlspecialchars(valider("lastName", $queryString)))
    if($mail = htmlspecialchars(valider("mail", $queryString)))
    if($tel = valider("tel", $queryString));
    if($plainPassword = valider("password", $queryString))
    if($age = valider("age", $queryString)) {
        ($studies = htmlspecialchars(valider("studies", $queryString))) ? : $studies = "";
        ($sex = valider("sex", $queryString)) ? : $sex = 0;
        ($imageData = valider("image", $queryString)) ? : $imageData = null;
        // TODO :
            // firstname :
                // ->> must be between 3 and 30 characters
            // lastName :
                // -> must be between 3 and 30 characters
            // mail :
                // -> must be a valide email adress
            // tel :
                // -> must not belong to an other account
            // age :
                // -> must be a int
            // studies
                // -> must not exceed 50 characters
            // sex
                // -> must be 0, 1, 2
            // imageData
                // -> must be an image
            // -> size not exceed ? mb
        $password = password_hash($plainPassword, PASSWORD_BCRYPT, ["cost"=>10]);
        if($imageData != null) $image = "image.png";

        $idUser = createUser($firstName, $lastName, $mail, $tel, $password, $age, $studies, $sex, $image);
        $data["user"] = getUser($idUser)[0];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Requête Invalide",400);
}

function listGroups($data, $queryString, $authKey) {
    if($authKey) {
        $idUser = authToId($authKey);
        $data["groups"] = getGroups($idUser);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Vous devez être connecté !", HTTP_FORBIDDEN);
}

function notAction($data) {
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}
