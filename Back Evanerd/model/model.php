<?php

include_once("includes/Config.php");
/**
 * Change le token d'authentification de l'utilisateur
 * @param int $idUser
 * @param string $authToken
 */
function updateAuthToken($idUser, $authToken) {
    $db = Config::getDatabase();
    $sql = "UPDATE Users SET authToken = ? WHERE id = ?";
    $db->SQLUpdate($sql, [$authToken, $idUser]);

    return getUser($idUser, 1);
}

/**
 * Retourne l'id de l'utilisateur en fonction de son token d'identification
 * @param string $authToken
 * @return int|false
 */
function authToId($authToken) {
    $db = Config::getDatabase();
    $sql = "SELECT Users.id FROM Users WHERE Users.authToken = ?";

    return $db->SQLGetChamp($sql,[$authToken]);
}

/**
 * Récupère les logins et l'info d'activation du compte en fonction du numéro de téléphone
 * @param string $tel
 * @return array 
 */
function getUserCredentials($tel) {
    $db = Config::getDatabase();
    $sql= "SELECT id, tel, activation, password FROM users WHERE tel = ?;";
    
    return Database::parcoursRs($db->SQLSelect($sql, [$tel]));
}

// Requete sur les users
/**
 * Récupère la listes de utilisateurs
 * @param int $idRole permet de filtrer par role
 * @return array
 */
function getUsers($idRole=null){
    $db = Config::getDatabase();
    $sql = "SELECT U.id, U.firstName, U.lastName, U.sex, U.age, U.studies, U.photo, U.activation FROM Users AS U";
    if ($idRole != null) {
        $sql .= " JOIN User_Roles AS UR ON U.id = UR.uid WHERE UR.rid = ? ";
        $params = [$idRole];
        return Database::ParcoursRs($db->SQLSelect($sql, $params));
    } else {
        return Database::ParcoursRs($db->SQLSelect($sql));
    }
}
/**
 * Récupère un utilisateur spécifique
 * @param int $idUser l'identifiant de l'utilisateur
 * @param bool $me permet de savoir si la demande provient de l'utilisateur connecté
 */
function getUser($idUser, $me = 0){
    $db = Config::getDatabase();
    $publicInfo = "U.id, U.firstName, U.lastName, U.sex, U.age, U.studies, U.photo, U.activation ";
    $privateInfo = ", U.tel, U.mail ";
    $sql = "SELECT " . $publicInfo;

    if($me) $sql .= $privateInfo;

    $sql .= "FROM Users AS U WHERE U.id = ?;";
    $params = [$idUser];
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function modifUser($idUser,$mail = null,$tel = null,$age = null,$studies = null,$password=null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE Users SET";
    if ($mail != null){
        $sql = $sql . ",mail = ?";
        array_push($params, $mail);
    }
    if ($tel != null){
        $sql = $sql . ",tel = ?";
        array_push($params, $tel);
    }
    if ($age != null){
        $sql = $sql . ",age = ?";
        array_push($params, $age);
    }
    if ($studies != null){
        $sql = $sql . ",studies = ?";
        array_push($params, $studies);
    }
    if ($password != null){
        $sql = $sql . ",password = ?";
        array_push($params, $password);
    }
    array_push($params,$idUser);
    $sql = $sql . "WHERE id = ?";
    return Database::parcoursRs($db->SQLUpdate($sql, $params));
}
/*
function deleteUser($idUser){
    $db = Config::getDatabase();

    $user = getUser($idUser);

    $Tabpid = getPostIds($idUser);

    Suppression des posts concernant l'utilisateur et des commentaires et réaction de ces posts 
    foreach($Tabpid as $post){
        $params = [$post];
        // Suppression Likes de Posts
        $sql = "DELETE * FROM Post_Likes WHERE pid = ?";
        $db->SQLDelete($sql, $params);

        // Suppression Commentaires des Posts de l'utilisateur
        $sql = "DELETE * FROM Post_Commentaires WHERE pid = ?";
        $db->SQLDelete($sql, $params);

        // Suppression des Reactions aux Posts de l'utilisateur 
        $sql = "DELETE * FROM Post_Commentaires WHERE pid = ?";
        $db->SQLDelete($sql, $params);

    }

    $params = [$idUser];

    // Suppression Likes posts de l'utilisateur
    $sql = "DELETE * FROM Post_Likes WHERE uid = ?";
    $db->SQLDelete($sql, $params);
    
    // Suppression Commentaires Posts de l'utilisateur
    $sql = "DELETE * FROM Post_Commentaires WHERE uid = ?";
    $db->SQLDelete($sql, $params);
    
    // Suppression des Reactions Posts de l'utilisateur 
    $sql = "DELETE * FROM Post_Commentaires WHERE uid = ?";
    $db->SQLDelete($sql, $params);

    // Suppression des Posts de l'utilisateur 
    $sql = "DELETE * FROM Posts WHERE uid = ?";
    $db->SQLDelete($sql, $params);

    /************************************************************************************************/
    /* Suppression des posts concernant l'utilisateur et des commentaires et réaction de ces posts 
}*/

function createUser($firstname,$lastname,$mail,$tel,$password,$age,$studies = "" , $sex = 0, $image = "default.png"){
    $db = Config::getDatabase();
    $params = [$firstname,$lastname,$mail,$tel,$password,$age, $studies, $sex, $image, generateEmailConfirmToken($tel)];
    $sql = "INSERT INTO Users (firstname, lastname, mail, tel, password, age, studies, sex, photo, activation, confirmToken) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
    
    return $db->SQLInsert($sql, $params);
}




function updateUserAchievement($uid,$aid){
    $db = Config::getDatabase();
    $params = [$aid, $uid];
    $sql = "INSERT INTO User_Achievements (achivid,uid) VALUES(?,?)";
    $ajout = Database::parcoursRs($db->SQLInsert($sql, $params));

    $sql = "UPDATE Users SET achivid = ? WHERE id = ?";
    $db->SQLUpdate($sql, $params);
    return $ajout;
}


function addUserRole($uid,$rid){
    $db = Config::getDatabase();
    $params = [$rid, $uid];
    $sql = "INSERT INTO User_Roles (rid,uid) VALUES(?,?)";
    return ($db->SQLInsert($sql, $params));
}

/*function getPostIds($idUser){
    $db = Config::getDatabase();
    $params = [$idUser];
    $sql = "SELECT id FROM Posts WHERE author = ?";
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}*/

// Requete sur les Groupes
function getGroups($uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $sql = "SELECT * INTO User_Groups WHERE uid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function getGroupsPerm($gid){

    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT GP.rid, R.label INTO Groups_Perms AS GP 
    JOIN  Roles AS R ON  GP.rid = R.id
    WHERE gid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function getMessages($gid){
    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT * INTO Group_Messages WHERE gid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}


function getMessagesReactions($mid){
    $db = Config::getDatabase();
    $params = [$mid];
    $sql = "SELECT * INTO Group_Message_Reactions WHERE mid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function insertGroup($uid,$image=null,$title=null){
    $User = getUser($uid);
    $db = Config::getDatabase();
    if ($title == null)
        $title = $User[0]["firstName"] . " " . $User[0]["lastName"];
    $params = [$title,$image];
    $sql = "INSERT INTO Groups(titre,image) VALUES(?,?)";
    $gid = $db->SQLInsert($sql, $params);
    addToGroup($uid, $gid);
}

function addToGroup($uid,$gid){
    $db = Config::getDatabase();
    $params = [$uid,$gid];
    $sql = "INSERT INTO User_Groups(uid,gid) VALUES (?,?) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));
}


?>
