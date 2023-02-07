
var api = "http://localhost/EvaNerd/Back%20Evanerd/api";
var authcode = "";
var member = 0;
var pdp = "";
var user = "";


/* AUTHENTIFICATION AJAX FUNC */

/**
 * Requête permettant de s'identifier sur le site
 * @param $tel Numéro de téléphone
 * @param $password Mot de passe
 */

function auth($tel,$password){
    $.ajax({
        type: "POST",
        url: api + "/auth?"+ "tel="+$tel+"&password="+$password,
        // headers: {"debug-data":true}, // données dans les entetes 
        headers: {},
        data: [  {
            "key": "tel",
            "value": $tel
        },
        {
            "key": "password",
            "value": $password
        },],
        error : function(){
            console.log("Une erreur s'est produite");
            
        },
        success: function(oRep){
            console.log(oRep); 
            user = oRep["user"].id;
            authcode = oRep.authToken;
            member = !oRep["user"].noMember;
            pdp = oRep["user"].photo;
        },
        dataType: "json"
    }).done(function launchAPP(){
        $('#header').empty();
        JcreerHeader({"photo" : pdp});
        AfficherAccueil();
        JcreerFooter({"membre" : member});
    });
    return 1;
}
/***** AUTHENTIFICATION END **********/

/* USERS AJAX FUNC */

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
 * Requête permettant de modifier les informations d'un utilisateur
 * @param $id Id de l'utilisateur
 * @param $informations Un JSON contenant les différentes données nécessaires à la création d'un compte
 * 
 * Exemple de JSON : $Infos{[firstName]:Prénom,[lastName]:Nom,[age]:20,[sex]:1,[mail]:monMail@gmail.com,[tel]:0601020304,[studies]:Ingénieur,[password]:mpLabX}
 */

function ModifyUser($informations,$uid){
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
        async: false,
        error : function(){
            console.log("Une erreur s'est produite : ERROR 403");
        },
        success: function(oRep){
            console.log(oRep); 
            return oRep;

        },
        dataType: "json"
    });
    
}

/**
 * Requête permettant de créer un nouvel utilisateur
 * @param $id Id de l'utilisateur
 * @param $informations Un JSON contenant les différentes données nécessaires à la création d'un compte
 * 
 * Exemple de JSON : $Infos{[firstName]:Prénom,[lastName]:Nom,[age]:20,[sex]:1,[mail]:monMail@gmail.com,[tel]:0601020304,[studies]:Ingénieur,[password]:mpLabX}
 */

function CreateUser($informations){
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

/**
 * Requête permettant d'ajouter un rôle à un utilisateur
 * @param {*} $uid Identifiant de l'utilisateur
 * @param {*} $rid Identifiant du rôle
 */
function AddUserRole($uid,$rid){
    $.ajax({
        type: "POST",
        url: api + "/users/"+ $uid + "/achievements",
        headers: {"authToken":""}, // données dans les entetes 
        data: [     
            {
                "key": "rid",
                "value": $rid
            }],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant d'ajouter un instrument à un utilisateur
 * @param {*} $iid Identifiant d'instrument
 */
function AddserInstruments($iid){
    $.ajax({
        type: "POST",
        url: api + "/users/instruments",
        headers: {"authToken":""}, // données dans les entetes 
        data: [     
            {
            "key": "iid",
            "value": $iid
            }],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant d'ajouter un achievement à un utilisateur
 * @param {*} $aid Identifiant d'achievement
 */
function AddserAchievement($aid){
    $.ajax({
        type: "POST",
        url: api + "/users/instruments",
        headers: {"authToken":""}, // données dans les entetes 
        data: [     
            {
            "key": "aid",
            "value": $aid
            }],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/** Requête permettant de vérifier le mail */
function VerifMail(){
    $.ajax({
        type: "POST",
        url: api + "/users/verify",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/***** USERS END **********/

/* ROLES AJAX FUNC */

/**
 * Requête permettant de lister les roles selon le paramètre active
 * @param {*} $active Permet de préciser : 0 = Rôles inactifs; 1 = Rôles actifs; "both" = Les deux;
 */
function ListerRoles($active){
    $.ajax({
        type: "GET",
        url: api + "/roles/",
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key": "active" , "value" : $active}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de modifier un role à partir de ces informations
 * @param {*} $informations Tableau de JSON contenant label et active* (*optionnel) 
 * @param {*} $rid Identifiant du role
 */
function ModifyRole($informations,$rid){
    if ($informations["label"])
        $data["label"] = $informations["label"];
    if ($informations["active"])
        $data["active"] = $informations["active"];
    $.ajax({
        type: "PUT",
        url: api + "/roles/"+$rid,
        headers: {"authToken":""}, // données dans les entetes 
        data: $data,
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de créer un role à partir de ces informations
 * @param {*} $informations Tableau de JSON contenant label et active* (*optionnel) 
 * @param {*} $rid Identifiant du role
 */
function CreateRole($informations,$rid){
    $data["label"] = $informations["label"];
    if ($informations["active"])
        $data["active"] = $informations["active"];
    else
        $data["active"] = 1;
    $.ajax({
        type: "POST",
        url: api + "/roles/",
        headers: {"authToken":""}, // données dans les entetes 
        data: $data,
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}
/***** ROLES END **********/


/* INSTRUMENTS AJAX FUNC */

/**
 * Requête permettant de lister tout les instruments
 */
function ListInstruments(){
    $.ajax({
        type: "GET",
        url: api + "/instruments",
        headers: {"authToken":""}, // données dans les entetes 
        data:[],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de modifier le nom d'un instruments 
 * @param {*} $label Nom de l'instrument
 * @param {*} $iid Identifiant de l'instrument
 */
function ModifyInstruments($label,$iid){
    $.ajax({
        type: "PUT",
        url: api + "/instruments/"+ $iid,
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key": "label" , "value" : $label}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant d'ajouter un instruments
 * @param {*} $label Nom de l'instrument
 */
function CreateInstruments($label){
    $.ajax({
        type: "POST",
        url: api + "/instruments",
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key": "label" , "value" : $label}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}
 
/***** INSTRUMENTS END **********/



/* ACHIEVEMENTS AJAX FUNC */

/**
 * Requête permettant d'obtenir la liste des achievement
 */
function ListAchievements(){
    $.ajax({
        type: "GET",
        url: api + "/achievements",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de modifier un achievement
 * @param {*} $aid Identifiant de l'achievement
 * @param {*} $label Nom de l'achievement
 */
function ModifyAchievements($aid,$label){
    $.ajax({
        type: "PUT",
        url: api + "/achievement/"+$aid,
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key": "label" , "value" : $label}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de créer un achievement
 */
function CreateAchievements(){
    $.ajax({
        type: "POST",
        url: api + "/users/achievements",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}
/* ACHIEVEMENTS END */

/* GROUPS AJAX FUNC */

/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListConv($authToken){
    $.ajax({
        type: "GET",
        url: api + "/groups",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(oRep){
            console.log(oRep);
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
 * Requête permettant de récupérer la liste des permissions d'un groupe
 * @param {*} $gid Identifiant du groupe
 */
function ListPermsConv($gid){
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
 * Requête permettant de récupérer la liste des messages d'un groupe
 * @param {*} $gid Identifiant du groupe
 */
function ListMsgConv($gid,$title,$currentuser,$authToken){
    $.ajax({
        type : "GET",
        url : api + "/groups/" + $gid + "/messages",
        headers:{"authToken" : $authToken},
        data : [],
        error : function(err){
            console.log("Une erreur s'est produite");
            console.log(err);
        },
        success : function(oRep){
            console.log(oRep);
            oRep.titre = $title;
            oRep.id = $currentuser;
            oRep["messages"].sort(function func(e1,e2){ return e1.id - e2.id ;});
            JCreerMessage(oRep);
        },
        dataType: "json"
    });
}

/**
 * Requête permettant de récupérer la liste des réactions d'un message d'un groupe
 * @param {*} $gid Identifiant d'un groupe
 * @param {*} $mid Identifiant d'un message
 */
function ListReactMsgConv($gid, $mid){
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
 * Requête permettant de créer un groupe
 * @param {*} $informations Donnée de l'image & Titre de la discu
 */
function CreateConv($informations){
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

/**
 * Requête permettant d'ajouter un utilisateur à un groupe
 * @param {*} $gid Identifiant du groupe
 * @param {*} $uid Identifiant de l'utilisateur
 */
function AddUserConv($gid, $uid){
    $.ajax({
        type: "POST",
        url: api + "/groups/" + $gid + "/users/" + $uid,
        headers: {"authToken" : ""},
        data: [],
        error: function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep);
            // à voir
        },
        dataType: "json"
    });
}

/**
 * Requête permettant d'envoyer un message dans le groupe
 * @param {} $gid Identifiant du groupe
 * @param {*} $informations Contenu du message & Identifiant du message cible
 */

function AddMsgConv($gid, $informations){

    $data["content"] = $informations["content"];
    if($informations["answerTo"])
        $data["answerTo"] = $informations["answerTo"];
    
    $.ajax({
        type: "POST",
        url: api + "/groups/" + $gid + "/messages",
        headers: {"authToken" : ""},
        data: $data,
        error: function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep);
            // JAjouterMsg
        },
        dataType: "json"
    });

}
/* GROUPS END */

/* POSTS AJAX FUNC */

/** Requête permettant de récupérer la liste des posts */
function ListPosts($authToken){
    $.ajax({
        type: "GET",
        url: api + "/posts",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(oRep){
            console.log("Une erreur s'est produite");
            console.log(oRep);
        },
        success: function(oRep){
            console.log(oRep);
            oRep["posts"].sort(function compare(e1,e2) { return e2.pinned - e1.pinned });
            oRep["posts"].forEach(element => {
                element["membre"] = 1
                if (element.visible == 1)
                    JcreerPost(element);
            });

        },
        dataType: "json"
    });   
}

/**
 * Requête permettant de récupérer la liste des réactions envoyés sur un post
 * @param {*} $pid identifiant du post
 */

function ListPostReactions($pid){
    $.ajax({
        type: "GET",
        url: api + "/posts/"+$pid+"/reactions",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}


/**
 * Requête permettant de récupérer la liste des messages envoyés sur un post
 * @param {*} $pid identifiant du post
 */

function ListPostMessages($pid){
    $.ajax({
        type: "GET",
        url: api + "/posts/"+$pid+"/messages",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    });   
}
/* POSTS END */



/* AGENDAS AJAX FUNC */


/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListCalendars($type = "concert"){
    $.ajax({
        type: "GET",
        url: api + "/agendas/",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            oRep["agendas"].forEach(element => {
                ListCalendarsEvents(element["id"],$type);
            });
        },
        dataType: "json"
    }); 
}

/**
 * Requête permettant de récupérer les événements d'un calendrier
 * @param {*} $aid Identifiant de l'agenda
 */

function ListCalendarsEvents($authToken,$aid){
    $.ajax({
        type: "GET",
        url: api + "/agendas/"+$aid +"/events",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep);
            oRep["events"].forEach(element => {
                JCreerAppel(element);
            });
        },
        dataType: "json"
    }); 
}


/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListCallMembers($authToken,$aid,$eid){
    $.ajax({
        type: "GET",
        url: api + "/agendas/"+$aid+"/event/"+$eid+"/calls",
        headers: {"authToken": $authToken}, // données dans les entetes 
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

/**
 * Requête permettant de créer un nouveau calendrier
 */
function CreateCalendars(){
    $.ajax({
        type: "POST",
        url: api + "/agendas/",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}
  
/**
 * Requête permettant de créer un nouvel évenement dans le calendrier
 * @param {*} $aid Identifiant du calendrier
 */
function CreateEventCalendars($aid){
    $.ajax({
        type: "POST",
        url: api + "/agendas/"+$aid+"/events",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}
/* END AGENDAS */

/* PARTCIPATIONS AJAX FUNC */

/**
 * Requête permettant de lister l'ensemble des participants à un événement
 * @param {*} $uid Identifiant de l'utilisateur
 * @param {*} $aeid Identifiant de l'évenement dans l'agenda
 */
function ListUserParticipation($uid,$aeid){
    $.ajax({
        type: "GET",
        url: api + "/users/"+$uid+"/agendas/"+$aeid+"/participations",
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}

/**
 * Requête permettant de modifier le statut de participation de l'utilisateur à un événement
 * @param {*} $uid Identifiant de l'utilisateur
 * @param {*} $aeid Identifiant de l'évenement dans l'agenda
 * @param {*} $partcipe 
 */
function ModifUserParticipation($uid,$aeid,$partcipe){
    $.ajax({
        type: "PUT",
        url: api + "/users/"+$uid+"/agendas/"+$aeid+"/participations?participation="+$partcipe,
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key":"participation","value":$partcipe}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}

/**
 * Requête permettant d'ajouter le statut de participation de l'utilisateur à un événement
 * @param {*} $uid Identifiant de l'utilisateur
 * @param {*} $aeid Identifiant de l'évenement dans l'agenda
 * @param {*} $partcipe 
 */
function AddUserParticipation($uid,$aeid,$partcipe){
    $.ajax({
        type: "POST",
        url: api + "/users/"+$uid+"/agendas/"+$aeid+"/participations?participation="+$partcipe,
        headers: {"authToken":""}, // données dans les entetes 
        data: [{"key":"participation","value":$partcipe}],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}

/* PARTICIPATION END */







