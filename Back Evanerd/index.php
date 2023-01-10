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
    case "POST /auth":
        authUser($data, $queryString);
        break;
    case "GET /users":
        listUsers($data, $queryString);
        break;

    case "GET /users/ID":
        sendUser($data, $idTabs, $authKey);
        break;
    case "POST /users":
        postUser($data, $queryString);
        break;

    case "GET /groups":
        listGroups($data, $queryString, $authKey);
        break;

    default:
        notAction($data);
        break;

}
?>