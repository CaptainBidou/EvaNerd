/************************************************************************/
/*                 CDN SCRIPT                                           */
/***********************************************************************/

src="Bootstrap/js/bootstrap.min.js";
src="Jquery/jquery-3.6.2.min.js";
src="Jquery/jquery-ui.min.js";
src="evanerd.html";

/************************************************************************/
/*                 DECLARATION DES VARIABLES                           */
/***********************************************************************/


//variables pour les posts
var JPost =$("<div>").addClass(["card", "post"]).data('type','post');
var JPostTitre=$("<h2>").addClass(["post-titre","card-title"]).data('type','post_titre');//TODO :rajouter des données pour quand on clique
var JPostBody=$("<div>").addClass("card-body").data('type','post_body');
var JPostImage=$("<img>").addClass("card-img-top").data('type','post_image');
var JPostDescription=$("<p>").addClass(["post-description", "card-text"]).data('type','post_text');
var JPostProfile = $("<img>").addClass(["post-profile", 'rounded-circle']).data("type","post_profile");//TODO :rajouter des données pour quand on clique
var JPostEpingle= $("<img>").addClass("icon").data("type","post_epingle").attr('src','Ressources/Accueil/epingle.png');
var JPostLike= $("<img>").addClass("icon").data("type","post_like").attr('src','Ressources/Accueil/like.png');
var JPostReaction= $("<img>").addClass("icon").data("type","post_reaction").attr('src','Ressources/Accueil/reaction.png');
var JPostCommentaire= $("<img>").addClass("icon").data("type","post_commentaire").attr('src','Ressources/Accueil/commentaire.png').css('margin-bottom','1%');

//variables pour le footer
var JFooter =$("<nav>").addClass(["footer","navbar"]).data("type","footer").on("click",function(context){JClickFooter(context);});
var JFooterAccueil= $("<img>").addClass(["footer-icon", "left"]).data("type","footer_accueil").attr('src','Ressources/Footer/accueilGris.png').attr('id','Accueil'); //TODO :rajouter des données pour quand on clique 
var JFooterAppel=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_appel").attr('src','Ressources/Footer/appel.png').attr('id','Appel');//TODO :rajouter des données pour quand on clique
var JFooterCreer=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_creer").attr('src','Ressources/Footer/creer.png').attr('id','Creer');//TODO :rajouter des données pour quand on clique
var JFooterAgenda=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_agenda").attr('src','Ressources/Footer/calendrier.png').attr('id','Calendrier');//TODO :rajouter des données pour quand on clique
var JFooterMail=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_mail").attr('src','Ressources/Footer/email.png').attr('id','Mail');//TODO :rajouter des données pour quand on clique

//variables pour le header
var JHeader =$("<nav>").addClass(["navbar", "header"]).data("type","header");
var JHeaderLogo = $("<img>").addClass(["rounded-circle", "left", "header-icon"]).data("type","header_logo").attr('src','Ressources/Header/logo.png');//TODO :rajouter des données pour quand on clique 
var JHeaderProfile=$("<img>").addClass(["rounded-circle", "right", "header-icon"]).data("type","header_profile");//TODO :rajouter des données pour quand on clique 
var JHeaderTag=$("<button>").addClass(["btn btn-primary dropdown-toggle header-tag"]).data("type","header_tag").attr("type","button").val("Categorie").html("Categorie");
var JHeaderMenu=$("<div>").addClass("dropdown-menu").data("type",'header_menu');
var JHeaderItem=$("<input>").addClass("dropdown-item").text("dfhskldfjhksjdfhkjh").attr("type","checkbox").data("type",'header_item');
var JHeaderSearch=$("<input>").data("type","header_search").attr("type","text").addClass("form-control header-search").attr("placeholder","Rechercher");

//variables pour les Convs
var JConv =$("<nav>").addClass("navbar conversation").data("type","conv").on("click",function(context){return $(context.target).attr("type_id");});//TODO ICI TU APPELLE TA FONCTION
var JConvImg = $("<img>").addClass("rounded-circle conversation-image").data("type","conv_img");//TODO :rajouter des données pour quand on clique 
var JConvp=$("<p>").addClass("navbar-text left").data("type","conv_p");

//variables pour les concerts 
var JConcert =$("<nav>").addClass("navbar concert").data("type","concert");
var JCouleur="silver";
var JConcertTitre =$("<h2>").addClass("card-title left concert-titre").data('type','concert_titre');
var JConcertCommentaire=$("<p>").addClass("navbar-text left concert-commentaire").attr("type","concert_comm");
var JConcertJeviens=$("<button>").data("type","button").attr("type","concert_jeviens").addClass("btn btn-danger concert-bouton").val("jeviens").html("Je viens").css("margin-left","30%");
var JConcertJevienspas=$("<button>").data("type","button").attr("type","concert_jevienspas").addClass("btn btn-danger concert-bouton").val("jevienspas").html("Je viens pas");
var JConcertJevienspeutetre=$("<button>").data("type","button").attr("type","concert_jevienspe").addClass("btn btn-danger concert-bouton").val("jevienspe").html("Je viens peut être").css("left","0");
var JConcertpourcentage=$("<div>").attr("type","concert_pourcentage").addClass("progress-bar progress-bar-striped progress-bar-animated concert-pourcentage").data("aria-valuemin","0").data("aria-valuemax","100");
var JConcertProgress = $("<div>").attr("type","concert_progress").addClass("progress concert-progress");
var JConcertDate=$("<p>").attr("type","concert_date").addClass("concert-date");

//variables pour les appels

var JRepetition = $("<nav>").addClass("navbar repetition").addClass("navbar").data("type","appel");
var JRepetitionTitre = $("<h2>").addClass("card-title left repetition-titre").data('type','appel_titre');
var JRepetitionDate=$("<p>").attr("type","appel_date").addClass("repetition-date");
var JRepetitionCommentaire=$("<p>").addClass("navbar-text left repetition-commentaire").attr("type","appel_comm");
var JRepetitionPresent=$("<button>").data("type","button").addClass("btn btn-danger repetition-bouton").val("Present").attr("type","present");
var JRepetitionAbsent=$("<button>").data("type","button").addClass("btn btn-danger repetition-bouton").val("Absent").attr("type","absent");
var JRepetitionJustificationText=$("<textarea>").addClass("form-control repetition-justification").attr("placeholder","Motif de l'absence").attr("type","motif");
var JRepetitionEnvoyer=$("<button>").data("type","button").addClass("btn btn-danger repetition-bouton").val("Envoyer").html("Envoyer").attr("type","envoyer");
var JRepetitionDiv=$("<div>").css("width","100%");
var JRepetitionRetour=$("<button>").data("type","button").addClass("btn btn-danger repetition-bouton").val("Retour").html("Retour").attr("type","retour");





//variables pour le dropUpCreer
var JDropUpCreer = $("<div>").css("width","30%").css("position","fixed").css("bottom","0%").css("left","35%").attr("id","popup").attr("existe","1");
var JDropUpCreerPost=$("<button>").addClass("btn btn-danger ").text("Post").addClass("buttonCreer").on("click",function(){JCreerPostCreer();});
var JDropUpCreerEvenement=$("<button>").addClass("btn btn-danger ").text("Evenement").addClass("buttonCreer").on("click",function(){JCreerEvenementCreer();});


//variables pour la page de création de post 
var JCreerPostForm=$("<div>").addClass("divFormPost");
var JCreerPostFormTitre=$("<input>").attr("type","text").addClass(["divFormPostTitre","form-control"]).attr("placeholder","Titre du post");
var JCreerPostFormContent=$("<textarea>").attr("type","text").addClass("divFormPostTitre divFormPostContent form-control").attr("placeholder","Description du post");
var JCreerPostFormCheckBox=$("<select>").addClass("divFormPostCheckBox form-control").append($("<option>").text("Visible pour tout le monde").addClass("option")).append($("<option>").text("Visible seulement pour les membres").addClass("option"));
var JCreerPostFormPublier=$("<button>").addClass("btn btn-danger ").text("Publier").addClass("buttonPublier").on("click",function(){return null;});
var JCreerPostFormImage=$("<input>").addClass("btn btn-danger form-control-file").attr("type","file").text("Ajouter une image").addClass("buttonAddImage").on("click",function(){return null;});
var JCreerPostFormLabel=$("<p>").addClass("labelTypeForm");


//variables pour la page de création d'évènements
var JCreerEventForm=$("<div>").addClass("divFormPost");
var JCreerEventFormLabel=$("<p>").addClass("labelTypeForm");
var JCreerEventFormTitre=$("<input>").attr("type","text").addClass(["divFormPostTitre","form-control"]).attr("placeholder","Nom de l'évènement");
var JCreerEventFormCheckBox=$("<select>").addClass("divFormPostCheckBox form-control").append($("<option>").text("Concert").addClass("option")).append($("<option>").text("Evènement intraorchestre").addClass("option"));
var JCreerEventFormContent=$("<textarea>").attr("type","text").addClass("divFormPostTitre divFormPostContent form-control").attr("placeholder","Description de l'évènement");
var JCreerEventDate=$("<input>").attr("type","datetime-local");
var JCreerEventDuree=$("<input>").attr("type","time").addClass("divFormEventDate");
var JCreerEventFormPublier=$("<button>").addClass("btn btn-danger ").text("Publier").addClass("buttonPublier").on("click",function(){return null;});




//variables pour la page du profil
var JProfileImage=$("<img>").addClass("rounded-circle profileImage").data("type","profile_img");
var JProfileNom=$("<p>").addClass("profileNom");
var JProfileTag=$("<p>").addClass("Profiletag");
var JProfilePourcentage=$("<div>").attr("type","profile_pourcentage").addClass("progress-bar progress-bar-striped progress-bar-animated profile-pourcentage").data("aria-valuemin","0").data("aria-valuemax","100");
var JProfileProgress = $("<div>").attr("type","concert_progress").addClass("progress profile-progress");
var JProfileActivite=$("<nav>").addClass("navbar Activite").data("type","activite");
var JProfileActiviteContent=$("<p>").attr("type","activite_context").addClass("activite-content");
var JProfileActiviteImage=$("<img>").addClass("activite-img");
var JProfileReglage=$("<img>").attr("src","Ressources/Profile/reglage.png").addClass("profile-reglage");


//variables pour la vue message
var JMessageHeader = $("<nav>").addClass("navbar MessageHeader");
var JMessageFleche =$("<img>").attr("src","Ressources/Message/arrow.png").addClass("Message-Fleche");
var JMessageReglage=$("<img>").attr("src","Ressources/Message/reglage.png").addClass("Message-Reglage");
var JMessageEpingle=$("<img>").attr("src","Ressources/Message/epingle.png").addClass("Message-Epingle");
var JMessageParticipant=$("<p>").addClass("Message-Participant");
var JMessage=$("<div>").addClass("Message").data("attribut","divMessage").attr("id","DivMessage");
var JMessageInput=$("<textarea>").attr('type','text').addClass("form-control Message-Input").attr("placeholder","Votre message");
var JMessageSend=$("<img>").addClass("Message-Send ").attr("src","Ressources/Message/send.png").on("click",function(){return null;});
var JMessageDown=$("<div>").addClass("Message-Down");

//variables pour les messages créé par des participants
var JMessageParticipantDiv=$("<div>").addClass("Participant-Div");
var JMessageParticipantProfile=$("<img>").addClass("Participant-Profile");
var JMessageParticipantTitre=$("<p>").addClass("Participant-Titre");
var JMessageParticipantRep=$("<img>").addClass("Participant-Rep").attr("src","Ressources/Message/rep.png");
var JMessageParticipantEpingle=$("<img>").addClass("Participant-Epingle").attr("src","Ressources/Message/epingle.png");
var JMessageParticipantContent=$("<p>").addClass("Participant-content");

//variables pour les messages créé par l'utilisateur actif 
var JMessageActifDiv=$("<div>").addClass("Actif-Div");
var JMessageActifProfile=$("<img>").addClass("Actif-Profile");
var JMessageActifTitre=$("<p>").addClass("Actif-Titre");
var JMessageActifRep=$("<img>").addClass("Actif-Rep").attr("src","Ressources/Message/rep.png");
var JMessageActifEpingle=$("<img>").addClass("Actif-Epingle").attr("src","Ressources/Message/epingle.png");
var JMessageActifContent=$("<p>").addClass("Actif-content");

//variables pour la connexion
var JConnexion = $("<div>").addClass("divFormConnexion");
var JConnexionTelephone=$("<input>").addClass("form-control Connexion-phone").attr("placeholder","Téléphone").attr("id","tel");
var JConnexionPwd=$("<input>").addClass("text").addClass("form-control Connexion-pwd").attr("placeholder","Mot de passe").attr("id","pwd");
var JConnexionSubmit=$("<button>").addClass("btn btn-danger Connexion-Submit").html("Se connecter").on("click",function(context){Connexion();});
var JConnexionP=$("<p>").addClass("Connexion-p");
var JConnexionTitre=$("<h1>").addClass("Connexion-titre");

/************************************************************************/
/*                 DECLARATION DES FONCTIONS                           */
/***********************************************************************/
/**
 * 
 * @param {*} Reponse
 * 
 * Reponse est un json de format : 
 * 
 * Reponse={
 *              'firstName':'Michel',
 *              'lastName':'Jean',
 *              'pinned':1, // boolean
 *              'visible':1,
 *              'banner':'lien/de/image.png',
 *              'like':'1',//boolean
 *              'reaction':'smiley',//null si aucune réaction ( c la réaction de l'utilisateur actif)
 *              'photo':'lien/de/image.png', //image de profile
 *              'content':'Aujourd hui je fais un concert ça va être génial venez nombreux !!',
 * 
 *          };
 * 
 *  
 */




function JcreerPost(Reponse){
    var jClonePost=JPost.clone(true,true);
    var jClonePostTitre=JPostTitre.clone(true,true).text(Reponse.firstName+" "+Reponse.lastName).css("text-overflow","ellipsis").css("direction","ltr").css("width","60%").css("white-space","nowrap").css("overflow","hidden");
    var jClonePostBody=JPostBody.clone(true,true);
    var jClonePostImage=JPostImage.clone(true,true).attr('src',Reponse.banner);
    var jClonePostDescription=JPostDescription.clone(true,true).text(Reponse.content).on("click",function(context){afficherToutleText(context);});
    jClonePostDescription=ajouterTextOverflow(jClonePostDescription,100);
    var jClonePostProfile=JPostProfile.clone(true,true).attr('src',Reponse.photo);

    var jClonePost2=JPost.clone(true,true);
    var jClonePostBody2=JPostBody.clone(true,true);


    var jClonePostEpingle=JPostEpingle.clone(true,true);
    var jClonePostComm=JPostCommentaire.clone(true,true);
    var jClonePostLike=JPostLike.clone(true,true);
    var jClonePostReact=JPostReaction.clone(true,true);





    //.append(jClonePostProfile)
    jClonePostBody2.append(jClonePostProfile).append(jClonePostTitre).css("vertical-align","middle");





    jClonePost2.append(jClonePostBody2).css("width","100%").css("background-color","lightgray").css("border-radius","5px 5px 0 0");
    jClonePostBody.append(jClonePostImage).append(jClonePostDescription);

    if(Reponse.membre==1)
    {
    jClonePostBody2.append(jClonePostEpingle);
    jClonePostBody.append(jClonePostComm).append(jClonePostLike).append(jClonePostReact);
    }

    if(Reponse.membre==0){

    }


    jClonePost.append(jClonePost2).append(jClonePostBody).css('margin-top','5%');



    $("#page").append(jClonePost);
    }






/**
 * 
 * @param {*} Reponse 
 * 
 * Reponse est soit nulle si l'utilisateur n'est pas connecté
 * soit l'attribut membre est à 1 si l'utilisateur est connecté et membre
 * soit l'attribut membre est à 0 si l'utilisateur est connecté mais pas membre
 * 
 * Reponse = { 
 *              'membre'='1', 
 *              
 *          }
 * 
 * 
 * 
 */
function JcreerFooter(Reponse){
    var JCloneFooter=JFooter.clone(true,true);
    var JCloneFooterAcceuil=JFooterAccueil.clone('true','true');
    var JCloneFooterAppel=JFooterAppel.clone('true','true');
    var JCloneFooterCreer=JFooterCreer.clone('true','true');
    var JCloneFooterAgenda=JFooterAgenda.clone('true','true');
    var JCloneFooterMail=JFooterMail.clone('true','true');



    if(Reponse.membre==1){
        JCloneFooter.append(JCloneFooterAcceuil).append(JCloneFooterAppel).append(JCloneFooterCreer).append(JCloneFooterAgenda).append(JCloneFooterMail);}
        $("#footer").append(JCloneFooter);
        if(Reponse.membre==0)
        {
            JCloneFooter.append(JCloneFooterAcceuil).append(JCloneFooterAgenda).append(JCloneFooterMail);
            $("#footer").append(JCloneFooter);
        }
}

/**
 * 
 * @param {*} Reponse 
 * Reponse est soit nulle si l'utilisateur n'est pas connecté 
 * soit c'est un json de type 
 * 
 * Reponse={
 *          'photo'='lien/de/image.png',
 * 
 * 
 *          }
 * 
 * 
 * 
 * 
 */
function JcreerHeader(Reponse){
    var JCloneHeader=JHeader.clone(true,true);
    var JCloneHeaderLogo=JHeaderLogo.clone(true,true);
    var JCloneHeaderSearch=JHeaderSearch.clone(true,true);
    var JCloneHeaderTag=JHeaderTag.clone(true,true);
    var JCloneHeaderMenu=JHeaderMenu.clone(true,true);
    var JCloneHeaderItem=JHeaderItem.clone(true,true);

    JCloneHeaderMenu.append(JHeaderItem);


    if(Reponse.photo!=null){
        var JCloneHeaderProfile=JHeaderProfile.clone(true,true).attr('src',Reponse.photo);
    }
    else{
        var JCloneHeaderProfile=null;
    }

    JCloneHeader.append(JCloneHeaderLogo).append(JCloneHeaderTag).append(JCloneHeaderMenu).append(JCloneHeaderSearch).append(JCloneHeaderProfile);
    $("#header").append(JCloneHeader);

}

/**
 * 
 * @param {*} context l'evenement clique sur le footer
 * 
 * 
 * Cette fonction transforme un element du footer ,surlequel on a cliqué ,en gris  
 */
function JClickFooter(context){
   
    if($(context.target).data("type")=="footer_mail")
    {JSetFooterColorBlack();
        $("#Mail").attr("src",'Ressources/Footer/emailGris.png');

        AfficherMessagerie();
    }
    if($(context.target).data("type")=="footer_agenda")
    {JSetFooterColorBlack();
        
        $("#Calendrier").attr("src",'Ressources/Footer/calendrierGris.png');
        
        
        AfficherCalendrier();
    }
    if($(context.target).data("type")=="footer_creer")
    {JSetFooterColorBlack();
        
        $("#Creer").attr("src",'Ressources/Footer/creerGris.png');

        AfficherCreer();
    }
    if($(context.target).data("type")=="footer_appel")
    {JSetFooterColorBlack();
        
        $("#Appel").attr("src",'Ressources/Footer/appelGris.png');

        AfficherAppel();
    }
    if($(context.target).data("type")=="footer_accueil")
    {JSetFooterColorBlack();
        $("#Accueil").attr("src","Ressources/Footer/accueilGris.png");


        AfficherAccueil();
    
    }

}
/*
 * Set toutes les icones du footer en noir
 */
function JSetFooterColorBlack()
{
    
    $("#Accueil").attr("src","Ressources/Footer/accueil.png");
    $("#Creer").attr("src",'Ressources/Footer/creer.png');
    $("#Appel").attr("src",'Ressources/Footer/appel.png');
    $("#Calendrier").attr("src",'Ressources/Footer/calendrier.png');
    $("#Mail").attr("src",'Ressources/Footer/email.png');

}






/**
 * 
 * @param {*} Reponse 
 * Reponse est de la forme 
 * 
 * si nom="sdgfpksjf" c le titre de la Conv sinon si c'est null on met le nom de tout les participants
 * si image="qdsqds" c le lien de l'image sinon si c null il 'ny a pas d'image
 *Reponse=
    {id:2,participants=[{id:5,nom:Jean,prenom:Pierre},{id:8,nom:michelmichel,prenom:kubiak},{id:18,nom:Monticul,prenom:Maurice}],nom:"conv des fous",image:"Ressources/Test/image.jpg",}
    Reponse=
    {id:2,participants=[{id:5,nom:Jean,prenom:Pierre},{id:18,nom:Monticul,prenom:Maurice}],nom:null,image:"Ressources/Test/image.jpg"}
}
 * 
 * 
 * 
 */
function JCreerConv(Reponse){
    var JCloneConv =JConv.clone(true,true).attr("type_id",Reponse.id).css("background-color",JCouleur);

    if (JCouleur == 'silver')
        JCouleur='Lightgray';
    else
        JCouleur='silver';
    
    var JCloneConvImage = JConvImg.clone(true,true).attr("type_id",Reponse.id);
    if(Reponse.image!=null)
    {
        JCloneConvImage.attr("src",Reponse.image);
        JCloneConv.append(JCloneConvImage);
    }
    var JCloneConvp=JConvp.clone(true,true);
    JCloneConvp.text(Reponse.titre).attr("type_id",Reponse.id);
    JCloneConv.append(JCloneConvp);
    $("#page").append(JCloneConv);
}









/**
 * 
 * @param {*} Reponse est un json avec comme attribut le nom du concert, la date, 
 * la durée le nombre de personnes qui viennent, le commentaire le nombre de personne sur l'appli et si la personne a déjà voté ou non 
 */


//Reponse={'titre':'superconcert','commentaire':'génial concert pour bien s amuser entre bestiiiees','pourcentage':'27','date':' 01/09/2008','vote':'1',};


function JCreerConcert(Reponse){
    var JCloneconcert=JConcert.clone(true,true).css("background-color",JCouleur);
    var JClonedate=JConcertDate.clone(true,true).text(Reponse.date);
    var JCloneTitre= JConcertTitre.clone(true,true).text(Reponse.titre);
    var JCloneCommentaire= JConcertCommentaire.clone(true,true).text(Reponse.commentaire).css("max-height","20%");
    JCloneCommentaire= ajouterTextOverflow(JCloneCommentaire,80);
    JCloneCommentaire.on("click",function(context){afficherToutleText(context);})
    var JCloneJeviens=JConcertJeviens.clone(true,true);
    var JCloneJevienspas=JConcertJevienspas.clone(true,true);
    var JCloneJevienspeutetre =JConcertJevienspeutetre.clone(true,true);

    var JCloneProgress=JConcertProgress.clone(true,true);
    var JClonepourcentage=JConcertpourcentage.clone(true,true).data('aria-valuenow',Reponse.pourcentage+'%').css("width",Reponse.pourcentage+'%').html(Reponse.pourcentage+'%');

    JCloneJeviens.on("click",function(){
        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
    });
    JCloneJevienspeutetre.on("click",function(){
        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
    });
    JCloneJevienspas.on("click",function(){
        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
    });


    if(Reponse.pourcentage<33)
        JClonepourcentage.css('background-color','red');

    else if(Reponse.pourcentage<63)
        JClonepourcentage.css('background-color','orange');

    else if(Reponse.pourcentage<100)
        JClonepourcentage.css('background-color','green');


    if (JCouleur == 'silver')
        JCouleur='Lightgray';
    else
        JCouleur='silver';


    JCloneProgress.append(JClonepourcentage);

    if(Reponse.vote==0)
        JCloneconcert.append(JCloneTitre).append(JClonedate).append(JCloneCommentaire).append(JCloneJeviens).append(JCloneJevienspas).append(JCloneJevienspeutetre).append(JCloneProgress);
    else
        JCloneconcert.append(JCloneTitre).append(JClonedate).append(JCloneCommentaire).append(JCloneProgress);

    $('#page').append(JCloneconcert);

}

/**
 * 
 * @param {*} Reponse 
 * 
 * 
 * 
 * Reponse est un json de la forme Reponse={"titre":"Répétition tutti","date":"01/12/2019","commentaire","Répétitions au capitole à 18h c'est très important de venir","sex":"0"}
 * 
 * 
 */
function JCreerAppel(Reponse){
    var JCloneRepetition=JRepetition.clone(true,true).css("background-color",JCouleur);

    if (JCouleur == 'silver')
        JCouleur='Lightgray';
    else
        JCouleur='silver';

    var JCloneRepetitionEnvoyer=JRepetitionEnvoyer.clone(true,true).hide();
    var JCloneRepetitionRetour=JRepetitionRetour.clone(true,true).hide();


    var JCloneRepetitionDiv=JRepetitionDiv.clone(true,true);
    var JCloneRepetitionTitre = JRepetitionTitre.clone(true,true).text(Reponse.titre);
    var JCloneRepetitionDate= JRepetitionDate.clone(true,true).text(Reponse.date);
    var JCloneRepetitionCommentaire=JRepetitionCommentaire.clone(true,true).text(Reponse.commentaire);
    JCloneRepetitionCommentaire=ajouterTextOverflow(JCloneRepetitionCommentaire,80);
    var JCloneRepetitionPresent=JRepetitionPresent.clone(true,true);
    var JCloneRepetitionAbsent=JRepetitionAbsent.clone(true,true);
    var JCloneRepetitionJustification2=JRepetitionJustificationText.clone(true,true).hide();


    JCloneRepetition.on("click",function(context){
        
    //if($(context.target).prev().prop('tagName')=="INPUT" ||$(context.target).prev().prop('tagName')=="TEXTAREA"||$(context.target).prev().prop('tagName')=="LABEL")
    if($(context.target).attr("type")=="present")
    {
        
        JCloneRepetitionEnvoyer.show();
        JCloneRepetitionAbsent.hide();
        JCloneRepetitionPresent.hide();
        JCloneRepetitionRetour.show();
        JCloneRepetitionEnvoyer.data("valeur","present");
        
    }

    if($(context.target).attr("type")=="absent")
    {
        JCloneRepetitionJustification2.fadeIn();
        JCloneRepetitionEnvoyer.show();
        JCloneRepetitionAbsent.hide();
        JCloneRepetitionPresent.hide();
        JCloneRepetitionRetour.show();
        JCloneRepetitionEnvoyer.data("valeur","absent");
    }
    if($(context.target).attr("type")=="motif")
    {

    }

    if($(context.target).attr("type")=="envoyer"){
        
        //TODO placer la requete ici
    
        JCloneRepetition.remove();
    
    }
    if($(context.target).attr("type")=="retour"){
        JCloneRepetitionEnvoyer.hide();
        JCloneRepetitionAbsent.show();
        JCloneRepetitionPresent.show();
        JCloneRepetitionRetour.hide();
        JCloneRepetitionJustification2.hide();
        
    }

    if($(context.target).attr("type")=="appel_comm"){

    afficherToutleText(context,100); 
    }
    /*if (JCloneRepetitionCommentaire.css("text-overflow")=="ellipsis")
    {
        JCloneRepetitionCommentaire.css("text-overflow","").css("direction","").css("white-space","").css("overflow","");
    }
    else
    {
        JCloneRepetitionCommentaire.css("text-overflow","ellipsis").css("direction","ltr").css("white-space","nowrap").css("overflow","hidden");
    }*/

    });

    if(Reponse.sex==1)
    {
        JCloneRepetitionPresent.text("Présent");
        JCloneRepetitionAbsent.text("Absent");

    }
    else
    {
        JCloneRepetitionPresent.text("Présente");
        JCloneRepetitionAbsent.text("Absente");
    }



    JCloneRepetitionDiv.append(JCloneRepetitionPresent).append(JCloneRepetitionAbsent).append(JCloneRepetitionJustification2).append(JCloneRepetitionEnvoyer).append(JCloneRepetitionRetour);
    JCloneRepetition.append(JCloneRepetitionTitre).append(JCloneRepetitionDate).append(JCloneRepetitionCommentaire).append(JCloneRepetitionDiv);



    $("#page").append(JCloneRepetition);





}

function ajouterTextOverflow(objet,taille)
{
    objet.css("text-overflow","ellipsis").css("direction","ltr").css("width",taille+"%").css("white-space","nowrap").css("overflow","hidden");
    return objet;
}


function afficherToutleText(context){
if ($(context.target).css("text-overflow")=="ellipsis")
{
    $(context.target).css("text-overflow","").css("direction","").css("white-space","").css("overflow","");
}
else
{
    $(context.target).css("text-overflow","ellipsis").css("direction","ltr").css("white-space","nowrap").css("overflow","hidden");
}

}





function JCreerDropUpCreer(){
if($("#popup").attr("existe")==1)
{
    $("#popup").attr("existe","0");
    $("#popup").animate({bottom: '0%'});
    //$("#popup").remove();
  

}

else if($("#popup").attr("existe")==0)
{
    $("#popup").animate({bottom: '9.1%'});
    $("#popup").attr("existe","1");
}

else
{
    var JCloneDropUpCreer=JDropUpCreer.clone(true,true);
    var JCloneDropUpCreerPost=JDropUpCreerPost.clone(true,true);
    var JCloneDropUpCreerEvenement=JDropUpCreerEvenement.clone(true,true);

    JCloneDropUpCreer.append(JCloneDropUpCreerPost).append(JCloneDropUpCreerEvenement).animate({bottom: '9.1%'});

    $("#page").append(JCloneDropUpCreer);

}


}


function JCreerEvenementCreer(){
    $("#popup").remove();
    $("#page").empty();


    var JCloneCreerEventForm=JCreerEventForm.clone(true,true);
    var JCloneCreerEventFormTitreLabel=JCreerEventFormLabel.clone(true,true).text("Nom de l'évènement");
    var JCloneCreerEventFormTitre=JCreerEventFormTitre.clone(true,true);

    var JCloneCreerEventFormCheckboxLabel=JCreerEventFormLabel.clone(true,true).text("Type d'évènement");
    var JCloneCreerEventFormCheckBox=JCreerEventFormCheckBox.clone(true,true);

    var JCloneCreerEventFormContentLabel=JCreerEventFormLabel.clone(true,true).text("Description de l'évènement");
    var JCloneCreerEventFormContent=JCreerEventFormContent.clone(true,true);

    var JCloneCreerEventFormDateLabel=JCreerEventFormLabel.clone(true,true).text("Date de l'évènement");
    var JCloneCreerEventDate=JCreerEventDate.clone(true,true);

    var JCloneCreerEventFormDureeLabel=JCreerEventFormLabel.clone(true,true).text("Durée de l'évènement");
    var JCloneCreerEventDuree=JCreerEventDuree.clone(true,true);

    var JCloneCreerEventFormPublier=JCreerEventFormPublier.clone(true,true);


JCloneCreerEventForm.append([JCloneCreerEventFormTitreLabel,JCloneCreerEventFormTitre,JCloneCreerEventFormCheckboxLabel,JCloneCreerEventFormCheckBox,JCloneCreerEventFormContentLabel,JCloneCreerEventFormContent,JCloneCreerEventFormDateLabel,JCloneCreerEventDate,JCloneCreerEventFormDureeLabel,JCloneCreerEventDuree,JCloneCreerEventFormPublier]);

console.log("qsdqsd");
$("#page").append(JCloneCreerEventForm);






}


function JCreerPostCreer(){
    $("#popup").remove();
    $("#page").empty();


    var JCLonePostForme=JCreerPostForm.clone(true,true);
    var JClonePostFormeTitreLabel=JCreerPostFormLabel.clone(true,true).text("Titre du poste");
    var JClonePostFormeTitre=JCreerPostFormTitre.clone(true,true);
    var JClonePostFormeContentLabel=JCreerPostFormLabel.clone(true,true).text("Description du poste");
    var JClonePostFormeContent=JCreerPostFormContent.clone(true,true);
    var JClonePostFormeCBLabel=JCreerPostFormLabel.clone(true,true).text("Visibilité du poste").attr("label",".divFormPostCheckBox");
    var JClonePostFormeCheckBox=JCreerPostFormCheckBox.clone(true,true);
    var JClonePostFormeImage=JCreerPostFormImage.clone(true,true);
    var JClonePostFormePublier=JCreerPostFormPublier.clone(true,true);



    JCLonePostForme.append([JClonePostFormeTitreLabel,JClonePostFormeTitre,JClonePostFormeContentLabel,JClonePostFormeContent,JClonePostFormeCBLabel,
        JClonePostFormeCheckBox,JClonePostFormeImage, JClonePostFormePublier]);

        $("#page").append(JCLonePostForme);
    


}



function JCreerProfile(Reponse){
var JCloneProfileReglage=JProfileReglage.clone(true,true);
$("#page").append(JCloneProfileReglage);

var JCloneProfileProgress=JProfileProgress.clone(true,true);
var JCloneProfileImage=JProfileImage.clone(true,true).attr("src",Reponse.photo);
var JCloneProfilePourcentage=JProfilePourcentage.clone(true,true).data('aria-valuenow',Reponse.pourcentage+'%').css("width",Reponse.pourcentage+'%').html(Reponse.pourcentage+'%');
var JCloneProfileNom=JProfileNom.clone(true,true).text(Reponse.firstName +" "+ Reponse.lastName);  

if(Reponse.pourcentage<33)
        JCloneProfilePourcentage.css('background-color','red');

    else if(Reponse.pourcentage<63)
        JCloneProfilePourcentage.css('background-color','orange');

    else if(Reponse.pourcentage<100)
        JCloneProfilePourcentage.css('background-color','green');


    if (JCouleur == 'silver')
        JCouleur='Lightgray';
    else
        JCouleur='silver';




$("#page").append(JCloneProfileImage);
$("#page").append(JCloneProfileNom);
var i;
for(i=0;i<Reponse.tag.length;i++)
JCreerProfileTag(Reponse.tag[i]);


$("#page").append(JCloneProfileProgress.append(JCloneProfilePourcentage));

for(i=0;i<Reponse.activity.length;i++)
{var activity = {"nom":Reponse.firstName,"prenom":Reponse.lastName,"activites":Reponse.activity[i],};
JCreerProfileActivite(activity);}





}


/**
 * 
 * @param {*} Reponse
 * 
 *  Reponse={"tag":[,{"nom":"Flute","couleur":"pink"}],}
 * Nous on prend juste un json du type {"nom":"Haut bois","couleur":"blue"}
 *  
 */
function JCreerProfileTag(Reponse){
var JCloneProfileTag=JProfileTag.clone(true,true).text(Reponse.nom).css("background-color",Reponse.couleur);
$("#page").append(JCloneProfileTag);


}


/**
 * 
 * @param {*} Reponse
 * 
 * 
 * Reponse={"nom":jean,"prenom":"pierre","activites":[{"nom":"repetition tutti"},{"nom":"repetition haut bois"}]} 
 */
function JCreerProfileActivite(Reponse){
var JCloneProfileActivite=JProfileActivite.clone(true,true);
var JCloneProfileActiviteImage=JProfileActiviteImage.clone(true,true).attr("src","Ressources/Footer/calendrier.png");
var JCloneProfileActiviteContent=JProfileActiviteContent.clone(true,true).text(Reponse.nom+" "+Reponse.prenom+" a participé à l'évènement "+Reponse.activites.nom);
JCloneProfileActiviteContent=ajouterTextOverflow(JCloneProfileActiviteContent,80);
JCloneProfileActiviteContent.on("click",function(context){afficherToutleText(context);})
JCloneProfileActivite.append([JCloneProfileActiviteContent,JCloneProfileActiviteImage]);
$("#page").append(JCloneProfileActivite);

}


function JCreerMessage(Reponse){
    var JCloneMessageHeader = JMessageHeader.clone(true,true);
    var JCloneMessageFleche =JMessageFleche.clone(true,true);
    var JCloneMessageReglage=JMessageReglage.clone(true,true);
    var JCloneMessageEpingle=JMessageEpingle.clone(true,true);
    var JCloneMessageParticipant=JMessageParticipant.clone(true,true).text(Reponse.titre);

    var JCloneMessage=JMessage.clone(true,true);

    var JCloneMessageDown=JMessageDown.clone(true,true);
    var JCloneMessageInput=JMessageInput.clone(true,true);
    var JCloneMessageSend=JMessageSend.clone(true,true);

    JCloneMessageParticipant=ajouterTextOverflow(JCloneMessageParticipant,60);
    
    $(JCloneMessageHeader).append([JCloneMessageFleche,JCloneMessageParticipant,JCloneMessageReglage,JCloneMessageEpingle]);
    $("#page").append(JCloneMessageHeader);
    $("#page").append(JCloneMessage);
JCloneMessageDown.append([JCloneMessageSend,JCloneMessageInput]);
$("#page").append(JCloneMessageDown);

    JCreerMessageParticipant(Reponse,JCloneMessage);
    

}

function JCreerMessageParticipant(Reponse,div)
{

    var JCloneMessageParticipantDiv=JMessageParticipantDiv.clone(true,true);
    var JCloneMessageParticipantProfile=JMessageParticipantProfile.clone(true,true).attr("src",Reponse.banner);
    var JCloneMessageParticipantTitre=JMessageParticipantTitre.clone(true,true).text(Reponse.firstName+ " "+Reponse.lastName);
    var JCloneMessageParticipantRep=JMessageParticipantRep.clone(true,true);
    var JCloneMessageParticipantEpingle=JMessageParticipantEpingle.clone(true,true);
    var JCloneMessageParticipantContent=JMessageParticipantContent.clone(true,true).text(Reponse.content);



    JCloneMessageParticipantDiv.append([JCloneMessageParticipantTitre,JCloneMessageParticipantRep,JCloneMessageParticipantEpingle,JCloneMessageParticipantContent]);
$(div).append([JCloneMessageParticipantProfile,JCloneMessageParticipantDiv]);

}

function JCreerMessageActif(Reponse,div)
{



}



function JCreerConnexion(){

    var JCloneTitre=JConnexionTitre.clone(true,true).text("Connexion");
    var JCloneLegendTel=JConnexionP.clone(true,true).text("Téléphone");
    var JCloneLegendPwd=JConnexionP.clone(true,true).text("Mot de Passe");
    var JCloneConnexion =  JConnexion.clone(true,true);
    var JCloneConnexionTelephone= JConnexionTelephone.clone(true,true);
    var JCloneConnexionPwd=JConnexionPwd.clone(true,true);
    var JCloneConnexionSubmit=JConnexionSubmit.clone(true,true);

    JCloneConnexion.append([JCloneTitre,JCloneLegendTel,JCloneConnexionTelephone,JCloneLegendPwd,JCloneConnexionPwd,JCloneConnexionSubmit]);
    $("#page").append(JCloneConnexion);


}



















