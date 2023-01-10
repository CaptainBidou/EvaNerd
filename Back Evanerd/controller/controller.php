<?php
include_once "baseController.php";
include_once "model/model.php";
include_once "includes/maLibSecurisation.php";

/**
 * Permet à un utilisateur de s'identifier
 * @param array Tableau de donnée à completer et envoyé
 * @param array queryString sous forme de tableau
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
 * @param array Tableau de donnée à completer et envoyé
 * @param array queryString sous forme de tableau
 */
function listUsers($data, $queryString) {
    $idRole = null;

    if($idRole = valider("idRole", $queryString)) {
        if(!is_id($idRole)) {
            sendError("identifiant role attendu !", HTTP_BAD_REQUEST);
            return;
        }
    }
    $data["users"] = selectUsers($idRole);
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
    $user = selectUser($idTabs[0], $me);
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

        $idUser = insertUser($firstName, $lastName, $mail, $tel, $password, $age, $studies, $sex, $image);
        $data["user"] = selectUser($idUser)[0];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Requête Invalide",400);
}

/**
 * Renvoie la liste des groupes
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function listGroups($data, $queryString, $authKey) {
    if($authKey) {
        // TODO : list group where user have permission
        $idUser = authToId($authKey);
        $data["groups"] = selectGroups($idUser);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Vous devez être connecté !", HTTP_FORBIDDEN);
}
/**
 * Renvoie la liste des message d'un groupe
 */
function listGroupMessage($data, $authKey, $idTabs) {
    if($authKey)
    if(count($idTabs) == 1) {
        $gid  = $idTabs[0];
        $idUser = authToId($authKey);
        if(isInGroup($idUser, $gid) || count(haveGroupPermission($idUser, $gid))) {
            $data["groupId"] = $gid;
            $data["messages"] = selectGroupMessages($gid);
            sendResponse($data, [getStatusHeader(HTTP_OK)]);
        }
        // TODO : error messages
        
        return;
    }

    sendError("Il faut vous identifié !", HTTP_FORBIDDEN);
}

/**
 * Modifie un utilisateur
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function putUser($data, $idTabs, $authKey) {

}

/**
 * Ajout un instrument à l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserInstrument($data, $idTabs, $authKey, $queryString) {

}

/**
 * Ajout un role à l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserRole($data, $idTabs, $authKey, $queryString) {

}

/**
 * Vérifie l'email d'un utilisateur
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function verifMail($data, $idTabs, $authKey) {

}

/**
 * Supprime un instrument de l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function delUserInstrument($data, $idTabs, $authKey, $queryString) {

}

/**
 * 
 */
function notAction($data) {
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}


function postUserAchievement($data, $idTabs, $authKey, $queryString) {

}

function postRole($data, $idTabs, $authKey, $queryString) {

}

function delUserAchievement($data, $idTabs, $authKey, $queryString) {

}

function delUserRole($data, $idTabs, $authKey, $queryString) {

}


function listRoles($data, $queryString) {

}


function putRoles($data, $idTabs, $authKey, $queryString) {

}

function postRoles($data, $authKey, $queryString) {

}

function listInstruments($data, $queryString) {

}

function putInstruments($data, $idTabs, $authKey, $queryString) {

}

function postInstruments($data, $authKey, $queryString) {

}

function listAchievements($data, $queryString) {

}

function putAchievements($data, $idTabs, $authKey, $queryString) {

}

function postAchievements($data, $authKey, $queryString) {

}

function listGroupsPerms($data, $idTabs, $authKey) {

}

function listGroupsMessages($data, $idTabs, $authKey) {

}

function listGroupsReacts($data, $idTabs, $authKey) {

}

function createUserGroups($data, $authKey, $queryString) {

}

function addUsersGroups($data, $authKey) {

}

function postMessagesGroups($data, $idTabs, $authKey, $queryString) {

}

function listPosts($data, $authKey) {

}

function listPostsReacts($data, $idTabs, $authKey) {

}

function listPostsMessages($data, $idTabs, $authKey) {

}

function listAgendas($data, $authKey) {

}

function listAgendaEvents($data, $idTabs, $authKey) {

}

function listAgendaEventCalls($data, $idTabs, $authKey) {

}
