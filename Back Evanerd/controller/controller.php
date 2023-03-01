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
 * @param array Tableau de donnée à completer et envoyer
 * @param array queryString sous forme de tableau
 */
function authUser($data, $queryString) {
    if($tel = valider("tel", $queryString))
    if($password = valider("password", $queryString)) {
        if($uidConn = checkUser($tel, $password)) {
            $rolesConn = selectUserRoles($uidConn);
            $data["authToken"] = generateAuthToken($tel);
            $data["user"] = updateAuthToken($uidConn, $data["authToken"])[0];
            $data["user"]["admin"] = searchRole("Membre du CA", $rolesConn);
            $data["user"]["noMember"] = searchRole("Non membre", $rolesConn);
            sendResponse($data, [getStatusHeader()]);
        }
        sendError("identifiant invalide !", HTTP_UNAUTHORIZED);
    }
    sendError("Paramètres invalide !", HTTP_BAD_REQUEST);
}

/**
 * Liste les utilisateur et renvoie la réponse sous format JSON dans le flux STDIN
 * @param array Tableau de donnée à completer et envoyer
 * @param array queryString sous forme de tableau
 */
function listUsers($data, $queryString) {
    $idRole = null;
    if($idRole = valider("idRole", $queryString)) 
        if(!is_id($idRole)) sendError("identifiant role attendu !", HTTP_BAD_REQUEST);
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
    if($authKey) if(authToId($authKey) == $idTabs[0]) $me = true;
    $user = selectUser($idTabs[0], $me);
    if(!$user) sendError("Aucun enregistrement trouvé : id invalide !", HTTP_NOT_FOUND);
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
        $body = fillTemplate(DIR_MAIL_TEMPLATES . "confirm_mail.html", ["LASTNAME" => $lastName, "FIRSTNAME" => $firstName, "TOKEN" => $token]);
        Config::getEmail()->sendMail($mail, "[OSET] Confirmation de votre compte", $body);
        // On envoie l'utilisateur créé
        $data["user"] = selectUser($idUser)[0]; 
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Requête Invalide",HTTP_BAD_REQUEST);
}

/**
 * Renvoie la liste des groupes
 * @param array $data tableau à completer et envoyer
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
 * @param array $data tableau à completer et envoyer
 * @param array $idTabs 
 * @param string $authKey Token d'identification de l'utilisateur
 */
function listGroupMessages($data, $idTabs, $authKey) {
    if(!$authKey) sendError("Il faut vous identifié !", HTTP_UNAUTHORIZED);
    $gid  = $idTabs[0];
    $uidConn = validUser(authToId($authKey));
    // Si il est dans le groupe ou que son role permet d'acceder au groupe
    // Alors il a le droit d'accéder au message du groupe
    if (!isInGroup($uidConn, $gid) && !haveGroupPermission($uidConn, $gid)) sendError("Vous devez être dans le groupe !", HTTP_FORBIDDEN);
        $i = 0;
        $data["groupId"] = $gid;
        $data["messages"] = array();
        $reactionData = groupby(selectGroupReactions($gid), "mid");
        // On regoupe par emoji
        foreach ($reactionData as $mid => $reactionstab) {
            $reactionData[$mid] = groupby($reactionstab, "emoji");
        }
        // on recupére les messages
        $messagesData = selectGroupMessages($gid);
        // Pour chaque message on construit un json et on lui associe ses emojis
        foreach ($messagesData as $message) {
            $data["messages"][$i]["id"] = $message["id"];
            $data["messages"][$i]["author"] = ["id" => $message["uid"], "firstName" => $message["firstName"], "lastName" => $message["lastName"], "photo" => $message["photo"]];
            $data["messages"][$i]["content"] = $message["content"];
            $data["messages"][$i]["pinned"] = $message["pinned"];
            $data["messages"][$i]["answerTo"] = $message["answerTo"];
            if (isset($reactionData[$message["id"]])) {
                $data["messages"][$i]["reactions"] = $reactionData[$message["id"]];
            }
            $i++;
        }
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
}

/**
 * Modifie un utilisateur
 * @param array $data tableau à completer et envoyer
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function putUser($data, $idTabs, $authKey,$queryString) {
    // TODO : mettre à jour la doc
    if($authKey) {
        $uid = $idTabs[0];
        $uidConn = validUser(authToId($authKey));
        $dir = DIR_USERS . $uid;
        if($uidConn != $uid && !searchRole("Membre du CA", selectUserRoles($uid))) 
            sendError("Vous n'avez pas la permissions !", HTTP_UNAUTHORIZED);

        // Récupération et validation des infos :
        $mail = isEmail(htmlspecialchars(valider("mail", $queryString)));
        $tel = isPhoneNumber(valider("tel", $queryString));
        $plainPassword = validString(valider("password", $queryString), 70, 5);
        $age = intval(valider("age", $queryString));
        $studies = validString(htmlspecialchars(valider("studies", $queryString)), 50, 0);
        // Vérification que le numéro de téléphone n'est pas déjà utilisé
        if(phoneToUid($tel) !== false) sendError("Il y a déjà un compte avec ce numéro de téléphone !", HTTP_FORBIDDEN);
        // On hash le mot de passe avec l'algo bcrypt2 et avec un cout de 10
        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_BCRYPT, ["cost"=>10]) : false;
        updateUser($uid, $mail, $tel, $age, $studies,false,$password);
        $data["user"] = selectUser($uidConn, 1);
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
}

/**
 * Ajout un instrument à l'utilisateur connecté
 * @param array $data tableau à completer et envoyer
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserInstrument($data, $authKey, $queryString) {
    if(!$authKey) sendError("Il faut être identifié", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    // Récupération et vérification de l'id de l'instrument
    if ($iid = valider("iid", $queryString)) {
        $instrument = selectInstruments($iid);
        if ($instrument) {
            if (!haveInstrument($uidConn, $iid)) {
                insertUserInstrument($iid, $uidConn);
                $data["instrument"] = $instrument[0];
                sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
            }
            sendError("L'utilisateur a déjà l'instrument", HTTP_FORBIDDEN);
        }
    }
    sendError("Il faut envoyer l'id d'un instrument valide", HTTP_BAD_REQUEST);
}

/**
 * Ajout un role à l'utilisateur connecté
 * @param array $data tableau à completer et envoyer
 * @param array $idTabs paramètre d'url
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 */
function postUserRole($data, $idTabs, $authKey, $queryString) {
    if(!$authKey) sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $user = selectUser($idTabs[0]);
    $rid = valider("rid", $queryString);
    $rolesConn = selectUserRoles($uidConn);
    $roleToAdd = selectRole($rid);
    //Verification si l'user existe, que le role existe et que l'user connecté est membre du CA
    if (!$roleToAdd) sendError("Ce role n'existe pas !", HTTP_BAD_REQUEST);
    if (!$user) sendError("Cet utilisateur n'existe pas !", HTTP_BAD_REQUEST);
    if (!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas faire cette action", HTTP_FORBIDDEN);
    $uid = $user[0]["id"];
    $roles = selectUserRoles($uid);
    // On vérifie si l'utilisateur n'a pas déjà ce role
    if (array_search($roleToAdd[0]["label"], array_column($roles, "label")) !== false)
        sendError("Cet utilisateur a déjà ce role", HTTP_FORBIDDEN);
    // Si tout est bon on ecrit la réponse et on l'envoie
    $data["user"] = $user[0];
    $data["role"] = $roleToAdd[0];
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
}

/**
 * Vérifie l'email d'un utilisateur
 * @param array $data tableau à completer et envoyer
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
 * @param array $data tableau à completer et envoyer
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
*/ 
function delUserInstrument($data, $authKey, $queryString) {
    return $data;
}

/**
 * Réponse de l'api par défaut
 */
function notAction($data) {
    sendError("Cette route n'existe pas ! ", HTTP_NOT_FOUND);
}

function postUserAchievement($data, $authKey, $queryString) {
    // TODO : Faire les conditions pour les achievement
    if(!$authKey) sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    if ($aid = valider("achievement", $queryString)) {
        if (updateUserAchievement($uidConn, $aid)) {
            $data["achivement"] = $aid;
            sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
        }
        sendError("L'utilisateur a déjà l'achievement", HTTP_FORBIDDEN);
    }
    sendError("Aucun achievement a été fourni",HTTP_FORBIDDEN);
}

function delUserAchievement($data, $authKey, $queryString) {

}

function delUserRole($data, $authKey, $queryString) {

}

/**
 * Liste les roles présent dans la base de donnée
 * @param array $data tableau à completer et envoyer
 * @param array $queryString paramètre de requête
 */
function listRoles($data, $queryString) {
    ($active = valider("active", $queryString) !== false) ? : $active = "both";
    $rolesData = selectRoles($active);
    $data["roles"] = $rolesData;
    sendResponse($data, [getStatusHeader()]);
}


function putRoles($data, $idTabs, $authKey, $queryString) {
    if(!$authKey) sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $rolesConn = selectUserRoles($uidConn);
    $roleAModif = $idTabs[0];
    if (!searchRole("Membre du CA", $rolesConn)) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);
    (($active = valider("active", $queryString)) !== false) ?: $active = null;
    ($label = htmlspecialchars(valider("label", $queryString))) != "" ?: $label = null;
    if ($active === null && $label === null) sendError("Il faut modifier au moins 1 paramètre", HTTP_BAD_REQUEST);
    if (updateRole($roleAModif, $label, $active)) {
        $data["role"] = selectRole($roleAModif)[0];
        sendResponse($data, [getStatusHeader(201)]);
    } else
        sendError("Erreur lors de la modification", HTTP_FORBIDDEN);
}

/**
 * Créer un role dans la base de donnée
 * @param array $data tableau à completer et à envoyer
 * @param array $queryString paramètre de requête
 * @param string $authKey Token d'identification de l'utilisateur
 * @return void
 */
function postRole($data, $authKey, $queryString) {
    if(!$authKey) sendError("Il faut être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $admin = searchRole("Membre du CA", selectUserRoles($uidConn));
    if(!$admin) sendError("Vous ne pouvez pas effectuer cette action !", HTTP_FORBIDDEN);
    $active = valider("active", $queryString) ? 1 : 0;
    if($label = validString(htmlspecialchars(valider("label", $queryString)), 50, 1)) {
        $rid = insertRole($label, $active);
        $data["role"] = selectRole($rid)[0];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Il faut au moins un label", HTTP_BAD_REQUEST);
}
/**
 * Liste les instruments disponible dans la base de donnée
 * @param array $data tableau à completer et envoyer
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
 * @param array $data tableau à completer et envoyer
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
        $newId = getNewGid();
        $dir = DIR_GROUPS . $newId;
        $uidConn = validUser(authToId($authKey));
        $image = "default.png";
        if(!is_dir($dir)) mkdir($dir);
        if($title = validString(valider("title", $queryString), 70, 3)) {
            if(isset($_FILES["image"])) {
                $imageInfo = uploadImage("$dir/image", $_FILES["image"]);
                if($imageInfo["code"] != 1) sendError($imageInfo["message"], HTTP_FORBIDDEN);
                $image = basename($imageInfo["filename"]);
            }
            $gid = insertGroup($uidConn, $image, $title);
            $data["group"] = selectGroup($gid)[0];
            sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
        }
        else sendError("il faut spécifier un titre", HTTP_FORBIDDEN);
    }
    sendError("il faut être identifié !", HTTP_UNAUTHORIZED);
}

/**
 * Ajoute un utilisateur dans un groupe
 * @param array $data tableau à completer et envoyer
 * @param string $authKey Token d'identification de l'utilisateur
 */
function addUsersGroups($data, $idTabs, $authKey) {
    if($authKey) {
        $uidConn = authToId($authKey); // uid de l'utilisateur connecté
        if($uidConn === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);

        $uidToAdd = $idTabs[1]; // uid de l'utilisateur à ajouter
        $gid = $idTabs[0];
        $user = selectUser($uidToAdd);
        if(!$user) sendError("Cet utilisateur n'existe pas" , HTTP_BAD_REQUEST);
        if(!isInGroup($uidConn, $gid) && !haveGroupPermission($uidConn, $gid)) sendError("Vous ne pouvez pas ajouter un membre dans ce groupe !" , HTTP_UNAUTHORIZED);
        if(isInGroup($uidToAdd, $gid)) sendError("Cet utilisateur est déjà dans ce groupe", HTTP_UNAUTHORIZED);
        insertIntoGroup($uidToAdd, $gid);
        $data["user"] = $user[0];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Vous devez vous identifier !", HTTP_UNAUTHORIZED);
}

/**
 * Permet d'envoyer un message dans le groupe
 * @param array $data tableau à completer et envoyer
 * @param string $authKey Token d'identification de l'utilisateur
 * @param array $idTabs paramètre d'url 
 * @param array $queryString paramètre de requête
 * @param 
*/
function postMessagesGroups($data, $idTabs, $authKey, $queryString) {
    if(!$authKey) sendError("Vous devez être identifié pour envoyer un message", HTTP_UNAUTHORIZED);
    $gid = $idTabs[0];
    $uidConn = authToId($authKey);
    if ($uidConn === false) sendError("Token invalide !", HTTP_UNAUTHORIZED);
    if (!isInGroup($uidConn, $gid) && !haveGroupPermission($uidConn, $gid)) sendError("Vous devez être dans le groupe pour pouvoir envoyer un message", HTTP_BAD_REQUEST);
    if ($message = validString(htmlspecialchars(valider("content", $queryString)), 300, 1)) {
        $answerTo = valider("answerTo", $queryString);
        selectGroupMessage($answerTo, $gid) ?: $answerTo = null;
        $mid = insertGroupMessage($uidConn, $gid, $message, $answerTo);
        $data["message"] = ["id" => $mid, "content" => $message, "pinned" => 0, "answerTo" => $answerTo];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Vous ne pouvez pas envoyer un message vide ", HTTP_BAD_REQUEST);
}

/**
 * Permet de lister les posts visible par l'utilisateur connecté
 * @param array $data tableau à completer et envoyer
 * @param string $authKey Token d'identification de l'utilisateur
 */
function listPosts($data, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $i = 0;
    $uidConn = validUser(authToId($authKey));
    $notAMember = searchRole("Non Membre", selectUserRoles($uidConn));
    $postData = selectPosts($notAMember, $uidConn);
    foreach ($postData as $post) {
        $data["posts"][$i]["id"] = $post["id"];
        $data["posts"][$i]["author"] = ["id" => $post["uid"], "firstName" => $post["firstName"], "lastName" => $post["lastName"], "photo" => $post["photo"]];
        $data["posts"][$i]["content"] = $post["content"];
        $data["posts"][$i]["pinned"] = $post["pinned"];
        $data["posts"][$i]["visible"] = $post["visible"];
        $data["posts"][$i]["banner"] = $post["banner"];
        $data["posts"][$i]["liked"] = $post["liked"];
        $i++;
    }
    sendResponse($data, getStatusHeader());
}

function listPostReactions($data, $idTabs, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $pid = $idTabs[0];
    $notAMember = searchRole("Non Membre", selectUserRoles($uidConn));
    $postData = selectPost($pid);
    if (!$postData) sendError("Ce post n'existe pas !", HTTP_BAD_REQUEST);
    if ($notAMember && !$postData[0]["visible"]) sendError("VOus n'avez pas les permissions !", HTTP_FORBIDDEN);
    $reactionData = groupby(selectPostReactions($pid), "emoji");
    $data["postId"] = $pid;
    $data["reactions"] = $reactionData;
    sendResponse($data, [getStatusHeader(HTTP_OK)]);

}
/**
 * Permet d'envoyer un commentaire sous un post
 * @param array $data tableau à completer et envoyer
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

function listAgendas($data, $queryString, $authKey) {
    if($authKey) {
        $uidConn = validUser(authToId($authKey));
        $type = valider("type", $queryString);
        if(!$type) $type = "extra"; 
        $agendas = selectCalendar($uidConn,null, $type);
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

function postAgenda($data, $queryString, $authKey) {
    if(!$authKey) sendError("Vous être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    //TODO : faire les permissions
    //TODO : ajouter l'uid
    if ($title = validString(htmlspecialchars(valider("title", $queryString)), 30, 1))
        if ($extra = valider("type", $queryString)) {
            $extra = ($extra == "intra") ? 0 : 1;
            $aid = insertAgenda($title, $extra, $uidConn);
            $data["agenda"] = ["id" => $aid, "title" => $title, "extra" => $extra];
            sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
        }
    sendError("Requête invalide !", HTTP_BAD_REQUEST);
}

/**
 * Permet d'ajouter un évènement à un agenda
 * @param array $data tableau à completer et envoyer
 * @param string $authKey Token d'identification de l'utilisateur
 * @param array $idTabs paramètre d'url
 */
function postAgendasEvent($data, $idTabs, $queryString, $authKey) {
    //TODO : Vérifier les permissions
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    validUser(authToId($authKey));
    $aid = $idTabs[0];
    if(!selectAgenda($aid)) sendError("Cet agenda n'existe pas !", HTTP_BAD_REQUEST);
    if($event = validString(htmlspecialchars(valider("event", $queryString)), 30, 1))
    if($start = validDate(valider("start", $queryString)))
    if($end = validDate(valider("end", $queryString))) {
        $eid = insertEvent($aid, $event, $start, $end);
        $data["event"] = ["id" => $eid, "event" => $event, "start" => $start, "end" => $end];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Requête invalide !", HTTP_BAD_REQUEST);
}

function getUserRoles($data, $idTabs) {
    $uid = validUser($idTabs[0]);
    if(!selectUser($uid)) sendError("Cet utilisateur n'existe pas !", HTTP_FORBIDDEN);
    $data["userId"] = $uid;
    $data["roles"] = selectUserRoles($uid);
    sendResponse($data, [getStatusHeader(HTTP_OK)]);

}

function getUserInstruments($data, $idTabs){
    $uid = $idTabs[0];
    if(!selectUser($uid)) sendError("Cet utilisateur n'existe pas !", HTTP_FORBIDDEN);
    $data["userId"] = $uid;
    $data["instruments"] = selectUserInstruments($uid);
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}

function postImage($data, $idTabs,$authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uid = $idTabs[0];
    $uidConn = validUser(authToId($authKey));
    $dir = DIR_USERS . $uid;
    if ($uidConn != $uid && !searchRole("Membre du CA", selectUserRoles($uid)))
        sendError("Vous n'avez pas la permissions !", HTTP_UNAUTHORIZED);

    $user = selectUser($uid, 1);
    if (isset($_FILES["image"])) {
        // Récupération de l'ancienne image et des données de la nouvelle
        $oldImage = basename($user[0]["photo"]);
        $imageData = uploadImage($dir . "/image", $_FILES["image"]);
        // Si l'image est pas correct, on envoie une erreur
        if ($imageData["code"] != 1) sendError($imageData["message"], HTTP_BAD_REQUEST);
        $image = basename($imageData["filename"]);
        unlink("$dir/$oldImage");
        updateUser($uid, false, false, false, false, $image);
        $data["user"] = $user[0];
        $data["user"]["photo"] = getBaseLink() . "/users/$uid/$image";
        sendResponse($data, [getStatusHeader(HTTP_OK)]);
    }
    sendError("Aucune image reçu !", HTTP_BAD_REQUEST);
    
}

function postMessageReactions($data, $idTabs, $authKey, $queryString) {
    if(!$authKey) sendError("Vous devez être identifié", HTTP_UNAUTHORIZED);
    if (!($emoji = valider("emoji", $queryString))) sendError("Vous devez saisir un emoji", HTTP_BAD_REQUEST);
    $uidConn = validUser(authToId($authKey));
    $gid = $idTabs[0];
    $mid = $idTabs[1];
    $msg = selectGroupMessage($mid, $gid);
    $reac = selectGroupReaction($mid, $uidConn, $emoji);
    $data["reaction"] = ["uid" => $uidConn, "mid" => $mid];
    if (haveGroupPermission($uidConn, $gid)) sendError("VOus n'avez pas les permissions !", HTTP_FORBIDDEN);
    if (!$msg) sendError("Ce message n'existe pas !", HTTP_BAD_REQUEST);
    $data["groupId"] = $gid;
    $data["messageId"] = $mid;
    if (!$reac) {
        insertGroupMessageReaction($mid, $uidConn, $emoji);
        $data["reaction"] = $emoji;
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    deleteGroupMessageReaction($mid, $uidConn, $emoji);
    $data["reaction"] = null;
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);

}

function postPost($data, $authKey,$queryString) {
    if($authKey) sendError("Vous devez être identifié", HTTP_UNAUTHORIZED);
    $newId = getNewPid();
    $dir = DIR_POSTS . $newId;
    $uidConn = validUser(authToId($authKey));
    if (searchRole("Non Membre", selectUserRoles($uidConn))) sendError("Vous n'avez pas la permission !", HTTP_UNAUTHORIZED);
    if ($content = validString(valider("content"), 300, 0));
    if (($pinned = valider("visible", $queryString)) !== false)
    if (($visible = valider("pinned", $queryString)) !== false)
        if (isset($_FILES["banner"])) {
            if (!is_dir($dir)) mkdir($dir);
            $imageInfo = uploadImage("$dir/image", $_FILES["banner"]);
            if ($imageInfo["code"] != 1)  sendError($imageInfo["message"], HTTP_BAD_REQUEST);
            $image = basename($imageInfo["filename"]);
            $pid = insertPost($content, $image, $pinned, $visible, $uidConn);
            $data["post"] = selectPost($pid)[0];
            sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
        }
    sendError("Paramètres invalide !", HTTP_BAD_REQUEST);
}

function postPostLike($data, $idTabs, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $pid = $idTabs[0];
    $post = selectPost($pid);
    $like = selectLiked($pid, $uidConn);
    $data["like"] = ["uid" => $uidConn, "pid" => $pid];
    // TODO : vérifier que l'user peut voir le post (ajouter un param optionnel à selectPost)
    if (!$post) sendError("Le post n'existe pas !", HTTP_BAD_REQUEST);
    if (!$like) {
        insertLiked($uidConn, $pid, 1);
        $data["like"]["liked"] = 1;
    } else {
        if ($like[0]["Liked"]) liked($pid, $uidConn, 0);
        else liked($pid, $uidConn, 1);

        $data["like"]["liked"] = !$like[0]["Liked"];
    }
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
}

function putMessagePinned($data, $idTabs, $authKey) { // A TESTER
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $gid = $idTabs[0]; $mid = $idTabs[1];
    $uidConn = validUser(authToId($authKey));
    //TODO : vérifier permissions avec role
    if(!isInGroup($uidConn, $gid)) sendError("Vous n'avez pas les permissions !", HTTP_FORBIDDEN);
    $msg = selectGroupMessage($mid, $gid);
    $pinned = $msg[0]["pinned"];
    if (!$msg) sendError("Le message n'existe pas !", HTTP_BAD_REQUEST);
    updateMessage($mid, false, (int) !$pinned);
    $data["message"] = $msg[0];
    $data["message"]["pinned"] = (int) !$pinned;
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
}

function putPostPinned($data, $idTabs, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $pid = $idTabs[0];
    $uidConn = validUser(authToId($authKey));
    if(!searchRole("Membre du CA", selectUserRoles($uidConn))) sendError("Vous n'avez pas les permissions !", HTTP_FORBIDDEN);
    $post = selectPost($pid);
    $pinned = $post[0]["pinned"];
    if (!$post) sendError("Le post n'existe pas !", HTTP_BAD_REQUEST);
    updatePost($pid, false, (int) !$pinned);
    $data["post"] = $post[0];
    $data["post"]["pinned"] = (int) !$pinned;
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);

}

function postEventParticipations($data, $idTabs, $queryString, $authKey) {
    if(!$authKey)  sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $eid = $idTabs[0];
    $event = selectEvent($eid, "extra");
    if (!$event) sendError("Vous ne pouvez répondre à cette événement !", HTTP_FORBIDDEN);
    if (selectParticipations($eid, $uidConn)) sendError("Vous avez déjà répondu !", HTTP_FORBIDDEN);
    $participation = validString(valider("participation", $queryString), 1, 1);
    if ($participation != "y" && $participation != "n" && $participation != "j")
        $participation = "n";
    insertParticipation($uidConn, $eid, $participation);
    $data["participation"] = ["uid" => $uidConn, "participation" => $participation, "eid" => $eid];
    sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
}

function postEventCalls($data, $idTabs, $queryString, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $eid = $idTabs[0];
    //TODO : vérifier les permissions d'écriture
    $event = selectEvent($eid, "intra");
    if (!$event) sendError("Vous ne pouvez répondre à cette événement !", HTTP_FORBIDDEN);
    if (!$event[0]["read"]) sendError("Vous n'avez pas les permissions !", HTTP_FORBIDDEN);
    if (selectCallMembers($eid, $uidConn)) sendError("Vous avez déjà répondu !", HTTP_FORBIDDEN);
    if (($present = valider("present", $queryString)) !== false) {
        $reason_desc = validString(htmlspecialchars(valider("reason")), 180, 0);
        if (!$present && !$reason_desc) sendError("Motif obligateur en cas d'absence !", HTTP_FORBIDDEN);
        if ($present) $reason_desc = null;
        insertCall($uidConn, $eid, $present, $reason_desc);
        $data["call"] = ["uid" => $uidConn, "present" => $present, "eid" => $eid, "reason_desc" => $reason_desc];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Requête invalide !", HTTP_BAD_REQUEST); 
}

function listEventParticipations($data, $idTabs, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $eid = $idTabs[0];
    validUser(authToId($authKey));
    $event = selectEvent($eid, "extra");
    if (!$event) sendError("Vous n'avez pas accès à cet évenement", HTTP_FORBIDDEN);
    $data["eventId"] = $eid;
    $agendaData = selectParticipations($eid);
    if($agendaData) $data["participationsRatio"] = $agendaData[0]["ratio"];
    $agendaData = groupby($agendaData, "participation");
    foreach ($agendaData as $key => $participationData) {
        $i = 0;
        $data["participations"][$key] = array();
        foreach ($participationData as $agenda) {
            $data["participations"][$key][$i]["user"] = ["id" => $agenda["uid"], "firstName" => $agenda["firstName"], "lastName" => $agenda["lastName"], "photo" => $agenda["photo"]];
            $i++;
        }
    }
    sendResponse($data, [getStatusHeader(HTTP_OK)]);
}

function listEventCalls($data, $idTabs, $authKey) {
    if(!$authKey)  sendError("Il faut vous identifier", HTTP_UNAUTHORIZED);
    $i = 0;
    $uidConn = validUser(authToId($authKey));
    $eid = $idTabs[0];
    $event = selectEvent($eid, "intra", $uidConn);
    if (!$event) sendError("Cet évenement n'existe pas !", HTTP_BAD_REQUEST);
    if (!$event[0]["write"]) sendError("Vous n'avez pas les droits !", HTTP_FORBIDDEN);
    $data["eventId"] = $eid;
    $data["calls"] = array();
    $callsData = selectCallMembers($eid);
    foreach ($callsData as $calls) {
        $data["calls"][$i]["user"] = array("uid" => $calls["uid"], "firstName" => $calls["firstName"], "lastName" => $calls["lastName"]);
        $data["calls"][$i]["present"] = $calls["present"];
        if (!$calls["present"]) {
            $data["calls"][$i]["reason_desc"] = $calls["reason_desc"];
        }
        $i++;
    }
    sendResponse($data, [getStatusHeader()]);
} 


function listEvents($data, $queryString, $authKey) {
    if(!$authKey) sendError("Vous devez vous identifier", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $type = valider("type", $queryString);
    if(!$type) $type = "extra";
    $agendaEvents = selectEvents($uidConn, $type);
    $data["events"] = $agendaEvents;
    sendResponse($data, [getStatusHeader()]);
}

function postAgendasPermissions($data, $idTabs, $queryString, $authKey) {
    if(!$authKey) sendError("Vous devez être identifié !", HTTP_UNAUTHORIZED);
    $uidConn = validUser(authToId($authKey));
    $aid = $idTabs[0];
    $agenda = selectAgenda($aid, $uidConn);
    if (!$agenda) sendError("Ce calendrier n'existe pas !", HTTP_FORBIDDEN);
    //TODO : à finir
    sendError("NOT IMPLEMENTED", HTTP_NOT_FOUND);
}

function postPostMessage($data, $idTabs, $authKey, $queryString) {
    if(!$authKey) sendError("Vous devez être identifié pour envoyer un message", HTTP_UNAUTHORIZED);
    $pid = $idTabs[0];
    $uidConn = validUser(authToId($authKey));
    $notAMember = searchRole("Non Membre", selectUserRoles($uidConn));
    if(!selectPost($pid, $notAMember)) sendError("Impossible de commenter ce post !", HTTP_FORBIDDEN);
    
    if($message = validString(htmlspecialchars(valider("content", $queryString)), 180, 0)) {
        $answerTo = valider("answerTo", $queryString);
        selectPostMessage($answerTo, $pid) ? : $answerTo = null;
        $mid = insertPostMessage($pid, $uidConn, $message, $answerTo);
        $data["comment"] = ["id" => $mid, "content" => $message, "pinned" => 0, "answerTo" => $answerTo];
        sendResponse($data, [getStatusHeader(HTTP_CREATED)]);
    }
    sendError("Vous ne pouvez pas envoyer un message vide ", HTTP_BAD_REQUEST);
}