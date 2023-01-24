importScripts("http://localhost/EvaNerd/Front%20Evanerd/biblio_composants.js");

var api = "http://localhost/EvaNerd/Back%20Evanerd/api";

function GETUser(){
    $.ajax({
        type: "GET",
        url: api + "/users",
        // headers: {"debug-data":true}, // données dans les entetes 
        headers: {},

        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            return oRep;

        },
        dataType: "json"
    });
}

/**  * @param $id Id de l'utilisateur */

function GETUserbyID($id){
    $.ajax({
        type: "GET",
        url: api + "/users/" + $id,
        headers: {"authToken":""}, // données dans les entetes 
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            return oRep;
        },
        dataType: "json"
    });
}

/**
 * 
 * @param $id Id de l'utilisateur
 * @param $informations Un JSON contenant les différentes données nécessaires à la création d'un compte
 * 
 * Exemple de JSON : $Infos{[firstName]:Prénom,[lastName]:Nom,[age]:20,[sex]:1,[mail]:monMail@gmail.com,[tel]:0601020304,[studies]:Ingénieur,[password]:mpLabX}
 */

function POSTUser($informations){
    $.ajax({
        type: "POST",
        url: api + "/users",
        headers: {"authToken":""}, // données dans les entetes 
        data: [
            {
                "key": "firstName",
                "value": $informations["firstName"]
            },
            {
                "key": "lastName",
                "value": $informations["lastName"]
            },
            {
                "key": "age",
                "value": $informations["age"]
            },
            {
                "key": "sex",
                "value": $informations["sex"]
            },
            {
                "key": "mail",
                "value": $informations["mail"]
            },
            {
                "key": "tel",
                "value": $informations["tel"]
            },
            {
                "key": "studies",
                "value": $informations["studies"]
            },
            {
                "key": "password",
                "value": $informations["password"]
            }
        ],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            return oRep;

        },
        dataType: "json"
    });
    
}






