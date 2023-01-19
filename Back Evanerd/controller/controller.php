<?php
include_once "baseController.php";
include_once "model/model.php";
include_once "includes/maLibSecurisation.php";
/**
 * TODO : Refacto code redondant
 *  - Vérification que l'utilisateur a accès à un groupe
 *  - Vérification que le token d'authentification est valide
 *  - Vérification des champs pour postUser et putUser
 *  - Vérification a le role de "Non Membre", "Membre du CA" ...
*/

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
        }
        sendError("identifiant invalide !", [getStatusHeader(HTTP_UNAUTHORIZED)]);
    }
    sendError("Paramètres invalide !", [getStatusHeader(HTTP_BAD_REQUEST)]);
}

/**
 * Liste les utilisateur et renvoie la réponse sous format JSON dans le flux STDIN
 * @param array Tableau de donnée à completer et envoyé
 * @param array queryString sous forme de tableau
 */
function listUsers($data, $queryString) {
    $idRole = null;

    if($idRole = valider("idRole", $queryString)) {
        if(!is_id($idRole)) sendError("identifiant role attendu !", HTTP_BAD_REQUEST);
    }
    $data["users"] = selectUsers($idRole);
    if(count($data["users"]) == 0) 
        sendError("Aucun enregistrement trouvé : idRole invalide !", HTTP_NOT_FOUND);

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
    if(count($user) == 0)
        sendError("Aucun enregistrement trouvé : id invalide !", HTTP_NOT_FOUND);

    $data["user"] = $user[0];
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
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
    sendError("Requête Invalide",HTTP_BAD_REQUEST);
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
        if($idUser === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

        $data["groups"] = selectGroups($idUser);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Vous devez être connecté !", HTTP_UNAUTHORIZED);
}
/**
 * Renvoie la liste des message d'un groupe
 * @param array $data tableau à completer et envoyé
 * @param array $idTabs 
 * @param string $authKey Token d'identification de l'utilisateur
 */
function listGroupMessages($data, $idTabs, $authKey) {
    if($authKey)
    if(count($idTabs) == 1) {
        $gid  = $idTabs[0]; 
        $idUser = authToId($authKey);
        if($idUser === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

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
        sendError("Vous devez être dans le groupe !", HTTP_FORBIDDEN);
    }
    sendError("Il faut vous identifié !", HTTP_FORBIDDEN);
}

/**
 * Modifie un utilisateur
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function putUser($data, $idTabs, $authKey,$queryString) {

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
        if($idUser === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

        if($iid = valider("iid", $queryString))
        if(is_id($iid)) {
            $instrument = selectInstruments($iid);
            if(count($instrument)) {
                if(!count(haveInstrument($idUser, $iid))) {
                    insertUserInstrument($iid, $idUser);
                    $data["instrument"] = $instrument;
                    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
                }
                sendError("L'utilisateur a déjà l'instrument", HTTP_FORBIDDEN);
            }
        }
        sendError("Il faut un envoyé l'id d'un instrument valide", HTTP_BAD_REQUEST);
    }
    sendError("Il faut être identifié", HTTP_UNAUTHORIZED);
}

/**
 * Ajout un role à l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $idTabs paramètre d'url
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserRole($data, $idTabs, $authKey, $queryString) {
    if($authKey) {
        $uidConn = authToId($authKey);
        $user = selectUser($idTabs[0]);
        if(!count($user)) sendError("Cet utilisateur n'existe pas !", HTTP_BAD_REQUEST);
        $uid = $user[0]["id"];
        $rid = valider("rid", $queryString);
        $rolesConn = selectUserRoles($uidConn);
        $roles = selectUserRoles($uid);
        $role = selectRole($rid);

        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if($uidConn === false) sendError("Token invalide", HTTP_UNAUTHORIZED);
        if(!$admin) sendError("Vous ne pouvez pas faire cette action", HTTP_FORBIDDEN);
        if(array_search($role, array_column($roles, "label")) !== false) 
            sendError("Cet utilisateur a déjà ce role", HTTP_FORBIDDEN);
        
        $data["user"] = $user[0];
        $data["role"] = array("id" => $rid, "label" => $role);
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
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
 
*function delUserInstrument($data, $authKey, $queryString) {
*    if ($authKey){
*       $uidConn = authToId($authKey);
*       if ($instrument = htmlspecialchars(valider("instrument", $queryString))){

*       }

*   }
*}*/

/**
 * 
 */
function notAction($data) {
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}


function postUserAchievement($data, $authKey, $queryString) {
    if($authKey){
        $uidConn = authToId($authKey);
        if ($aid= htmlspecialchars(valider("achievement", $queryString))){
            updateUserAchievement($uidConn, $aid);
            $data["achivement"] = $aid;
            sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
        }

    }
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
    if($authKey){
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $roleAModif = $idTabs[0];
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        if($active = htmlspecialchars(valider("active", $queryString))
           || $label = htmlspecialchars(valider("label", $queryString))) {
            if(updateRole($roleAModif, $label, $active)) {
                sendResponse($roleAModif, [getStatusHeader(201)]);
                $data["instrument"] = array("id" => insertInstruments($label), "label" => $label);
                sendResponse($data, [getStatusHeader(201)]);
            }
            else
                sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
        }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
    }
}


function postRoles($data, $authKey, $queryString) {

    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
    }

    if($active = htmlspecialchars(valider("active", $queryString)) 
       || $label = htmlspecialchars(valider("label", $queryString))){
        if(insertRole($label, $active)){
            $data["instrument"] = array("id" => insertInstruments($label), "label" => $label);
            sendResponse($data, [getStatusHeader(201)]);
        } else
            sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
    }
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
    if($authKey){
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $instrument = $idTabs[0];
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        if($label = htmlspecialchars(valider("label", $queryString))) {
            if(updateInstruments($instrument,$label)) {
                $data["instrument"] = array("id" => insertInstruments($label), "label" => $label);
                sendResponse($data, [getStatusHeader(201)]);
            }
            else
                sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
        }
        sendError("Il faut spécifier le nom de l'instrument", HTTP_FORBIDDEN);


    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
}

function postInstruments($data, $authKey, $queryString) {
    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        if($label = htmlspecialchars(valider("label", $queryString))) {
            $data["instrument"] = array("id" => insertInstruments($label), "label" => $label);
            sendResponse($data, [getStatusHeader(201)]);
        }
        sendError("Il faut spécifier le nom de l'instrument", HTTP_FORBIDDEN);
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);

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
    if($authKey){
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        if($label = htmlspecialchars(valider("label", $queryString))) {
            if(insertAchievement($label)) {
                $data["achievements"] = array("id" => insertInstruments($label), "label" => $label);
                sendResponse($data, [getStatusHeader(201)]);
            }
            else
                sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
        }
        sendError("Il faut spécifier le nom de l'instrument", HTTP_FORBIDDEN);


    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);   

}

function listGroupsPerms($data, $idTabs, $authKey) {
    if($authKey) {
        $gid  = $idTabs[0]; 
        $idUser = authToId($authKey);
        if(isInGroup($idUser, $gid) || count(haveGroupPermission($idUser, $gid))) {
            $groupsPermsData = selectGroupsPerm($idTabs);
            $data["permissions"] = $groupsPermsData;
            sendResponse($data, [getStatusHeader()]);
        }
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
}

function listGroupsReacts($data, $idTabs, $authKey) {
    if($authKey) {
        $gid  = $idTabs[0]; 
        $idUser = authToId($authKey);
        if(isInGroup($idUser, $gid) || count(haveGroupPermission($idUser, $gid))) {
            $groupsReactsData = selectGroupReaction($idTabs);
            $data["reactions"] = $groupsReactsData;
            sendResponse($data, [getStatusHeader()]);
        }
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);

}

function createUserGroups($data, $authKey, $queryString) {
    if($authKey) {
        $idUser = authToId($authKey);
        if (htmlspecialchars(valider("image")))
            $image = addslashes(valider("image"));
        
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
}

/**
 * Ajoute un utilisateur dans un groupe
 * @param array $data tableau à completer et envoyé
 * @param string $authKey Token d'identification de l'utilisateur
 */
function addUsersGroups($data, $idTabs, $authKey) {
    if($authKey) {
        $uidConn = authToId($authKey); // uid de l'utilisateur connecté
        if($uidConn === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

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
                sendError("Cet utilisateur est déjà dans ce groupe", HTTP_UNAUTHORIZED);
            }
            sendError("Vous ne pouvez pas ajouter un membre dans ce groupe !" , HTTP_UNAUTHORIZED);
        }
        sendError("Cet utilisateur n'existe pas" , HTTP_BAD_REQUEST);
    }
    sendError("Vous devez vous identifier !", HTTP_UNAUTHORIZED);
}

/**
 * Permet d'envoyer un message dans le groupe
 * @param array $data tableau à completer et envoyé
 * @param string $authKey Token d'identification de l'utilisateur
 * @param array $idTabs paramètre d'url 
 * @param array $queryString paramètre de requête
 * @param 
*/
function postMessagesGroups($data, $idTabs, $authKey, $queryString) {
    if($authKey)
    if(count($idTabs)) {
        $gid = $idTabs[0];
        $uidConn = authToId($authKey);
        if($uidConn === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

        if(isInGroup($uidConn, $gid) || count(haveGroupPermission($uidConn, $gid))){
            if($message = htmlspecialchars(valider("content", $queryString))) 
            if(strlen($message) <= 300 ) {
                $answerTo = valider("answerTo", $queryString);
                count(selectGroupMessage($answerTo, $gid)) ? : $answerTo = null;
                $mid = insertGroupMessage($uidConn, $gid, $message, $answerTo);
                $data["message"] = ["id" => $mid, "content" => $message, "pinned" => 0, "answerTo" => $answerTo];
                sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
            }
            sendError("Vous ne pouvez pas envoyé un message vide ", HTTP_BAD_REQUEST);
        }
        sendError("Vous devez être dans le groupe pour pouvoir envoyé un message", HTTP_BAD_REQUEST);
    }
    sendError("Vous devez être identifié pour envoyer un message", HTTP_UNAUTHORIZED);

}

/**
 * Permet de lister les posts visible par l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param string $authKey Token d'identification de l'utilisateur
 */
function listPosts($data, $authKey) {
    if($authKey) {
        $uidConn = authToId($authKey);
        if($uidConn === false) sendError("Token invalide !", HTTP_FORBIDDEN);
        $role = selectUserRoles($uidConn);
        $notAMember = (array_search("Non Membre", array_column($role, "label")) !== false) ? 1 : 0;
        $data["posts"] = selectPosts($notAMember);
        sendResponse($data, getStatusHeader());

    }
    sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
}

function listPostsReacts($data, $idTabs, $authKey) {

}

/**
 * Permet d'envoyer un commentaire sous un post
 * @param array $data tableau à completer et envoyé
 * @param string $authKey Token d'identification de l'utilisateur
 * @param array $idTabs paramètre d'url 
 * 
 */
function listPostsMessages($data, $idTabs, $authKey) {
    if($authKey) {
        $i = 0;
        $pid = $idTabs[0];
        $data["idPost"] = $pid;
        $uidConn = authToId($authKey);
        if($uidConn === false) sendError("Token invalide !", HTTP_FORBIDDEN);
        // Récupération des infos sur le post et l'utilisateur connecté
        $post = selectPost($pid);
        $role = selectUserRoles($uidConn);
        $notAMember = (array_search("Non Membre", array_column($role, "label")) !== false) ? 1 : 0;
        // Vérification si le post existe et que l'utilisateur connecté peut le voir
        if(!count($post)) sendError("Ce post n'existe pas !", HTTP_BAD_REQUEST);
        if($notAMember && $post[0]["visible"] != 1) sendError("Vous ne pouvez pas accéder à ce post", HTTP_FORBIDDEN);

        $messagesData = selectPostMessages($pid);
        $data["comments"] = array();
        // Creation de la réponse
        foreach($messagesData as $message) {
            $data["comments"][$i]["id"] = $message["id"];
            $data["comments"][$i]["author"] = ["id" => $message["uid"], "firstName" => $message["firstName"], "lastName" => $message["lastName"]];
            $data["comments"][$i]["content"] = $message["content"];
            $data["comments"][$i]["pinned"] = $message["pinned"];
            $data["comments"][$i]["answerTo"] = $message["answerTo"];
            $i++;

        }
        sendResponse($data, getStatusHeader());
    }
    sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);

}

function listAgendas($data, $authKey) {
    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $agendas = selectCalendar($uidConn);
        $data["agendas"] = $agendas;
        sendResponse($data, [getStatusHeader()]);
    }
    sendError("Vous devez vous identifier", HTTP_UNAUTHORIZED);
}

function listAgendaEvents($data, $idTabs, $authKey) {
    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $aid = $idTabs[0];
        $agendaEvents = selectEvents($aid, $uidConn);
        $data["agendaId"] = $aid;
        $data["events"] = $agendaEvents;
        sendResponse($data, [getStatusHeader()]);
    }
    sendError("Vous devez vous identifier", HTTP_UNAUTHORIZED);
}

function listAgendaEventCalls($data, $idTabs, $authKey) {
    if($authKey) {
        $i = 0;
        $uidConn = validUser(authToId($authKey));
        $aid = $idTabs[0];
        $eid = $idTabs[1];
        $rolesConn = selectUserRoles($uidConn);
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
        if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        $data["agendasId"] = $aid;
        $data["eventId"] = $eid;
        $data["calls"] = array();
        $callsData = selectCallMembers($eid);
        foreach($callsData as $calls) {
            $data["calls"][$i]["user"] = array("uid" => $calls["uid"], "firstName" => $calls["firstName"], "lastName" => $calls["lastName"]);
            $data["calls"][$i]["present"] = $calls["present"];
            if(!$calls["present"]) {
                $data["calls"][$i]["reason_title"] = $calls["reason_title"];
                $data["calls"][$i]["reason_desc"] = $calls["reason_desc"];
            }
            $i++;

        }
        sendResponse($data, [getStatusHeader()]);
    }
    sendError("Il faut vous identifier", HTTP_UNAUTHORIZED);
}
