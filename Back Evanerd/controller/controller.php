<?php
include_once "baseController.php";

function listUsers($data, $queryString) {
    $users = array();
    $momo = array("id" => 1,
        "firstname" => "Maurice",
        "lastName" => "Monticule",
        "mail" => "momo@gmail.com",
        "sex" => 0,
        "age" => 19,
        "studies" => "Centrale Lille IG2I",
        "photo" => "www.example/users/1.png");

    $jp = array("id" => 2,
        "firstname" => "Jean Pierre",
        "lastName" => "Barrebasse",
        "mail" => "jpBasse@gmail.com",
        "sex" => 2,
        "age" => 34,
        "studies" => "Ecole du rire",
        "photo" => "www.example/users/2.png");
    // Fixture en attendant le model.php
    if(isset($queryString["idRole"])) {
        $users = array();
        $users[] = $momo;
        $data["users"] = $users;
        echo json_last_error();
        sendResponse($data, [getStatusHeader(200)]);
        return;
    }
    else {
        $users = array();
        $users[] = $momo;
        $users[] = $jp;
        $data["users"] = $users;
        sendResponse($data, [getStatusHeader(200)]);
        return;
    }
}


function notAction($data) {
    sendResponse($data, [getStatusHeader(200)]);
}
