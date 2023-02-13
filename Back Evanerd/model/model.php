<?php
include_once("includes/Config.php");

function phoneToUid($tel) {
    $db = Config::getDatabase();
    $sql = "SELECT Users.tel FROM Users WHERE Users.tel = ?;";
    return $db->SQLGetChamp($sql, [$tel]);
}

function getNewUid() {
    $db = Config::getDatabase();
    $sql = "SELECT MAX(Users.id)+1 FROM `Users`";
    return $db->SQLGetChamp($sql);
}

function getNewPid() {
    $db = Config::getDatabase();
    $sql = "SELECT MAX(Posts.id)+1 FROM `Posts`";
    return $db->SQLGetChamp($sql);
}
function getNewGid() {
    $db = Config::getDatabase();
    $sql = "SELECT MAX(Groups.id)+1 FROM `Groups`";
    return $db->SQLGetChamp($sql);
}

function confirmToUser($confirmToken) {
    $db = Config::getDatabase();
    $sql = "SELECT Users.id FROM Users WHERE Users.confirmToken = ?";
    return $db->SQLGetChamp($sql,[$confirmToken]);
}

function selectConfirmToken($uid) {
    $db = Config::getDatabase();
    $sql = "SELECT Users.confirmToken FROM Users WHERE Users.id = ?";
    return $db->SQLGetChamp($sql, [$uid]);
}

function activateUser($uid) {
    $db = Config::getDatabase();
    $sql = "UPDATE Users SET activation=1, confirmToken=null WHERE id= ?";
    return $db->SQLUpdate($sql, [$uid]);
}

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

/**
 * Vérifie si l'utilisateur est dans le groupe
 * @param int $idUser id de l'utilisateur
 * @param int $idGroup l'id du groupe
 * @return int|false L'id de l'utilisateur ou false
 */
function isInGroup($idUser, $idGroup) {
    $db = Config::getDatabase();
    $sql = "SELECT User_Groups.uid FROM User_Groups 
            WHERE User_Groups.gid = ? AND User_Groups.uid = ?";
    
    return $db->SQLGetChamp($sql, [$idGroup, $idUser]);
}
/**
 * Vérifie si un utilisateur a les permission pour accèder à un groupe
 * @param int $idUser id de l'utilisateur
 * @param int $idGroup l'id du groupe
 * @return array retourne un tableau vide si il n'y pas les permissions
 */
function haveGroupPermission($idUser, $idGroup) {
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Roles 
            JOIN Groups_Perms
                ON Groups_Perms.rid = Roles.id
            JOIN User_Groups
                ON User_Groups.gid = Groups_Perms.gid
            JOIN Users
                ON Users.id = User_Groups.uid
            WHERE Users.id = ? AND User_Groups.gid = ?";

    return Database::parcoursRs($db->SQLSelect($sql, [$idUser, $idGroup]));
}

function selectGroupMessage($mid, $gid) {
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Group_Messages WHERE id = ? AND gid = ?";

    return Database::parcoursRs($db->SQLSelect($sql, [$mid, $gid]));
}

function haveInstrument($idUser, $idInstrument) {
    $db = Config::getDatabase();
    $sql = "SELECT * FROM User_Instruments WHERE uid= ? AND iid = ?;";
    $params = [$idUser, $idInstrument];
    return Database::parcoursRs($db->SQLSelect($sql, $params));
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
    $sql= "SELECT id, tel, activation, password FROM Users WHERE tel = ?;";
    
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
    // TODO : serait-il plus sage de le faire autre part.
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", U.id), CONCAT(\"/\", U.photo)) AS photo";
    $sql = "SELECT U.id, U.firstName, U.lastName, U.sex, U.age, U.studies, $photo, U.activation FROM Users AS U";
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
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", U.id), CONCAT(\"/\", U.photo)) AS photo";
    $publicInfo = "U.id, U.firstName, U.lastName, U.sex, U.age, U.studies, $photo, U.activation ";
    $privateInfo = ", U.tel, U.mail ";
    $sql = "SELECT " . $publicInfo;

    if($me) $sql .= $privateInfo;

    $sql .= "FROM Users AS U WHERE U.id = ?;";
    $params = [$idUser];
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function updateUser($idUser,$mail = false,$tel = false,$age = false,$studies = false,$image = false,$password=false){
    $db = Config::getDatabase();
    $params = [];

    $sqlMail = $mail ? "?": "mail";
    $sqlTel = $tel ? "?" : "tel";
    $sqlAge = $age ? "?" : "age";
    $sqlStudies = $studies ? "?" : "studies";
    $sqlPassword = $password ? "?" : "password";
    $sqlImage = $image ? "?": "photo";

    if ($mail != false) array_push($params, $mail);
    if ($tel != false) array_push($params, $tel);
    if ($age != false) array_push($params, $age);
    if ($studies != false) array_push($params, $studies);
    if ($password != false) array_push($params, $password);
    if ($image != false) array_push($params, $image);
    array_push($params,$idUser);

    if(count($params) == 1) return false;
    $sql = "UPDATE Users SET mail=$sqlMail, tel=$sqlTel, age=$sqlAge, studies=$sqlStudies, password=$sqlPassword, photo=$sqlImage
            WHERE id = ?";
            
    return $db->SQLUpdate($sql, $params);
}

function insertUser($firstname,$lastname,$mail,$tel,$password,$age,$confirmToken,$studies = "" , $sex = 0, $image = "default.png"){
    $db = Config::getDatabase();
    $params = [$firstname,$lastname,$mail,$tel,$password,$age, $studies, $sex, $image, $confirmToken];
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



function selectRoles($active = "both"){
    $db = Config::getDatabase();
    $whereStm = " WHERE active = ?;";
    $sql = "SELECT * FROM Roles";
    $params = [];

    if($active == "0" || $active === "1") {
        $params = [$active];
        $sql .= $whereStm;
    }
    else {
        $sql .= ";";
    }
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectRole($rid) {
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Roles WHERE id = ?";

    return Database::parcoursRs($db->SQLSelect($sql, [$rid]));
}

function selectUserRoles($uid){
    $db = Config::getDatabase();
    $sql = "SELECT Roles.*
            FROM Roles
            JOIN User_Roles
                ON User_Roles.rid = Roles.id
            WHERE User_Roles.uid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql,[$uid])));
}

function updateRole($rid,$label = null,$active = null){
    $db = Config::getDatabase();

    $sqlLabel = $label !== null ? "?" : "label"; 
    $sqlActive = $active !== null ? "?" : "active";
    $params = [];
    $sql = "UPDATE Roles SET label=$sqlLabel, active=$sqlActive ";
    if ($sqlLabel == "?"){
        array_push($params, $label);
    }
    if ($sqlActive == "?"){
        array_push($params, $active);
    }

    array_push($params, $rid);
    $sql .= " WHERE id = ?";
    return $db->SQLUpdate($sql, $params);
}

function insertRole($label,$active=1){
    $db = Config::getDatabase();
    $params = [$label,$active];
    $sql = "INSERT INTO Roles(label,active) VALUES (?,?) ";
    return $db->SQLInsert($sql, $params);

}


function selectInstruments($idInstrument = null){
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Instruments ";
    $params = [];
    $whereStm = " WHERE id = ?";

    if($idInstrument) {
        $sql .= $whereStm;
        array_push($params, $idInstrument);
    }

    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function updateInstruments($iid,$label = null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE Instruments SET ";
    if ($label != null){
        $sql = $sql . "label = ?";
        array_push($params, $label);
    }
    array_push($params, $iid);
    $sql .= " WHERE id = ?";

    return $db->SQLUpdate($sql, $params);
}

function insertInstruments($label){
    $db = Config::getDatabase();
    $params = [$label];
    $sql = "INSERT INTO Instruments(label) VALUES (?) ";
    return $db->SQLInsert($sql, $params);

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
    return $db->SQLInsert($sql, $params);

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
    $image = "CONCAT(CONCAT(\"" . getBaseLink() . "/groups/\"" . ", Groups.id), CONCAT(\"/\", Groups.image)) AS image";
    $params = [$uid];
    $sql = "SELECT DISTINCT Groups.id, Groups.titre, $image
            FROM User_Groups
            JOIN Groups ON Groups.id = User_Groups.gid 
            WHERE uid = ?;";
            
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
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$gid];
    $sql = "SELECT Group_Messages.*, Users.firstName, Users.lastName, $photo FROM `Group_Messages` JOIN Users ON Users.id = Group_Messages.uid WHERE Group_Messages.gid = ?;";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}


function selectGroupReaction($mid,$uid = null, $emoji = null){
    $db = Config::getDatabase();
    $params = [$mid];
    $sqlUid = "";
    $sqlEmoji = "";

    if($uid) {
        $sqlUid = " AND uid = ?";
        array_push($params, $uid);
    }
    if($emoji) {
        $sqlEmoji = " AND emoji = ?";
        array_push($params, $emoji);
    }
    $sql = "SELECT * FROM Group_Message_Reactions WHERE mid = ? $sqlUid $sqlEmoji";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectGroupReactions($gid) {
    $db = Config::getDatabase();
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$gid];
    $sql = "SELECT Group_Message_Reactions.emoji, Group_Message_Reactions.mid, Users.id AS uid, Users.firstName, Users.lastName, $photo
            FROM Group_Messages
            JOIN Group_Message_Reactions
                ON Group_Message_Reactions.mid = Group_Messages.id
            JOIN Users
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

    return $gid;
}

function selectGroup($gid) {
    $image = "CONCAT(CONCAT(\"" . getBaseLink() . "/groups/\"" . ", Groups.id), CONCAT(\"/\", Groups.image)) AS image";
    $sql = "SELECT Groups.id, Groups.titre, $image FROM Groups Where id = ?";
    $db = Config::getDatabase();
    return Database::parcoursRs($db->SQLSelect($sql, [$gid]));
}

function insertIntoGroup($uid,$gid){
    $db = Config::getDatabase();
    $params = [$uid,$gid];
    $sql = "INSERT INTO User_Groups(uid,gid) VALUES (?,?) ";
    return $db->SQLInsert($sql, $params);
}


function insertGroupMessage($uid,$gid,$content,$answerTo = null){
    $db = Config::getDatabase();
    $params = [$uid,$gid,$content,$answerTo];
    $sql = "INSERT INTO Group_Messages(uid,gid,content,answerTo,pinned) VALUES (?,?,?,?,0) ";
    return $db->SQLInsert($sql, $params);
}


function selectPosts($notAMember, $uid){
    $db = Config::getDatabase();
    $params = [$uid];
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $banner = "CONCAT(CONCAT(\"" . getBaseLink() . "/posts/\"" . ", Posts.id), CONCAT(\"/\", Posts.banner)) AS banner";
    $sql = "SELECT Posts.id, Posts.content, $banner, Posts.pinned, Posts.visible, Users.id AS uid, Users.firstName, Users.lastName, $photo,IFNULL(Post_Likes.liked,0) AS liked
            FROM Posts
            JOIN Users ON Users.id = Posts.author
            LEFT JOIN Post_Likes ON Post_Likes.pid = Posts.id AND Post_Likes.uid = ?";
    $whereStm = " WHERE Posts.visible = 1";

    if($notAMember) $sql .= $whereStm;

    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectPost($pid){
    $db = Config::getDatabase();
    $sql = "SELECT * FROM Posts WHERE id = ?";

    return Database::parcoursRs(($db->SQLSelect($sql, [$pid])));
}

function selectPostReactions($pid = null){
    $db = Config::getDatabase();
    $sqlPid = "";
    $whereStm = "";
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$pid];
    if($pid == null) {
        $params =[];
        $sqlPid = "Post_Reactions.pid,";
    }
    else $whereStm = " WHERE Post_Reactions.pid = ?";

    $sql = "SELECT Post_Reactions.emoji, $sqlPid Users.id AS uid, Users.firstName, Users.lastName, $photo
            FROM Post_Reactions
            JOIN Users ON Post_Reactions.uid = Users.id $whereStm";

    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectPostMessages($pid){
    $db = Config::getDatabase();
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$pid];
    $sql = "SELECT Post_Comments.*, Users.firstName, Users.lastName, $photo FROM `Post_Comments` JOIN Users ON Users.id = Post_Comments.uid WHERE Post_Comments.pid = ?;";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

// TODO : eventuellement modif pour rendre l'uid optionnelle
function selectCalendar($uid, $aid = null, $type = "intra"){
    $db = Config::getDatabase();
    $params = [];
    $sqlAid = "";

    if($aid)  {
        $sqlAid = " AND Agendas.id = ?";
        array_push($params,$aid);
    }
    if($type == "extra") {
        $sql = "SELECT Agendas.* FROM Agendas 
                WHERE Agendas.extra = 1 $sqlAid;"; 
    }
    else if($type == "intra") {
        array_unshift($params, $uid);
        $sql = "SELECT Agendas.* FROM Agendas 
                JOIN Agenda_Perms ON Agendas.id = Agenda_Perms.aid
                JOIN User_Roles ON User_Roles.rid = Agenda_Perms.rid
                WHERE Agenda_Perms.read = 1 AND User_Roles.uid = ? AND Agendas.extra = 0 $sqlAid;";
    }
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectEvents($uid, $type){
    //TODO : params pour afficher seulement les events en cours
    $db = Config::getDatabase();
    $type = $type == "extra" ? 1 : 0;
    $params = [$type];
    $joinStmt = "";
    $readPerms = "";
    if($type == "intra") {
        $joinStmt = "JOIN Agenda_Perms ON Agenda_Events.aid = Agenda_Perms.aid
                     JOIN User_Roles ON User_Roles.rid = Agenda_Perms.rid";
        $readPerms = "AND Agenda_Perms.read = 1 AND User_Roles.uid = ?;";
        array_push($params,$uid);
    }
    $sql = "SELECT DISTINCT Agenda_Events.*
            FROM Agenda_Events
            JOIN Agendas ON Agendas.id = Agenda_Events.aid $joinStmt
            WHERE Agendas.extra = ? $readPerms";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectCallMembers($aeid, $uid = null){
    $db = Config::getDatabase();
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$aeid];
    $sqlUid = "";
    if($uid) {
        $sqlUid = " AND Agenda_Event_Calls.uid = ?";
        array_push($params,$uid);
    }
    $sql = "SELECT Agenda_Event_Calls.*, Users.id, Users.firstName, Users.lastName, $photo
            FROM Agenda_Event_Calls
            JOIN Users ON Users.id = Agenda_Event_Calls.uid
            WHERE Agenda_Event_Calls.aeid = ? $sqlUid;";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}

function selectParticipations($aeid, $uid = null) {
    $db = Config::getDatabase();
    $photo = "CONCAT(CONCAT(\"" . getBaseLink() . "/users/\"" . ", Users.id), CONCAT(\"/\", Users.photo)) AS photo";
    $params = [$aeid];
    $sqlUid = "";
    if($uid) {
        $sqlUid = " AND User_Participations.uid = ?";
        array_push($params, $uid);
    }
    $sql = "SELECT User_Participations.participation, Users.id as uid, Users.firstName, Users.lastName, $photo
            FROM User_Participations
            JOIN Users ON Users.id = User_Participations.uid
            WHERE User_Participations.aeid = ? $sqlUid;";

    return Database::parcoursRs($db->SQLSelect($sql, $params));
}


function selectUserInstruments($uid) {
    $db = Config::getDatabase();
    $sql = "SELECT Instruments.*
            FROM Instruments
            JOIN User_Instruments ON User_Instruments.iid = Instruments.id
            WHERE User_Instruments.uid = ?;";
    return Database::parcoursRs($db->SQLSelect($sql, [$uid]));

}

function insertParticipation($uid, $aeid, $participation) {
    $db = Config::getDatabase();
    $sql = "INSERT INTO User_Participations(uid, aeid, participation) VALUES (?,?,?);";
    $db->SQLInsert($sql, [$uid, $aeid, $participation]);
}

function selectLiked($pid, $uid){
    $db = Config::getDatabase();
    $sql = "SELECT Liked, pid, uid
            FROM Post_Likes
            WHERE pid = ? AND uid = ?";
    $param = [$pid, $uid];

    return Database::parcoursRs($db->SQLSelect($sql, $param));
}
function liked($pid, $uid, $liked){
    $db = Config::getDatabase();
    $sql = "UPDATE Post_Likes
            SET liked = ?
            WHERE pid = ? AND uid = ?";
    $param = [$liked, $pid, $uid];

    return $db->SQLUpdate($sql, $param);
}

function insertPost($content, $image, $pinned, $visible, $author) {
    $db = Config::getDatabase();
    $param = [$author, $content, $image, $pinned, $visible];
    $sql = "INSERT INTO Posts(author, content, banner, pinned, visible) VALUES (?,?,?,?,?);";
    return $db->SQLInsert($sql, $param);
}

function insertLiked($uid, $pid, $liked) {
    $db = Config::getDatabase();
    $param = [$uid, $pid, $liked];
    $sql = "INSERT INTO Post_Likes(uid,pid,liked) VALUES (?,?,?);";
    return $db->SQLInsert($sql, $param);
}

function selectPinned($id, $uid, $gid) {
    $db = Config::getDatabase();
    $param = [$id, $uid, $gid];
    $sql = "SELECT pinned, pid, uid, id
            FROM Group_Messages
            WHERE id= ? AND uid = ? AND pid = ?";
    return Database::parcoursRs($db->SQLSelect($sql, $param));
}

function pinned($id, $uid, $gid, $pinned) {
    $db = Config::getDatabase();
    $param = [$pinned,$id, $uid, $gid];
    $sql = "UPDATE Group_Messages
            SET pinned  = ? 
            WHERE id = ? AND uid = ? AND gid = ?";
    return $db->SQLUpdate($sql, $param);
}

function insertPinned($id, $uid, $gid, $pinned) {
    $db = Config::getDatabase();
    $param = [$pinned,$id, $uid, $gid];
    $sql = "INSERT INTO Group_Messages(id, uid, gid, pinned) VALUES (?, ?, ?, ?)";
    return $db->SQLInsert($sql, $param);
}

function insertGroupMessageReaction($mid, $uid, $reaction) {
    $db = Config::getDatabase();
    $params = [$mid, $uid, $reaction];
    $sql = "INSERT INTO Group_Message_Reactions(mid, uid, emoji) VALUES (?,?,?);";
    return $db->SQLInsert($sql, $params);
}

function deleteGroupMessageReaction($mid, $uid, $emoji) {
    $db = Config::getDatabase();
    $params = [$mid, $uid, $emoji];
    $sql = "DELETE FROM Group_Message_Reactions WHERE mid = ? AND uid = ? AND emoji = ?;";

    return $db->SQLDelete($sql, $params);
}

function insertAgenda($title, $extra, $uid) {
    //TODO : ajouter l'uid
    $db = Config::getDatabase();
    $params = [$title, $extra];
    $sql = "INSERT INTO Agendas(title, extra) VALUES (?,?);";

    return $db->SQLInsert($sql, $params);
}

function selectEvent($eid, $type, $uid = null) {
    //TODO : refacto
    $db = Config::getDatabase();
    $params = [$eid];
    $sqlUid = "";
    if($type == "extra") {
        $sql = "SELECT Agenda_Events.*
        FROM Agenda_Events JOIN Agendas ON Agendas.id = Agenda_Events.aid
        WHERE Agenda_Events.id = ? AND Agendas.extra = 1 
            AND DATE(Agenda_Events.endDate) >= DATE(CURRENT_DATE);";
        
    }
    else {
        if($uid) {
            array_push($params, $uid);
            $sqlUid = " AND User_Roles.uid = ?";
        }
        $sql = "SELECT DISTINCT Agenda_Events.*, MAX(Agenda_perms.read) AS `read`, MAX(Agenda_perms.write) AS `write`
                FROM Agenda_Events JOIN Agendas ON Agendas.id = Agenda_Events.aid
                JOIN Agenda_Perms ON Agenda_Perms.aid = Agendas.id JOIN User_Roles ON User_Roles.rid = Agenda_Perms.rid
                WHERE Agenda_Events.id = ? AND Agendas.extra = 0 
                AND DATE(Agenda_Events.endDate) >= DATE(CURRENT_DATE) $sqlUid;";
    }
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function insertCall($uid, $eid, $present, $reason_desc) {
    $db = Config::getDatabase();
    $params = [$uid, $eid, $present, $reason_desc];
    $sql = "INSERT INTO Agenda_Event_Calls(uid, aeid, present, reason_desc) VALUE (?,?,?,?)";
    $db->SQLInsert($sql, $params);
}
?>
