<?php 
include_once "controller/controller.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

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
    
    case "GET /users/ID/instruments":
        getUserInstruments($data, $idTabs);
        break;

    case "GET /users/ID/roles":
        getUserRoles($data, $idTabs);
        break;
    
    case "PUT /users/ID":
        putUser($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users":
        postUser($data, $queryString);
        break;

    case "POST /users/instruments":
        postUserInstrument($data, $authKey, $queryString);
        break;

    case "POST /users/achievement":
        postUserAchievement($data, $authKey, $queryString);
        break;

    case "POST /users/ID/roles":
        postUserRole($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /users/verify":
        verifMail($data, $queryString);
        break;
    
    case "POST /users/ID/image":
        postImage($data, $idTabs, $authKey);
        break;

    case "DEL /users/instruments":
        delUserInstrument($data, $authKey, $queryString);
        break;
    
    case "DEL /users/achievements":
        delUserAchievement($data, $authKey, $queryString);
        break;
    
    case "DEL /users/roles":
        delUserRole($data, $authKey, $queryString);
        break;

    /* Roles */
    case "GET /roles":
        listRoles($data, $queryString);
        break;
    
    case "PUT /roles/ID":
        putRoles($data, $idTabs, $authKey, $queryString);
        break;
    
    case "POST /roles":
        postRole($data, $authKey, $queryString);
        break;
    
    /* Instruments */
    case "GET /instruments":
        listInstruments($data);
        break;

    case "PUT /instruments/ID":
        putInstruments($data, $idTabs, $authKey, $queryString);
        break;

    case "POST /instruments":
        postInstruments($data, $authKey, $queryString);
        break;

    /* Achievements */
    case "GET /achievements":
        listAchievements($data);
        break;
    
    case "PUT /achievements/ID":
        putAchievements($data, $idTabs, $authKey, $queryString);
        break;
    
    /* Groups */
    case "GET /groups":
        listGroups($data, $queryString, $authKey);
        break;

    case "GET /groups/ID/permissions":
        listGroupsPerms($data, $idTabs, $authKey);
        break;

    case "GET /groups/ID/messages":
        listGroupMessages($data, $idTabs, $authKey);
        break;
    
    case "GET /groups/ID/messages/ID":
        listGroupsReacts($data, $idTabs, $authKey);
        break;

    case "POST /groups":
        createUserGroups($data, $authKey, $queryString);
        break;
    
    case "POST /groups/ID/users/ID":
        addUsersGroups($data, $idTabs,$authKey);
        break;
    
    case "POST /groups/ID/messages":
        postMessagesGroups($data, $idTabs, $authKey, $queryString);
        break;
    
    case "POST /groups/ID/messages/ID/reactions":
        postMessageReactions($data, $idTabs, $authKey, $queryString);
        break;
    /* Posts */
    case "GET /posts":
        listPosts($data, $authKey);
        break;
        
    case "GET /posts/ID/messages":
        listPostsMessages($data, $idTabs, $authKey);
        break;
    
    case "POST /posts":
        postPost($data, $authKey,$queryString);
        break;
    
    case "POST /posts/ID/likes":
        postPostLike($data, $idTabs, $authKey);
        break;
    
    /* Agendas */

    case "GET /agendas":
        listAgendas($data, $queryString, $authKey);
        break;
    
    case "GET /agendas/ID/events":
        listAgendaEvents($data, $idTabs, $authKey);
        break;

    case "GET /agendas/ID/events/ID/calls":
        listAgendaEventCalls($data, $idTabs, $authKey);
        break;
    
    case "POST /agendas":
        postAgenda($data, $queryString, $authKey);
        break;

    case "POST /agendas/ID/events":
        postAgendasEvent($data, $idTabs, $queryString, $authKey);
        break;

    /* Participation */

    case "GET agendas/ID/events/ID/participations":
        listParticipations($data, $idTabs, $authKey);
        break;

    case "POST /agendas/ID/events/ID/participations":
        postParticipations($data, $idTabs, $queryString, $authKey);
        break;
    
    default:
        notAction($data);
        break;

}
?>