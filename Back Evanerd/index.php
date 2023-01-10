<?php 
include_once "controller/controller.php";

$data = genereAPIInfo();

$authKey = getAuthHeader();
$queryString = getRequestParams();
$requestType = getRequestType();
$idTabs = array();

if(isset($queryString["request"])) $request = $queryString["request"]; // rendre plus robuste
else $request = false;


$action = getAction($request, $requestType, $idTabs);

switch($action) {

    /* Authentification */
    case "POST /auth":
        authUser($data, $queryString);
        break;

    /* Users */
    case "GET /users":
        listUsers($data, $queryString);
        break;

    case "GET /users/ID":
        sendUser($data, $idTabs, $authKey);
        break;
    
    case "PUT /users/ID":
        putUser($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users":
        postUser($data, $queryString);
        break;

    case "POST /users/instruments":
        postUserInstrument($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users/achievement":
        postUserAchievement($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users/ID/roles":
        postRole($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users/verify":
        verifMail($data, $idTabs, $authKey);
        break;

    case "DEL /users/instruments":
        delUserInstrument($data, $idTabs, $authKey, $queryString);
        break;
    
    case "DEL /users/achievements":
        delUserAchievement($data, $idTabs, $authKey, $queryString);
        break;
    
    case "DEL /users/roles":
        delUserRole($data, $idTabs, $authKey, $queryString);
        break;

    /* Roles */
    case "GET /roles":
        listRoles($data, $queryString);
        break;
    
    case "PUT /roles/ID":
        putRoles($data, $idTabs, $authKey, $queryString);
        break;
    
    case "POST /roles":
        postRoles($data, $authKey, $queryString);
        break;
    
    /* Instruments */
    case "GET /instruments":
        listInstruments($data, $queryString);
        break;

    case "PUT /instruments/ID":
        putInstruments($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /instruments":
        postInstruments($data, $authKey, $queryString);
        break;

    /* Achievements */
    case "GET /achievements":
        listAchievements($data, $queryString);
        break;
    
    case "PUT /achievements/ID":
        putAchievements($data, $idTabs, $authKey, $queryString);
        break;
    
    case "POST /achievements":
        postAchievements($data, $authKey, $queryString);

    /* Groups */
    case "GET /groups":
        listGroups($data, $queryString, $authKey);
        break;

    case "GET /groups/ID/permissions":
        listGroupsPerms($data, $idTabs, $authKey);
        break;

    case "GET /groups/ID/messages":
        listGroupsMessages($data, $idTabs, $authKey);
        break;
    
    case "GET /groups/ID/messages/ID":
        listGroupsReacts($data, $idTabs, $authKey);
        break;

    case "POST /groups":
        createUserGroups($data, $authKey, $queryString);
        break;
    
    case "POST /groups/ID/users/ID":
        addUsersGroups($data, $authKey);
        break;
    
    case "POST /groups/ID/messages":
        postMessagesGroups($data, $idTabs, $authKey, $queryString);
        break;

    /* Posts */
    case "GET /posts":
        listPosts($data, $authKey);
        break;

    case "GET /posts/ID/reactions":
        listPostsReacts($data, $idTabs, $authKey);
        break;
    
    case "GET /posts/ID/messages":
        listPostsMessages($data, $idTabs, $authKey);
        break;
    
    /* Agendas */

    case "GET /agendas":
        listAgendas($data, $authKey);
        break;
    
    case "GET /agendas/ID/event":
        listAgendaEvents($data, $idTabs, $authKey);
        break;

    case "GET /agendas/ID/event/ID/calls":
        listAgendaEventCalls($data, $idTabs, $authKey);
        break;

    default:
        notAction($data);
        break;

}
?>
