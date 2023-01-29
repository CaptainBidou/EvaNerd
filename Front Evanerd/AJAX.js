var api = "http://localhost/EvaNerd/Back%20Evanerd/api";


function auth($tel,$password){
    oRep = $.ajax({
        type: "GET",
        url: api + "/auth/",
        // headers: {"debug-data":true}, // données dans les entetes 
        headers: {},
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            return oRep;

        },
        dataType: "json"
    });
    return oRep;
}





function GETUsers(){
    oRep = $.ajax({
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
    return oRep;
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

function PUTUser($informations,$uid){
    $data = [];
    if ($informations["firstName"])
        $data["firstname"] = $informations["firstName"];
    if ($informations["lastName"])
        $data["lastName"] = $informations["lastName"];
    if ($informations["age"])
        $data["age"] = $informations["age"];
    if ($informations["sex"])
        $data["sex"] = $informations["sex"];
    if ($informations["mail"])
        $data["mail"] = $informations["mail"];
    if ($informations["tel"])
        $data["tel"] = $informations["tel"];
    if ($informations["studies"])
        $data["studies"] = $informations["studies"];

    $.ajax({
        type: "PUT",
        url: api + "/users/"+$uid,
        headers: {"authToken":""}, // données dans les entetes 
        data: $data,
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



function ListerPosts(){
    $.ajax({
        type: "GET",
        url: api + "/posts",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            oRep["posts"].forEach(element => {
                JcreerPost(element);
            });

        },
        dataType: "json"
    });   
}

/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListerConv(){
    $.ajax({
        type: "GET",
        url: api + " /groups",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            oRep["groups"].forEach(element => {
                JCreerConv(element);
            });
        },
        dataType: "json"
    });   
}

/**
 * Récupérer la liste des permissions d'un groupe
 * @param {*} $gid Identifiant du groupe
 */
function ListerPermsConv($gid){
    $.ajax({
        type: "GET",
        url: api + "/groups/" + $gid + "/permissions",
        headers:{"authToken":""},
        data : [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success : function(oRep){
            console.log(oRep);
            return oRep;
        },
        dataType: "json"
    });
}

/**
 * Récupérer la liste des messages d'un groupe
 * @param {*} $gid Identifiant du groupe
 */
function ListerMsgConv($gid){
    $.ajax({
        type : "GET",
        url : api + "/groups/" + $gid + "/messages",
        headers:{"authToken" : ""},
        data : [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success : function(oRep){
            console.log(oRep);
            oRep["groups"].forEach(element => {
                JAfficherMessageConv(oRep);
            });
        },
        dataType: "json"
    });
}

/**
 * Récupérer la liste des réactions d'un message d'un groupe
 * @param {*} $gid Identifiant d'un groupe
 * @param {*} $mid Identifiant d'un message
 */
function ListerReactMsgConv($gid, $mid){
    $.ajax({
        type :"GET",
        url : api + "/groups/" + $gid + "/messages/" + $mid,
        headers:{"authToken" : ""},
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        sucess : function(oRep){
            console.log(oRep);
            // Fonctions JReactMsgConv
        },
        dataType : "json"
    });
}

/**
 * Créer un groupe
 * @param {*} $informations Donnée de l'image & Titre de la discu
 */
function CreerConv($informations){
    if($informations["image"])
        $data["image"] = $informations["image"];
    if($informations["titre"])
        $data["titre"] = $informations["titre"];

    $.ajax({
        type:"POST",
        url: api + "/groups",
        headers:{"authToken" : ""},
        data: $data,
        error: function(){
            console.log("Une erreur s'est produite");
        },
        success : function(oRep){
            console.log(oRep);
            //Fonction JCreerConv
        },
        dataType: "json"
    });
}

/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListerAppel($aid,$eid){
    $.ajax({
        type: "GET",
        url: api + "/agendas/"+$aid+"/event/"+$eid+"/calls",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            oRep["calls"].forEach(element => {
                JCreerAppel(element);
            });
        },
        dataType: "json"
    }); 
}
  








