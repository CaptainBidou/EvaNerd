<?php

include_once("includes/Config.php");

// Requete sur les users
function getUsers($idRole=null){
    $db = Config::getDatabase();
    if ($idRole != null) {
        $sql = "SELECT * 
        FROM Users AS U JOIN User_Roles AS UR ON U.id = UR.uid
        WHERE UR.rid = ? ";
        $params = [$idRole];
        return Database::ParcoursRs($db->SQLSelect($sql, $params));
    } else {
        $sql = "SELECT * FROM Users";
        return Database::ParcoursRs($db->SQLSelect($sql));
    }
}

function getUser($idUser){
    $db = Config::getDatabase();
    $sql = "SELECT * 
    FROM Users AS U 
    WHERE U.Id = ? ";
    $params = [$idUser];
    return Database::parcoursRs($db->SQLSelect($sql, $params));
}

function modifUser($idUser,$mail = null,$tel = null,$age = null,$studies = null,$password=null){
    $db = Config::getDatabase();
    $params = [];
    $sql = "UPDATE table SET";
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

function createUser($firstname,$lastname,$mail,$tel,$password,$age,$studies = null , $sexe = null, $image = "default.png"){
    $db = Config::getDatabase();
    $params = [$firstname,$lastname,$mail,$tel,$password,$age];
    $sql = "INSERT INTO Users (firstname,lastname,mail,tel,age";
    if ($studies != null){
        $sql = $sql . ",studies";
        array_push($params, $studies);
    }
    if ($studies != null){
        $sql = $sql . ",sexe";
        array_push($params, $sexe);
    }

    $sql = $sql . ",images";
    array_push($params, $studies);

    $sql = $sql . "VALUES(";

    foreach ($params as $Val)
        $sql = $sql .",?";
    
    $sql = $sql . ")";
    
    return Database::parcoursRs($db->SQLInsert($sql, $params));
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

function getMessages($gid){
    $db = Config::getDatabase();
    $params = [$gid];
    $sql = "SELECT * INTO Group_Messages WHERE gid = ?";
    return Database::parcoursRs(($db->SQLSelect($sql, $params)));
}



?>
