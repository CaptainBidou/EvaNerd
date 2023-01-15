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
        if($idUser === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }

        $data["groups"] = selectGroups($idUser);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Vous devez être connecté !", HTTP_FORBIDDEN);
}
/**
 * Renvoie la liste des message d'un groupe
 */
function listGroupMessages($data, $idTabs, $authKey) {
    if($authKey)
    if(count($idTabs) == 1) {
        $gid  = $idTabs[0]; 
        $idUser = authToId($authKey);
        if($idUser === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }

        if(isInGroup($idUser, $gid) || count(haveGroupPermission($idUser, $gid))) {
            echo "coucou";
            $i = 0;
            $data["groupId"] = $gid;
            $data["messages"] = array();

            $reactionData = groupby(selectGroupReactions($gid), "mid");
            foreach($reactionData as $mid => $reactionstab) {
                $reactionData[$mid] = groupby($reactionstab, "emoji");
            }

            $messagesData = selectGroupMessages($gid);
            foreach($messagesData as $message) {
                $data["messages"][$i]["id"] = $message["id"];
                $data["messages"][$i]["author"] = ["id" => $message["uid"], "firstName" => $message["firstName"], "lastName" => $message["lastName"]];
                $data["messages"][$i]["content"] = $message["content"];
                $data["messages"][$i]["pinned"] = $message["pinned"];
                $data["messages"][$i]["answerTo"] = $message["answerTo"];
                if(isset($reactionData[$message["id"]])) {
                    $data["messages"][$i]["reactions"] = $reactionData[$message["id"]];
                }

                $i++;

            }
            sendResponse($data, [getStatusHeader(HTTP_OK)]);
        }
        sendError("Vous devez être identifié !", HTTP_FORBIDDEN);

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
function postUserInstrument($data, $authKey, $queryString) {
    if($authKey) {
        $idUser = authToId($authKey);
        if($idUser === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }

        if($iid = valider("iid", $queryString))
        if(is_id($iid)) {
            $instrument = selectInstruments($iid);
            if(count($instrument)) {
                if(!count(haveInstrument($idUser, $iid))) {
                    insertUserInstrument($iid, $idUser);
                    $data["instrument"] = $instrument;
                    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
                    return;
                }
                sendError("L'utilisateur a déjà l'instrument", HTTP_UNAUTHORIZED);
                return;
            }

        }
        sendError("Il faut un envoyé l'id d'un instrument valide", HTTP_BAD_REQUEST);
        return;
    }
    sendError("Il faut être identifié", HTTP_FORBIDDEN);

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
function delUserInstrument($data, $authKey, $queryString) {

}

/**
 * 
 */
function notAction($data) {
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}


function postUserAchievement($data, $authKey, $queryString) {

}

function postRole($data, $idTabs, $authKey, $queryString) {

}

function delUserAchievement($data, $authKey, $queryString) {

}

function delUserRole($data, $authKey, $queryString) {

}

/**
 * Liste les roles présent dans la base de donnée
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 */
function listRoles($data, $queryString) {
    ($active = valider("active", $queryString) !== false) ? : $active = "both";
    $rolesData = selectRoles($active);
    $data["roles"] = $rolesData;
    sendResponse($data, [getStatusHeader()]);
}


function putRoles($data, $idTabs, $authKey, $queryString) {

}

function postRoles($data, $authKey, $queryString) {

}
/**
 * Liste les instruments disponible dans la base de donnée
 * @param array $data tableau à completer et envoyé
 */
function listInstruments($data) {
    $instrumentsData = selectInstruments();
    $data["instruments"] = $instrumentsData;
    sendResponse($data, [getStatusHeader()]);

}

function putInstruments($data, $idTabs, $authKey, $queryString) {

}

function postInstruments($data, $authKey, $queryString) {

}

/**
 * Liste les instruments disponible dans la base de donnée
 * @param array $data tableau à completer et envoyé
 */
function listAchievements($data) {
    $achievementsData = selectAchievements();
    $data["achievements"] = $achievementsData;
    sendResponse($data, [getStatusHeader()]);

}

function putAchievements($data, $idTabs, $authKey, $queryString) {

}

function postAchievements($data, $authKey, $queryString) {

}

function listGroupsPerms($data, $idTabs, $authKey) {

}

function listGroupsReacts($data, $idTabs, $authKey) {

}

function createUserGroups($data, $authKey, $queryString) {

}
/**
 * Ajoute un utilisateur dans un groupe
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function addUsersGroups($data, $idTabs, $authKey) {
    if($authKey)
    if(count($idTabs) == 2) {
        $uidConn = authToId($authKey); // uid de l'utilisateur connecté
        if($uidConn === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }

        $uidToAdd = $idTabs[1]; // uid de l'utilisateur à ajouter
        $gid = $idTabs[0];
        $user = selectUser($uidToAdd);
        
        if(count($user)) {
            if(isInGroup($uidConn, $gid) || count(haveGroupPermission($uidConn, $gid))){
                if(!isInGroup($uidToAdd, $gid)) {
                    insertIntoGroup($uidToAdd, $gid);
                    $data["user"] = $user[0];
                    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
                }
                else
                    sendError("Cet utilisateur est déjà dans ce groupe", HTTP_UNAUTHORIZED);
            }
            else
                sendError("Vous ne pouvez pas ajouter un membre dans ce groupe !" , HTTP_FORBIDDEN);
        }
        else
            sendError("Cet utilisateur n'existe pas" , HTTP_BAD_REQUEST);
    }
    sendError("Vous devez vous identifier !", HTTP_FORBIDDEN);


}

function postMessagesGroups($data, $idTabs, $authKey, $queryString) {
    if($authKey)
    if(count($idTabs)) {
        $gid = $idTabs[0];
        $uidConn = authToId($authKey);
        if($uidConn === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }

        if(isInGroup($uidConn, $gid) || count(haveGroupPermission($uidConn, $gid))){
            if($message = htmlspecialchars(valider("content", $queryString))) 
            if(strlen($message) <= 300 ) {
                $answerTo = valider("answerTo", $queryString);
                count(selectGroupMessage($answerTo, $gid)) ? : $answerTo = null;
                $mid = insertGroupMessage($uidConn, $gid, $message, $answerTo);
                $data["message"] = ["id" => $mid, "content" => $message, "pinned" => 0, "answerTo" => $answerTo];
                sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
            }
            sendError("Vous ne pouvez pas envoyé un message vide ", HTTP_UNAUTHORIZED);
        }
        sendError("Vous devez être dans le groupe pour pouvoir envoyé un message", HTTP_BAD_REQUEST);
    }
    sendError("Vous devez être identifié pour envoyer un message", HTTP_FORBIDDEN);

}

function listPosts($data, $authKey) {
    if($authKey) {
        $uidConn = authToId($authKey);
        if($uidConn === false) {
            sendError("Token invalide !", HTTP_FORBIDDEN);
            return;
        }
        $role = selectUserRoles($uidConn);
        $notAMember = (array_search("Non Membre", array_column($role, "label")) !== false) ? 1 : 0;
        $data["posts"] = selectPosts($notAMember);
        sendResponse($data, getStatusHeader());
    }
    else {
        sendError("Vous devez être identifié !", HTTP_FORBIDDEN);
    }

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
