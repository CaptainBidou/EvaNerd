<?php
include_once "baseController.php";
include_once "model/model.php";
include_once "includes/maLibSecurisation.php";
include_once "includes/maLibGD2.php";
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
        sendError("identifiant invalide !", HTTP_UNAUTHORIZED);
    }
    sendError("Paramètres invalide !", HTTP_BAD_REQUEST);
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
    if(!$data["users"]) 
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
    if(!$user)
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
    $newId = getNewUid();
    $dir = DIR_USERS . $newId;
    // on récupère tous les champs obligatoire sinon on renvoie une erreur
    if($firstName = validString(htmlspecialchars(valider("firstName", $queryString)), 30, 3))
    if($lastName = validString(htmlspecialchars(valider("lastName", $queryString)), 30, 3))
    if($mail = isEmail(htmlspecialchars(valider("mail", $queryString))))
    if($tel = isPhoneNumber(valider("tel", $queryString)))
    if($plainPassword = validString(valider("password", $queryString), 70, 5))
    if($age = intval(valider("age", $queryString))) {
        if(phoneToUid($tel) !== false) sendError("Il y a déjà un compte avec ce numéro de téléphone !", HTTP_FORBIDDEN);
        // Récupérations des champs optionnels 
        ($studies = validString(htmlspecialchars(valider("studies", $queryString)), 50, 0)) ? : $studies = "";
        ($sex = isSex(valider("sex", $queryString))) ? : $sex = 0;
        // On hash le mot de passe avec l'algo bcrypt2 et avec un cout de 10
        $password = password_hash($plainPassword, PASSWORD_BCRYPT, ["cost"=>10]);
        // Si le répertoire pour cette id n'a pas été fait on le créer
        if(!is_dir($dir)) mkdir($dir);
        // Traitement de l'image
        if(isset($_FILES["image"])) {
            $imageData = uploadImage($dir . "/image", $_FILES["image"]);
            if($imageData["code"] != 1) sendError($imageData["message"], HTTP_BAD_REQUEST);
            $image = basename($imageData["filename"]);
            echo $image;
        }
        else {
            // Si il n'y pas d'image on en génére une
            $image = "default.png";
            defaultPicture("$dir/$image", $firstName, $lastName);
        }
        // Génération du token de confirmation
        $token = generateEmailConfirmToken($tel);
        // Enfin si tout est bon alors on créer l'utilisateur en base et on le renvoie en réponse
        $idUser = insertUser($firstName, $lastName, $mail, $tel, $password, $age,$token, $studies, $sex, $image);
        // TODO : Récupérer l'objet mail pour envoyer le mail avec le lien de confirmation

        // On envoie l'utilisateur créé
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
        $uidConn = validUser(authToId($authKey));
        $data["groups"] = selectGroups($uidConn);
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
    if($authKey) {
        $gid  = $idTabs[0]; 
        $uidConn = validUser(authToId($authKey));
        // Si il est dans le groupe ou que son role permet d'acceder au groupe
        // Alors il a le droit d'accéder au message du groupe
        if(isInGroup($uidConn, $gid) || haveGroupPermission($uidConn, $gid)) {
            $i = 0;
            $data["groupId"] = $gid;
            $data["messages"] = array();
            $reactionData = groupby(selectGroupReactions($gid), "mid");
            // On regoupe par emoji
            foreach($reactionData as $mid => $reactionstab) {
                $reactionData[$mid] = groupby($reactionstab, "emoji");
            }
            // on recupére les messages
            $messagesData = selectGroupMessages($gid);
            // Pour chaque message on construit un json et on lui associe ses emojis
            foreach($messagesData as $message) {
                $data["messages"][$i]["id"] = $message["id"];
                $data["messages"][$i]["author"] = ["id" => $message["uid"], "firstName" => $message["firstName"], "lastName" => $message["lastName"], "photo" => $message["photo"]];
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
    sendError("Il faut vous identifié !", HTTP_UNAUTHORIZED);
}

/**
 * Modifie un utilisateur
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function putUser($data, $idTabs, $authKey,$queryString) {
    print_r($_FILES);
    print_r($_POST);
    // TODO : mettre à jour la doc
    if($authKey) {
        $uid = $idTabs[0];
        $uidConn = validUser(authToId($authKey));
        $dir = DIR_USERS . $uid;
        if($uidConn != $uid) sendError("Vous n'avez pas la permissions !", HTTP_UNAUTHORIZED);

        // Récupération et validation des infos :
        $mail = isEmail(htmlspecialchars(valider("mail", $queryString)));
        $tel = isPhoneNumber(valider("tel", $queryString));
        $plainPassword = validString(valider("password", $queryString), 70, 5);
        $age = intval(valider("age", $queryString));
        $studies = validString(htmlspecialchars(valider("studies", $queryString)), 50, 0);
        $image = false;
        $user = selectUser($uidConn, 1);

        // Vérification que le numéro de téléphone n'est pas déjà utilisé
        if(phoneToUid($tel) !== false) sendError("Il y a déjà un compte avec ce numéro de téléphone !", HTTP_FORBIDDEN);
        // Récupération de l'image
        // TODO : GESTION D'UPLOAD DE FICHIER PUT OU ROUTE DEDIÉE POUR L'IMAGE
        /*
        if(isset($_FILES["image"])) {
            // Récupération de l'ancienne image et des données de la nouvelle
            $oldImage = basename($user[0]["photo"]);
            $imageData = uploadImage($dir . "/image", $_FILES["image"]);
            // Si l'image est pas correct, on envoie une erreur
            if($imageData["code"] != 1) sendError($imageData["message"], HTTP_BAD_REQUEST);
            $image = $imageData["filename"];
            unlink("$dir/$oldImage");
        }
        */
        // On hash le mot de passe avec l'algo bcrypt2 et avec un cout de 10
        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_BCRYPT, ["cost"=>10]) : false;
        updateUser($uid, $mail, $tel, $age, $studies, $image,$password);
        $data["user"] = selectUser($uidConn, 1);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
}

/**
 * Ajout un instrument à l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserInstrument($data, $authKey, $queryString) {
    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        // Récupération et vérification de l'id de l'instrument
        if($iid = valider("iid", $queryString)) {
            $instrument = selectInstruments($iid);
            if($instrument) {
                if(!haveInstrument($uidConn, $iid)) {
                    insertUserInstrument($iid, $uidConn);
                    $data["instrument"] = $instrument[0];
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
        $uidConn = validUser(authToId($authKey));
        $user = selectUser($idTabs[0]);
        $rid = valider("rid", $queryString);
        $rolesConn = selectUserRoles($uidConn);
        $roleToAdd = selectRole($rid);

        //Verification si l'user existe, que le role existe et que l'user connecté est membre du CA
        if(!$roleToAdd) sendError("Ce role n'existe pas !", HTTP_BAD_REQUEST);
        if(!$user) sendError("Cet utilisateur n'existe pas !", HTTP_BAD_REQUEST);
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas faire cette action", HTTP_FORBIDDEN);
        $uid = $user[0]["id"];
        $roles = selectUserRoles($uid);
        // On vérifie si l'utilisateur n'a pas déjà ce role
        if(array_search($roleToAdd[0]["label"], array_column($roles, "label")) !== false) 
            sendError("Cet utilisateur a déjà ce role", HTTP_FORBIDDEN);
        // Si tout est bon on ecrit la réponse et on l'envoie
        $data["user"] = $user[0];
        $data["role"] = $roleToAdd[0];
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
function verifMail($data, $queryString) {
    if($token = valider("token", $queryString)) {
        if(!($uid = confirmToUser($token))) sendError("Aucun compte associé à ce token !", HTTP_FORBIDDEN);
        activateUser($uid);
        $data["user"] = selectUser($uid)[0];
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Pas de token !", HTTP_UNAUTHORIZED);

}

/**
 * Supprime un instrument de l'utilisateur connecté
 * @param array $data tableau à completer et envoyé
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
*/ 
function delUserInstrument($data, $authKey, $queryString) {
    return $data;
}

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
            if (updateUserAchievement($uidConn, $aid)) {
                $data["achivement"] = $aid;
                sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
            } else {
                sendError("L'utilisateur a déjà l'achievement", HTTP_FORBIDDEN);
            }
        }
        sendError("Aucun achievement a été fourni", HTTP_FORBIDDEN);
    }
    sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
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
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        (($active = valider("active", $queryString)) !== false) ? : $active = null;
        ($label = htmlspecialchars(valider("label", $queryString))) != "" ? : $label = null;
        if($active === null && $label === null) sendError("Il faut modifier au moins 1 paramètre", HTTP_BAD_REQUEST);
        if(updateRole($roleAModif, $label, $active)) {
            $data["role"] = selectRole($roleAModif)[0];
            sendResponse($data, [getStatusHeader(201)]);
        }
        else
            sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
}

/**
 * //TODO ; REVOIR LA FONCTION !!!
 */
function postRole($data, $authKey, $queryString) {

    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $rolesConn = selectUserRoles($uidConn);
        $admin = (array_search("Membre du CA", array_column($rolesConn, "label")) !== false) ? 1 : 0;
    }

    if($active = htmlspecialchars(valider("active", $queryString)) 
       || $label = htmlspecialchars(valider("label", $queryString))){
        if(insertRole($label, $active)){
            $data["instrument"] = array("id" => insertRole($label), "label" => $label);
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
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

        if($label = htmlspecialchars(valider("label", $queryString))) {
            if(updateInstruments($instrument,$label)) {
                $data["instrument"] = array("id" => $instrument, "label" => $label);
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
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

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
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);
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
        if(isInGroup($idUser, $gid) || haveGroupPermission($idUser, $gid)) {
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
        if(isInGroup($idUser, $gid) || haveGroupPermission($idUser, $gid)) {
            $groupsReactsData = selectGroupReaction($idTabs);
            $data["reactions"] = $groupsReactsData;
            sendResponse($data, [getStatusHeader()]);
        }
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);

}

function createUserGroups($data, $authKey, $queryString) {
    if($authKey) {

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
        
        if($user) {
            if(isInGroup($uidConn, $gid) || haveGroupPermission($uidConn, $gid)){
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
    if($authKey) {
        $gid = $idTabs[0];
        $uidConn = authToId($authKey);
        if($uidConn === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

        if(isInGroup($uidConn, $gid) || haveGroupPermission($uidConn, $gid)){
            if($message = htmlspecialchars(valider("content", $queryString))) 
            if(strlen($message) <= 300 ) {
                $answerTo = valider("answerTo", $queryString);
                selectGroupMessage($answerTo, $gid) ? : $answerTo = null;
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
        $i = 0;
        $uidConn = authToId($authKey);
        if($uidConn === false) sendError("Token invalide !", HTTP_FORBIDDEN);
        $role = selectUserRoles($uidConn);
        $notAMember = (array_search("Non Membre", array_column($role, "label")) !== false) ? 1 : 0;
        $postData = selectPosts($notAMember);
        $reactionData = groupby(selectPostReactions(), "pid");
        // On regoupe par emoji
        foreach($reactionData as $pid => $reactionstab) {
            $reactionData[$pid] = groupby($reactionstab, "emoji");
        }
        foreach($postData as $post) {
            $data["posts"][$i]["id"] = $post["id"];
            $data["posts"][$i]["author"] = ["id" => $post["uid"], "firstName" => $post["firstName"], "lastName" => $post["lastName"], "photo" => $post["photo"]];
            $data["posts"][$i]["content"] = $post["content"];
            $data["posts"][$i]["pinned"] = $post["pinned"];
            $data["posts"][$i]["visible"] = $post["visible"];
            $data["posts"][$i]["banner"] = $post["banner"];
            if(isset($reactionData[$post["id"]]))
                $data["posts"][$i]["reactions"] = $reactionData[$post["id"]];
            
            $i++;

        }
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
        if(!$post) sendError("Ce post n'existe pas !", HTTP_BAD_REQUEST);
        if($notAMember && $post[0]["visible"] != 1) sendError("Vous ne pouvez pas accéder à ce post", HTTP_FORBIDDEN);

        $messagesData = selectPostMessages($pid);
        $data["comments"] = array();
        // Creation de la réponse
        foreach($messagesData as $message) {
            $data["comments"][$i]["id"] = $message["id"];
            $data["comments"][$i]["author"] = ["id" => $message["uid"], "firstName" => $message["firstName"], "lastName" => $message["lastName"], "photo" => $message["photo"]];
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
        if(!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);

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

function postAgenda($data, $queryString, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);
}

function postAgendasEvent($data, $idTabs, $queryString, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);
}

function listAgendaEventParticipation($data, $idTabs, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);

}

function listParticipations($data, $idTabs, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);
}

function addParticipations($data, $idTabs, $queryString, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);
}

function postParticipations($data, $idTabs, $queryString, $authKey) {
    sendError("Not implemented yet !", HTTP_NOT_FOUND);
}