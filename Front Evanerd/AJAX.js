
//var api = "http://localhost/EvaNerd/Back%20Evanerd/api";
var api ="https://evanerds.fr/api/v1";
var authcode = ""; //Code d'auth de l'utilisateur
var member = false; 
var pdp = "";
var user = ""; 
var admin = 0;
var currentComm = 0;
var currentGroup = 0;
var userProfile = {};


/* AUTHENTIFICATION AJAX FUNC */

/**
 * Requête permettant de s'identifier sur le site
 * @param $tel Numéro de téléphone
 * @param $password Mot de passe
 */

function auth($tel,$password){
    //localStorage.setItem('tel',$tel);
    //localStorage.setItem('passwd',$password);
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
        
            admin = oRep["user"].admin;
            localStorage.setItem('admin',admin);
            localStorage.setItem('user',user);
            localStorage.setItem('authToken',authcode);
            localStorage.setItem('member',member);
            localStorage.setItem('pdp',pdp);
        },
        dataType: "json"
    }).done(function launchAPP(){
        $('#header').empty();
        JcreerHeader({"photo" : pdp,"id" : user});
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
        url: api + "/users/achievements",
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

/**
 * Donne un token de vérification. Renvoie soit l'User soit une erreur. 
 * Passe l'activation du compte à 1.
 */
function VerifMail($token){
    $.ajax({
        type: "POST",
        url: api + "/users/verify?token="+$token,
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



/**  * @param $id Id de l'utilisateur */

function  ChargementInfosProfil($id){
    $.ajax({
        type: "GET",
        url: api + "/users/" + $id,
        headers: {"authToken":""}, // données dans les entetes 
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            userProfile.user = oRep.user;
            $id = oRep.user.id;
        },
        dataType: "json",
    }).done(function ListUserInstruments(){
        console.log($id);
        $.ajax({
            type: "GET",
            url: api + "/users/" + $id + "/instruments",
            headers: {"authToken" : ""},
            data: [],
            error : function(){
                console.log("Une erreur s'est produite");
            },
            success: function(oRep){
                console.log(oRep);
                userProfile.instruments = oRep.instruments;
                userProfile.userId=oRep.userId;
            },
            dataType: "json",
        }).done( function ListUserRoles(){
            $.ajax({
                type: "GET",
                url: api + "/users/" + $id + "/roles",
                headers: {"authToken" : ""},
                data: [],
                error : function(){
                    console.log("Une erreur s'est produite");
                },
                success: function(oRep){
                    console.log(oRep);
                    userProfile.Roles = oRep.roles
                    AfficherProfilCharger(oRep);
                },
                dataType: "json",
            });
            });
        })
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
            JClickHeaderMenu(oRep);
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
            oRep.instruments.forEach(element => {
                $("#Instrument").append($('<option>', {
                    value: element.id,
                    text: element.label,
                }).on("click",function func(context){
                    if (!$(context.target).hasClass("selectedInstruments"))
                        $(context.target).addClass("selectedInstruments");
                    else
                        $(context.target).removeClass("selectedInstruments");
                }));
            })
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
            if(member==1)
            {
                JCreerCreerConv();
            }
            
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
function ListMsgConv($gid,$title,$currentuser,$authToken,mode){
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
            if(mode == "NORMAL")
            {
            console.log(oRep);
            oRep.titre = $title;
            oRep.id = $currentuser;
            oRep["messages"].sort(function func(e1,e2){ return e1.id - e2.id ;});
            JCreerMessage(oRep);}

            if(mode == "REC")
            {
                oRep.titre = $title;
                oRep.id = $currentuser;
                oRep["messages"].sort(function func(e1,e2){ return e1.id - e2.id ;});
                JRefreshMessage(oRep,$(".Message-Layout"),$("#DivMessage"));

            }

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
    var $data = {};
    if($informations.image)
        $data.image = $informations.image;
    if($informations.titre)
        $data.titre = $informations.titre;

    $.ajax({
        type:"POST",
        url: api + "/groups?title=" + $informations.titre + "&image=" + $informations.image,
        headers:{"authToken" : authcode},
        data: $data,
        error: function( jqXhr, textStatus, errorThrown){
            console.log(textStatus, errorThrown, jqXhr);
        },
        success : function(oRep){
            console.log(oRep);
            return JAdduserGroupe(oRep.group.id);
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
        headers: {"authToken" : authcode},
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

function AddMsgConv($gid, $informations,$authToken){
    $data=[];
    $data["content"] = $informations.content;
    $url = api + "/groups/" + $gid + "/messages?content=" + $data["content"];
    console.log($informations);
    if($informations.answerTo){
        $data["answerTo"] = $informations.answerTo;
        $url += "&answerTo=" +  $data["answerTo"];
    }
    console.log($data);
    $.ajax({
        type: "POST",
        url: $url,
        headers: {"authToken" : $authToken},
        data: $data,
        error: function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep);
        },
        dataType: "json"
    });

}

function EpinglerMsgConv($mid, currentGroup, $authToken){

    $.ajax({
        type: "PUT",
        url: api + "/groups/" + currentGroup 
        + "/messages/" + $mid 
        + "/pinned",
        headers : {"authToken" : $authToken},
        data: [],
        error: function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep)
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
            $("#page").html("");
            $("#header").html("");
            $("#footer").html("");
            JCreerConnexion();
        },
        success: function(oRep){
            $("#page").html("");
            console.log(oRep);
            oRep["posts"].sort(function compare(e1,e2) { return e2.pinned - e1.pinned });
            oRep["posts"].forEach(element => {
                element["membre"] = 1;
                JcreerPost(element,member,admin);
            });

        },
        dataType: "json"
    });   
}


function setLikePost($pid,$authToken) {
    $.ajax({
        type: "POST",
        url: api + "/posts/"+$pid+"/likes",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(oRep){
            console.log("Une erreur s'est produite");
            console.log(oRep);
        },
        success: function(oRep){

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

function ListPostMessages($pid,$authToken){
    console.log(currentComm);
    $.ajax({
        type: "GET",
        url: api + "/posts/"+$pid+"/messages",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            oRep["comments"].sort(function func(e1,e2){ return e1.id - e2.id});
            JCreerCommentaireLayout(oRep,user);
            currentComm = $pid;
        },
        dataType: "json"
    });   
}


function addComments($message,$pid,$authToken) {
    console.log($pid + " && " + $message);
    console.log(api + "/posts/"+$pid+"/messages?content="+$message)
    $.ajax({
        type: "POST",
        url: api + "/posts/"+$pid+"/messages?content="+$message,
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            $(".card").css("filter","blur(0)");$(".commentaires").remove();
            ListPostMessages(currentComm,$authToken);
        },
        dataType: "json"
    });
}


function UpdateEpinglePost($pid,$authToken){
    $.ajax({
        type: "PUT",
        url: api + "/posts/"+$pid+"/pinned",
        headers: {"authToken":$authToken}, // données dans les entetes 
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

function addPost($visible, $content, $authToken, $img){

    var formData = new FormData();
    formData.append("banner", $img);
    
    $.ajax({
        type: "POST",
        url: api + "/posts?titre=defaut&content="+$content+"&visible="+$visible+"&pinned=0",
        headers: {"authToken":$authToken}, // données dans les entetes
        data: formData,
        processData: false,
        contentType: false,
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep);
        },

        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        },
        dataType: "json"
    });
}

/* POSTS END */



/* AGENDAS AJAX FUNC */


/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListCalendarsEvents($authToken,$type,$all){
    
    $.ajax({
        type: "GET",
        url: api + "/events/?type=" + $type+"&all="+$all,
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error: function( jqXhr, textStatus, errorThrown){
            console.log(textStatus, errorThrown, jqXhr);
        },
        success: function(oRep){
            console.log(oRep); 
            if($all==1)
            {
                oRep["events"].forEach(element => {
                element.titre = element.event;
                JCreerAppel(element,1);});
            return 1;}

            console.log(oRep); 
            if ($type == "extra"){
                oRep["events"].forEach(element => {
                    element.titre = element.event;
                    console.log("extra")
                    getEventParticipation(element,$authToken);
                });
            }
            else
            {   
                if(admin==1)
                { JAppelAdmin();}
                oRep["events"].forEach(element => {
                    element.titre = element.event;
                    JCreerAppel(element);
                });
            }
        },
        dataType: "json"
    }); 
}



function getEventParticipation(eventInfos,$authToken){
    console.log(eventInfos);
    $.ajax({
        type: "GET",
        url: api + "/events/"+eventInfos.id+"/participations",
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            eventInfos["pourcentage"] = parseFloat(oRep.participationsRatio); 
            console.log(eventInfos); 
            JCreerConcert(eventInfos);
        },
        error: function( jqXhr, textStatus, errorThrown){
            console.log(textStatus, errorThrown, jqXhr);
        },
        dataType: "json"
    }); 
}


/** Effectue la requete ajax de listage de conversation et lance l'affichage des compo js*/
function ListCallMembers($authToken,$id){
    $.ajax({
        type: "GET",
        url: api + "/events/"+$id+"/calls",
        headers: {"authToken": $authToken}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            var id = oRep.eventId;
            oRep["calls"].forEach(element => {
                if(!element.reason_desc)
                JCreerProfileAppel(element.user,element.present,"",id);
                else
                JCreerProfileAppel(element.user,element.present,element.reason_desc,id);
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
function CreateEventCalendars($aid,$infos, $authToken){
    console.log($infos);
    $.ajax({
        type: "POST",
        url: api + "/agendas/"+$aid+"/events?event="+ $infos.titre 
                + "&start=" + $infos.date 
                + "&end=" + $infos.end 
                + "&description=" + $infos.description,
        headers: {"authToken":$authToken}, // données dans les entetes 
        data: [
            {"key":"event","value":$infos.titre},
            {"key":"start","value":$infos.date},
            {"key":"end","value":$infos.end},
            {"key":"description","value":$infos.description}
        ],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}

function PostEventJustif($eid,$authToken,$present,$justif){
    $.ajax({
        type: "POST",
        url: api + "/events/"+$eid+"/calls?present="+$present+"&reason="+$justif,
        headers: {"authToken":$authToken},
        data: {"present" : $present, "reason" : $justif},
        error : function(oRep){
            console.log("Une erreur s'est produite");
            console.log(oRep);
        },
        success : function(oRep){
            console.log(oRep);
        },
        dataType : "json"
    });






}
/* END AGENDAS */

/* PARTCIPATIONS AJAX FUNC */

/**
 * Requête permettant de lister l'ensemble des participants à un événement
 * @param {*} $uid Identifiant de l'utilisateur
 * @param {*} $aeid Identifiant de l'évenement dans l'agenda
 */
function ListUserParticipation($uid,$aeid,$authToken){
    $.ajax({
        type: "GET",
        url: api + "/users/"+$uid+"/agendas/"+$aeid+"/participations",
        headers: {"authToken":$authToken}, // données dans les entetes 
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
function AddUserParticipation($id,$partcipe){
    $.ajax({
        type: "POST",
        url: api + "/events/"+$id+"/participations?participation="+$partcipe,
        headers: {"authToken":authcode}, // données dans les entetes 
        data: [{}],
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


function ListUser($div,$name,$idRole)
{
    console.log($idRole);
    
    if($idRole == undefined){
        $idRole = "";
    }
    

    $.ajax({
        type: "GET",
        url: api + "/users?name="+$name+"&idRole="+$idRole,
        headers: {"authToken":""}, // données dans les entetes 
        data: [],
        error : function(){
            console.log("Une erreur s'est produite");
        },
        success: function(oRep){
            console.log(oRep); 
            $($div).empty().show();
            for(var i = 0; i < oRep.users.length; i++){
            JCreerProfilRecherche($div,oRep.users[i]);}


        },
        dataType: "json"
    }); 
}


//PUT /users/ID/events/ID/calls 

function ChangerUserPresence($uid,$aid){
    var reason = "motif non renseigné";
    $.ajax({
        type: "PUT",
        url: api + "/users/"+$uid+"/events/"+$aid+"/calls?present=0&reason="+reason,
        headers: {"authToken":authcode}, // données dans les entetes 
        data: [],
        error: function( jqXhr, textStatus, errorThrown){
            console.log(textStatus, errorThrown, jqXhr);
        },
        success: function(oRep){
            console.log(oRep); 
        },
        dataType: "json"
    }); 
}
