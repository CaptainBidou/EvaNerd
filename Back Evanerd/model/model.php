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

    return selectUser($idUser, 1);
}


function isInGroup($idUser, $idGroup) {
    $db = Config::getDatabase();
    $sql = "SELECT User_Groups.uid FROM User_Groups 
            WHERE User_Groups.gid = ? AND User_Groups.uid = ?";
    
    return $db->SQLGetChamp($sql, [$idGroup, $idUser]);
}

function haveGroupPermission($idUser, $idGroup) {
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Roles 
            JOIN Groups_Perms
                ON Groups_Perms.rid = Roles.id
            JOIN User_groups
                ON User_Groups.gid = Groups_Perms.gid
            JOIN Users
                ON Users.id = User_Groups.uid;
            WHERE Users.id = ? AND User_Groups.gid = ?";

    return Database::parcoursRs($db->SQLSelect($sql, [$idUser, $idGroup]));
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
function selectUsers($idRole=null){
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
function selectUser($idUser, $me = 0){
    $db = Config::getDatabase();
    $publicInfo = "U.id, U.firstName, U.lastName, U.sex, U.age, U.studies, U.photo, U.activation ";
    $privateInfo = ", U.tel, U.mail ";
    $sql = "SELECT " . $publicInfo;

    if($me) $sql .= $privateInfo;

    $sql .= "FROM Users AS U WHERE U.id = ?;";
    $params = [$idUser];
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function updateUser($idUser,$mail = null,$tel = null,$age = null,$studies = null,$password=null){
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

function insertUser($firstname,$lastname,$mail,$tel,$password,$age,$studies = "" , $sex = 0, $image = "default.png"){
    $db = Config::getDatabase();
    $params = [$firstname,$lastname,$mail,$tel,$password,$age, $studies, $sex, $image, generateEmailConfirmToken($tel)];
    $sql = "INSERT INTO Users (firstname, lastname, mail, tel, password, age, studies, sex, photo, activation, confirmToken) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
    
    return $db->SQLInsert($sql, $params);
}

function insertUserInstrument($iid,$uid){
    $db = Config::getDatabase();
    $params = [$iid, $uid];
    $sql = "INSERT INTO User_Instruments(iid,uid) VALUES(?,?)";
    return ($db->SQLInsert($sql, $params));
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


function insertUserRole($uid,$rid){
    $db = Config::getDatabase();
    $params = [$rid, $uid];
    $sql = "INSERT INTO User_Roles (rid,uid) VALUES(?,?)";
    return ($db->SQLInsert($sql, $params));
}

function checkMail($code){
    $db = Config::getDatabase();
    $params = [$code];
    $sql = "SELECT * FROM Users WHERE confirmToken = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));

}

function deleteUserInstrument($uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $sql = "DELETE * FROM User_Instruments WHERE uid = ?";
    $db->SQLDelete($sql, $params);
}

function deleteUserAchievement($uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $sql = "UPDATE Users SET achivid = null WHERE id = ?";
    $db->SQLUpdate ($sql, $params);
    $sql = "DELETE * FROM User_Achievement WHERE uid = ?";
    $db->SQLDelete($sql, $params);
}

function deleteUserRole($uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $sql = "DELETE * FROM User_RolesWHERE uid = ?";
    $db->SQLDelete($sql, $params);
}



function selectRoles(){
    $db = Config::getDatabase();
    $params = [];
    $sql = "SELECT * FROM Roles ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function updateRole($rid,$label = null,$active = null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE Roles SET ";
    if ($label != null){
        $sql = $sql . ",label = ?";
        array_push($params, $label);
    }
    if ($active != null){
        $sql = $sql . ",active = ?";
        array_push($params, $active);
    }
    array_push($params, $rid);
    $sql = "WHERE id = ?";
    return $db->SQLUpdate($sql, $params);
}

function insertRole($label,$active=1){
    $db = Config::getDatabase();
    $params = [$label,$active];
    $sql = "INSERT INTO Roles(label,active) VALUES (?,?) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));

}


function selectInstruments(){
    $db = Config::getDatabase();
    $params = [];
    $sql = "SELECT * FROM Instruments ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function updateInstruments($rid,$label = null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE Instruments SET ";
    if ($label != null){
        $sql = $sql . ",label = ?";
        array_push($params, $label);
    }
    array_push($params, $rid);
    $sql = "WHERE id = ?";
    return $db->SQLUpdate($sql, $params);
}

function insertInstruments($label){
    $db = Config::getDatabase();
    $params = [$label];
    $sql = "INSERT INTO Roles(label) VALUES (?) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));

}

function selectAchievements(){
    $db = Config::getDatabase();
    $params = [];
    $sql = "SELECT * FROM Achievements ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function updateAchievements($aid,$label = null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE Achievements SET ";
    if ($label != null){
        $sql = $sql . ",label = ?";
        array_push($params, $label);
    }
    array_push($params, $aid);
    $sql = "WHERE id = ?";
    return $db->SQLUpdate($sql, $params);
}

function insertAchievement($label){
    $db = Config::getDatabase();
    $params = [$label];
    $sql = "INSERT INTO Achievements(label) VALUES (?) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));

}

/*function getPostIds($idUser){
    $db = Config::getDatabase();
    $params = [$idUser];
    $sql = "SELECT id FROM Posts WHERE author = ?";
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}*/

// Requete sur les Groupes
function selectGroups($uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $sql = "SELECT DISTINCT Groups.id, Groups.titre 
            FROM User_Groups
            JOIN Groups ON Groups.id = User_Groups.gid; 
            WHERE uid = ?";
            
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectGroupsPerm($gid){

    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT GP.rid, R.label INTO Groups_Perms AS GP 
    JOIN  Roles AS R ON  GP.rid = R.id
    WHERE gid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectGroupMessages($gid){
    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT Group_Messages.*, Users.firstName, Users.lastName FROM `Group_Messages` JOIN Users ON Users.id = Group_Messages.uid WHERE Group_Messages.gid = ?;";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}


function selectGroupReaction($mid){
    $db = Config::getDatabase();
    $params = [$mid];
    $sql = "SELECT * FROM Group_Message_Reactions WHERE mid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectGroupReactions($gid) {
    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT Group_Message_Reactions.emoji, Group_Message_Reactions.mid, Users.id AS uid, Users.firstName, Users.lastName
            FROM Group_Messages
            JOIN Group_Message_Reactions
                ON Group_Message_Reactions.mid = Group_Messages.id
            JOIN users
                ON Group_Message_Reactions.uid = Users.id
            WHERE Group_Messages.gid = ?;";

    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function insertGroup($uid,$image=null,$title=null){
    $User = selectUser($uid);
    $db = Config::getDatabase();
    if ($title == null)
        $title = $User[0]["firstName"] . " " . $User[0]["lastName"];
    $params = [$title,$image];
    $sql = "INSERT INTO Groups(titre,image) VALUES(?,?)";
    $gid = $db->SQLInsert($sql, $params);
    insertIntoGroup($uid, $gid);
}

function insertIntoGroup($uid,$gid){
    $db = Config::getDatabase();
    $params = [$uid,$gid];
    $sql = "INSERT INTO User_Groups(uid,gid) VALUES (?,?) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));
}


function insertGroupMessage($uid,$gid,$content,$answerTo = null){
    $db = Config::getDatabase();
    $params = [$uid,$gid,$content,$answerTo];
    $sql = "INSERT INTO Group_Messages(uid,gid,content,answerTo,pinned) VALUES (?,?,?,?,null) ";
    return Database::parcoursRs(($db->SQLInsert($sql, $params)));
}


function selectPosts(){
    $db = Config::getDatabase();
    $params = [];
    $sql = "SELECT * FROM Posts ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectPostReactions($pid){
    $db = Config::getDatabase();
    $params = [$pid];
    $sql = "SELECT * FROM Post_Reactions WHERE pid = ? ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectPostMessages($pid){
    $db = Config::getDatabase();
    $params = [$pid];
    $sql = "SELECT * FROM Post_Comments WHERE pid = ? ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectCalendar(){
    $db = Config::getDatabase();
    $params = [];
    $sql = "SELECT * FROM Agendas ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectEvents($aid){
    $db = Config::getDatabase();
    $params = [$aid];
    $sql = "SELECT * FROM Agendas WHERE aid = ? ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectCallMembers($aeid){
    $db = Config::getDatabase();
    $params = [$aeid];
    $sql = "SELECT * FROM Posts WHERE aeid = ? ";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}


?>
