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

var scroll;
var windowscroll;
var nbmessages=0;
var usersLocal;
//variables pour les posts
var JPost =$("<div>").addClass(["card", "post"]).data('type','post');
var JPostTitre=$("<h2>").addClass(["post-titre","card-title"]).data('type','post_titre');//TODO :rajouter des données pour quand on clique
var JPostBody=$("<div>").addClass("card-body").data('type','post_body');
var JPostImage=$("<img>").addClass("card-img-top").data('type','post_image');
var JPostDescription=$("<p>").addClass(["post-description", "card-text"]).data('type','post_text');
var JPostProfile = $("<img>").addClass(["post-profile", 'rounded-circle','profile']).on("click",function(context){AfficherProfile(context.target);}).data("type","post_profile");//TODO :rajouter des données pour quand on clique
var JPostEpingle= $("<img>").addClass("icon").data("type","post_epingle").attr('src','Ressources/Accueil/epingle.png').on("click",function(context){JclickEpingle(context.target);});
var JPostLike= $("<img>").addClass("icon").data("type","post_like").attr('src','Ressources/Accueil/like.png').on("click",function(context){JClickLike(context.target);});
var JPostReaction= $("<img>").addClass("icon").data("type","post_reaction").attr('src','Ressources/Accueil/reaction.png').on("click",function(context){JCreerReactionLayout(context.target);});
var JPostCommentaire= $("<img>").addClass("icon").data("type","post_commentaire").attr('src','Ressources/Accueil/commentaire.png').css('margin-bottom','1%').on("click",function(context){CommentairesPosts(context.target);});

//variables pour le footer
var JFooter =$("<nav>").addClass(["footer","navbar"]).data("type","footer").on("click",function(context){JClickFooter(context);});
var JFooterAccueil= $("<img>").addClass(["footer-icon", "left"]).data("type","footer_accueil").attr('src','Ressources/Footer/accueilGris.png').attr('id','Accueil'); //TODO :rajouter des données pour quand on clique 
var JFooterAppel=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_appel").attr('src','Ressources/Footer/appel.png').attr('id','Appel');//TODO :rajouter des données pour quand on clique
var JFooterCreer=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_creer").attr('src','Ressources/Footer/creer.png').attr('id','Creer');//TODO :rajouter des données pour quand on clique
var JFooterAgenda=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_agenda").attr('src','Ressources/Footer/calendrier.png').attr('id','Calendrier');//TODO :rajouter des données pour quand on clique
var JFooterMail=$("<img>").addClass(["footer-icon", "left"]).data("type","footer_mail").attr('src','Ressources/Footer/email.png').attr('id','Mail');//TODO :rajouter des données pour quand on clique

//variables pour le header
var JHeader =$("<nav>").addClass(["navbar", "header"]).data("type","header");
var JHeaderLogo = $("<img>").addClass(["rounded-circle", "left", "header-icon"]).data("type","header_logo").attr('src','Ressources/Header/logo.png').on("click",function(){JCreerGreetingsLayout();});//TODO :rajouter des données pour quand on clique 
var JHeaderProfile=$("<img>").addClass(["rounded-circle", "right", "header-icon",'profile']).on("click",function(context){AfficherProfile(context.target);}).data("type","header_profile");//TODO :rajouter des données pour quand on clique 
var JHeaderTag=$("<button>").addClass(["btn btn-danger dropdown-toggle header-tag"]).data("type","header_tag").attr("type","button").val("Categorie").html("Categorie").attr("id","dropdownMenuButton").attr("data-toggle","dropdown").attr("aria-haspopup","true").attr("aria-expanded","false").on("click",function(context){RolesMenu(context.target);});
var JHeaderMenu=$("<div>").addClass("dropdown-menu").data("type",'header_menu').attr("aria-labelledby","dropdownMenuButton");
var JHeaderItem=$("<a>").addClass("dropdown-item").text("dfhskldfjhksjdfhkjh").data("type",'header_item').attr("href","#");
var JHeaderSearch=$("<input>").data("type","header_search").attr("type","text").addClass("form-control header-search").attr("placeholder","Rechercher").on("keyup",function(context){if($(".header-search").val()==""){$(".Recherche-Profil").empty().hide();return;}ListUser($(".header-search").val());});

//variables pour les Convs
var JConv =$("<nav>").addClass("navbar conversation").data("type","conv").on("click",function(context){JRecupMessages(context);});//TODO ICI TU APPELLE TA FONCTION
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
var JRepetitionEnvoyer=$("<button>").data("type","button").attr("id","envoyer").addClass("btn btn-danger repetition-bouton").val("Envoyer").html("Envoyer").attr("type","envoyer");
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
var JCreerPostFormPublier=$("<button>").addClass("btn btn-danger ").text("Publier").addClass("buttonPublier").on("click",function(context){JCreerPostPublier(context.target);});
var JCreerPostFormImage=$("<input>").addClass("btn btn-danger form-control-file").attr("type","file").text("Ajouter une image").addClass("buttonAddImage").on("click",function(){return null;});
var JCreerPostFormLabel=$("<p>").addClass("labelTypeForm");


//variables pour la page de création d'évènements
var JCreerEventForm=$("<div>").addClass("divFormPost");
var JCreerEventFormLabel=$("<p>").addClass("labelTypeForm");
var JCreerEventFormTitre=$("<input>").attr("type","text").addClass(["divFormPostTitre","form-control","divFormEventTitre"]).attr("placeholder","Nom de l'évènement");
var JCreerEventFormCheckBox=$("<select>").addClass("divFormPostCheckBox form-control").append($("<option>").text("Concert").addClass("option")).append($("<option>").text("Evènement intraorchestre").addClass("option"));
var JCreerEventFormContent=$("<textarea>").attr("type","text").addClass("divFormPostTitre divFormPostContent form-control").attr("placeholder","Description de l'évènement");
var JCreerEventDate=$("<input>").attr("type","datetime-local").addClass("divFormEventDuree");
var JCreerEventDuree=$("<input>").attr("type","time").addClass("divFormEventDate");
var JCreerEventFormPublier=$("<button>").addClass("btn btn-danger ").text("Publier").addClass("buttonPublier").on("click",function(context){JCreerEventPublier(context.target)});




//variables pour la page du profil
var JProfileImage=$("<img>").addClass("rounded-circle profileImage").data("type","profile_img");
var JProfileNom=$("<p>").addClass("profileNom");
var JProfileTag=$("<p>").addClass("Profiletag");
var JProfilePourcentage=$("<div>").attr("type","profile_pourcentage").addClass("progress-bar progress-bar-striped progress-bar-animated profile-pourcentage").data("aria-valuemin","0").data("aria-valuemax","100");
var JProfileProgress = $("<div>").attr("type","concert_progress").addClass("progress profile-progress");
var JProfileActivite=$("<nav>").addClass("navbar Activite").data("type","activite");
var JProfileActiviteContent=$("<p>").attr("type","activite_context").addClass("activite-content");
var JProfileActiviteImage=$("<img>").addClass("activite-img");
var JProfileReglage=$("<img>").attr("src","Ressources/Profile/reglage.png").addClass("profile-reglage").on("click",function(){JReglageProfile();});
var JProfileDivActivite=$("<div>").addClass("div-profile-activite");
var JProfileArrow=$("<img>").attr("src","Ressources/Profile/arrow.png").addClass("profile-arrow");

//variables pour la vue message
var JMessageHeader = $("<nav>").addClass("navbar MessageHeader");
var JMessageFleche =$("<img>").attr("src","Ressources/Message/arrow.png").addClass("Message-Fleche").on("click",function(){AfficherMessagerie();});
var JMessageReglage=$("<img>").attr("src","Ressources/Message/reglage.png").addClass("Message-Reglage").on("click",function(){JReglageConv();});
var JMessageEpingle=$("<img>").attr("src","Ressources/Message/epingle.png").addClass("Message-Epingle").on("click",function(context){JLayoutPinnedMessages(context.target);});
var JMessageParticipant=$("<p>").addClass("Message-Participant");
var JMessageLayout=$("<div>").addClass("Message-Layout scroller").data("type","layout");
var JMessage=$("<div>").addClass("Message").data("attribut","divMessage").attr("id","DivMessage");
var JMessageInput=$("<textarea>").attr('type','text').addClass("form-control Message-Input").attr("placeholder","Votre message").on("keyup",function(context){if (context.which==13){JEnvoyerMessage($(".Message-Send")[0]);}});
var JMessageSend=$("<img>").addClass("Message-Send ").attr("src","Ressources/Message/send.png").on("click",function(context){EnvoyerMessage($(".Message-Input")[0],$(".Reponse-Message-layup")[0]);$(".Reponse-Message-layup").remove();$(".Message-Input").val("");});
var JMessageDown=$("<div>").addClass("Message-Down");

//variables pour les messages créé par des participants
var JMessageParticipantDiv=$("<div>").addClass("Participant-Div");
var JMessageParticipantProfile=$("<img>").addClass("Participant-Profile rounded-circle profile").on("click",function(context){AfficherProfile(context.target);});
var JMessageParticipantTitre=$("<p>").addClass("Participant-Titre");
var JMessageParticipantRep=$("<img>").addClass("Participant-Rep").attr("src","Ressources/Message/rep.png").on("click",function(context){JclickRep(context.target);});
var JMessageParticipantEpingle=$("<img>").addClass("Participant-Epingle").attr("src","Ressources/Message/epingle.png").on("click",function(context){JclickEpingle(context.target);});
var JMessageParticipantContent=$("<p>").addClass("Participant-content");

//variables pour les messages créé par l'utilisateur actif 
var JMessageActifDiv=$("<div>").addClass("Actif-Div");
var JMessageActifProfile=$("<img>").addClass("Actif-Profile rounded-circle");
var JMessageActifTitre=$("<p>").addClass("Actif-Titre");
var JMessageActifRep=$("<img>").addClass("Actif-Rep").attr("src","Ressources/Message/rep.png").on("click",function(context){JclickRep(context.target);});
var JMessageActifEpingle=$("<img>").addClass("Actif-Epingle").attr("src","Ressources/Message/epingle.png").on("click",function(context){JclickEpingle(context.target);});
var JMessageActifContent=$("<p>").addClass("Actif-content");

//variables pour la connexion
var JConnexion = $("<div>").addClass("divFormConnexion");
var JConnexionTelephone=$("<input>").addClass("form-control Connexion-phone").attr("placeholder","Téléphone").attr("id","tel").attr("type","number");
var JConnexionPwd=$("<input>").addClass("text").addClass("form-control Connexion-pwd").attr("placeholder","Mot de passe").attr("id","pwd").attr("type","password");
var JConnexionSubmit=$("<button>").addClass("btn btn-danger Connexion-Submit").html("Se connecter").on("click",function(context){Connexion();});
var JConnexionP=$("<p>").addClass("Connexion-p");
var JConnexionTitre=$("<h1>").addClass("Connexion-titre");


//variables pour les réglages des messages 
var JReglageMessage=$("<div>").addClass("Reglage-Message-Layout").attr("type","divisionReglage");
var JReglageMessageImage=$("<input>").addClass("btn btn-danger form-control-file Reglage-Message-Image").attr("type","file").text("Ajouter une image").on("click",function(){return null;});
var JReglageMessageImageSubmit=$("<button>").addClass("btn btn-danger Reglage-Message-Submit").html("Ajouter une Image").on("click",function(context){});
var JReglageMessageColor=$("<input>").attr("type","color").addClass("Message-Personne-Color");
var JReglageMessageColorSubmit=$("<button>").addClass("btn btn-danger Reglage-Message-Submit").html("Changer de couleur").on("click",function(context){ var couleur=$(".Message-Personne-Color").val();
                                                                                                                                                        JmodifCouleur(couleur);});
var JReglageMessagePerson=$("<input>").addClass("form-control Message-Personne-Reglage").attr("type","text").attr("placeholder","Nom de la personne");
var JReglageMessagePersonSubmit=$("<button>").addClass("btn btn-danger Reglage-Message-Submit").html("Ajouter une Personne").on("click",function(context){AjouterUtilisateur($(this))});
var JReglageMessageLabel=$("<p>").addClass("Message-Reglage-label");





//VARRIABLES pour les comms des posts
var JCommentaires = $("<div>").addClass("commentaires");
var JCommentairesUp=$("<div>").addClass("commentaires-Up");
var JCommentairesMiddle=$("<div>").addClass("commentaires-Mid");
var JCommentairesCroix=$("<img>").addClass("commentaires-cross").attr("src","Ressources/Accueil/croix.png").on("click",function(){$(".card").css("filter","blur(0)");$(".commentaires").remove();})
var JCommentaireInput=$("<textarea>").attr('type','text').addClass("form-control Commentaire-Input").attr("placeholder","Votre commentaire");
var JCommentaireSend=$("<img>").addClass("Commentaire-Send ").attr("src","Ressources/Message/send.png").on("click",function(context){EnvoyerCommentaire($(".Commentaire-Input")[0]);});
var JCommentaireDown=$("<div>").addClass("Commentaire-Down");



// varuables pour les reactions des posts
var JReaction=$("<div>").addClass("Reaction");
var JReactionUp=$("<div>").addClass("Reaction-Up");
var JReactionMiddle=$("<div>").addClass("Reaction-Mid");
var JReactionCroix=$("<img>").addClass("Reaction-cross").attr("src","Ressources/Accueil/croix.png").on("click",function(){$(".card").css("filter","blur(0)");$(".Reaction").remove();})


//variables pour les reglages du profil
var JReglageProfileLabel=$("<p>").addClass("Reglage-Profil-label");
var JReglageProfileDiv=$("<div>").addClass("Reglage-Profil-Layout").attr("type","divisionReglage");
var JReglageProfileImage=$("<input>").addClass("btn btn-danger form-control-file Reglage-Profil-Image").attr("type","file").text("Ajouter une image").on("click",function(){return null;}).on("change",function(context){$(".Submit-Image-profile").fadeIn(1000);});
var JReglageProfileImageSubmit=$("<button>").addClass("btn btn-danger Reglage-Profil-Submit Submit-Image-profile").html("Ajouter une Image").on("click",function(context){});
var JReglageProfileSelectTelMail=$("<select>").addClass("form-control Reglage-Profil-Select-Tel-Mail").attr("id","exampleFormControlSelect1").append($("<option>").text("Mail").addClass("option")).append($("<option>").text("Telephone").addClass("option")).on("change",function(context){$(".Reglage-Profil-Input-Tel-Mail").fadeIn(1000);});;
var JReglageProfileInputTelMail=$("<input>").addClass("form-control Reglage-Profil-Input-Tel-Mail").attr("type","text").attr("placeholder","Mail ou Telephone").on("keypress",function(context){$(".Submit-Reglage-Profile-mailTel").fadeIn(1000);});
var JReglageProfileSubmitTelMail=$("<button>").addClass("btn btn-danger Reglage-Profil-Submit Submit-Reglage-Profile-mailTel").html("Ajouter un Mail ou un Telephone");
var JReglageProfileTagSelect=$("<select>").addClass("form-control Reglage-Profil-Select-Tag").attr("id","exampleFormControlSelect1").append($("<option>").text("Tag").addClass("option")).on("change",function(context){$(".Reglage-Profil-Input-tag").fadeIn(1000);});
var JReglageProfileTagSubmit=$("<button>").addClass("btn btn-danger Reglage-Profil-Submit Reglage-Profil-Input-tag").html("Ajouter un Tag").on("click",function(context){});




//variables pour les remerciements 
var JGreetings=$("<div>").addClass("Greetings");
var JGreetingsParagraphe=$("<p>").addClass("Greetings-Paragraphe");



//variable pour les categories
var JCategorie=$("<div>").addClass("Categorie");
var JCategorieSelect=$("<select>").addClass("Categorie-Select");
var JCategorieOption=$("<option>").addClass("Categorie-Option");


//variables pour la création de conv
var JCreerConvImg=$("<img>").addClass(["Creer-Conv-Img","rounded-circle"]).attr("src","Ressources/Message/startWrite.png").on("click",function(context){JCreerConvCreer();});

//variables pour la vue de création de conv 

var JConvCreer = $("<div>").addClass("Conv-Creer");
var JConvCreerTitreTitre=$("<h3>").addClass("Conv-Creer-Titre-Titre").html("Création de conversation");
var JConvCreerTitre=$("<input>").attr("type","text").addClass("Conv-Creer-Titre").attr("placeholder","Titre de la conversation");
var JConvCreerMembre=$("<input>").attr("type","text").addClass("Conv-Creer-Membre").attr("placeholder","Membre de la conversation");
var JConvCreerMembreSubmit=$("<button>").addClass("btn btn-danger Conv-Creer-Submit-Membre").html("Ajouter un membre").on("click",function(context){JAjouterUtilisateurConv($(this))});
var JConvCreerSubmit=$("<button>").addClass("btn btn-danger Conv-Creer-Submit").html("Créer la conversation").on("click",function(context){JCreerCreerConvSubmit($(this))});

//variables pour la recherche de profil
var JRechercheProfil=$("<div>").addClass("Recherche-Profil");

var JRechercheProfilDivUser=$("<div>").addClass("Recherche-Profil-Div-User");
var JRechercheProfilDivUserImg=$("<img>").addClass(["Recherche-Profil-Div-User-Img","rounded-circle"]);
var JRechercheProfilDivUserNom=$("<p>").addClass("Recherche-Profil-Div-User-Nom");
var JRechercheProfilDivUserPrenom=$("<p>").addClass("Recherche-Profil-Div-User-Prenom");


//variables pour la vue admin des appels
var JFlecheAdmin=$("<img>").addClass("Fleche-Admin").attr("src","Ressources/Appel/arrow.png").on("click",function(){AfficherAppel();});
var JAppelAdminDiv=$("<div>").addClass("Appel-Admin-Div");

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




function JcreerPost(Reponse,membre,admin){
    var jClonePost=JPost.clone(true,true);
    var jClonePostTitre=JPostTitre.clone(true,true).text(Reponse.author.firstName+" "+Reponse.author.lastName).css("text-overflow","ellipsis").css("direction","ltr").css("width","60%").css("white-space","nowrap").css("overflow","hidden");
    var jClonePostBody=JPostBody.clone(true,true);
    var jClonePostImage=JPostImage.clone(true,true).attr('src',Reponse.banner).attr("id-profile",Reponse.author.id);
    var jClonePostDescription=JPostDescription.clone(true,true).text(Reponse.content).on("click",function(context){afficherToutleText(context);});
    jClonePostDescription=ajouterTextOverflow(jClonePostDescription,100);
    var jClonePostProfile=JPostProfile.clone(true,true).attr('src',Reponse.author.photo).data("id-profile",Reponse.author.id);
    var jClonePost2=JPost.clone(true,true).addClass("fixed-banner");
    var jClonePostBody2=JPostBody.clone(true,true);

    var jClonePostEpingle=JPostEpingle.clone(true,true).attr("id_post",Reponse.id);
    if(Reponse.pinned==1)
    {
        jClonePostEpingle.attr("src","Ressources/Accueil/epingleNOIR.png")

    }
    var jClonePostComm=JPostCommentaire.clone(true,true).attr("id_post",Reponse.id);
    var jClonePostLike=JPostLike.clone(true,true).attr("id_post",Reponse.id);
    if(Reponse.liked==1)
    {jClonePostLike.attr("src","Ressources/Accueil/likeBlanc.png");}

    //qsdqsdqd





    var jClonePostReact=JPostReaction.clone(true,true);



    //.append(jClonePostProfile)
    jClonePostBody2.append(jClonePostProfile).append(jClonePostTitre).css("vertical-align","middle");






    jClonePost2.append(jClonePostBody2).css("width","100%").css("background-color","lightgray").css("border-radius","5px 5px 0 0");
    jClonePostBody.append(jClonePostImage).append(jClonePostDescription);

    if(membre==1)
    {
  
    jClonePostBody.append(jClonePostComm).append(jClonePostLike).append(jClonePostReact);
    }

    if(membre==0){

    }
    if(admin==1)
    {
        jClonePostBody2.append(jClonePostEpingle);
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

console.log(Reponse.membre);

    if(Reponse.membre==true){
        JCloneFooter.append(JCloneFooterAcceuil).append(JCloneFooterAppel).append(JCloneFooterCreer).append(JCloneFooterAgenda).append(JCloneFooterMail);
        $("#footer").append(JCloneFooter);
        }
    if(Reponse.membre==false)
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
    var JCloneHeaderItem=JHeaderItem.clone(true,true).text("blabla");
    var JCloneRechercheProfil=JRechercheProfil.clone(true,true).hide();
    JCloneHeaderMenu.append(JCloneHeaderItem);


    
    if (Reponse==null)
    {
       JCloneHeader.append(JCloneHeaderLogo);

    }
    else{
    JCloneHeaderMenu.append(JHeaderItem);


    if(Reponse.photo!=null){
        var JCloneHeaderProfile=JHeaderProfile.clone(true,true).attr('src',Reponse.photo);
    }
    else{
        var JCloneHeaderProfile=null;
    }

    JCloneHeaderProfile.data("id-profile",Reponse.id);

    if (Reponse=null)
    {
       JCloneHeader.append(JCloneHeaderLogo);

    }
   
    JCloneHeader.append(JCloneHeaderLogo).append(JCloneHeaderTag).append(JCloneHeaderMenu).append(JCloneHeaderSearch).append(JCloneRechercheProfil).append(JCloneHeaderProfile);}
    
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
    var JClonedate=JConcertDate.clone(true,true).text(Reponse.startDate);
    var JCloneTitre= JConcertTitre.clone(true,true).text(Reponse.titre);
    //JCloneTitre= ajouterTextOverflow(JCloneTitre,80);
    var JCloneCommentaire= JConcertCommentaire.clone(true,true).text(Reponse.description).css("max-height","20%");
    JCloneCommentaire= ajouterTextOverflow(JCloneCommentaire,80);
    JCloneCommentaire.on("click",function(context){afficherToutleText(context);})
    var JCloneJeviens=JConcertJeviens.clone(true,true).data("id",Reponse.id);
    var JCloneJevienspas=JConcertJevienspas.clone(true,true).data("id",Reponse.id);
    var JCloneJevienspeutetre =JConcertJevienspeutetre.clone(true,true).data("id",Reponse.id);

    var JCloneProgress=JConcertProgress.clone(true,true);
    Reponse.pourcentage=Math.trunc(Reponse.pourcentage*100);
    if(isNaN(Reponse.pourcentage))
    {
        Reponse.pourcentage=0;
    }

    var JClonepourcentage=JConcertpourcentage.clone(true,true).data('aria-valuenow',Reponse.pourcentage+'%').css("width",Reponse.pourcentage+'%').html(Reponse.pourcentage+'%');

    JCloneJeviens.on("click",function(){

        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
        AddUserParticipation(JCloneJeviens.data("id"),"y")
    });
    JCloneJevienspeutetre.on("click",function(){
        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
        AddUserParticipation(JCloneJevienspeutetre.data("id"),"m");
    });
    JCloneJevienspas.on("click",function(){
        JCloneJeviens.fadeOut();
        JCloneJevienspas.fadeOut();
        JCloneJevienspeutetre.fadeOut();
        AddUserParticipation(JCloneJevienspas.data("id"),"n");
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

    if(Reponse.voted==null)
        JCloneconcert.append(JCloneTitre).append(JClonedate).append(JCloneCommentaire).append(JCloneJeviens).append(JCloneJevienspas).append(JCloneJevienspeutetre).append(JCloneProgress);
    else
        JCloneconcert.append(JCloneTitre).append(JClonedate).append(JCloneCommentaire).append(JCloneProgress);

    $('#page').append(JCloneconcert);

}


function JAppelAdmin(Reponse)
{
    var JCloneReglageAdmin=$("<img>").attr("src","Ressources/Appel/Reglage.png").addClass("ReglageAdmin").clone(true,true).on("click",function(){
    var JCloneAppelAdminDiv=JAppelAdminDiv.clone(true,true);    
        
        $("#page").empty();
        JCloneFlecheAdmin=JFlecheAdmin.clone(true,true);
        $("#page").append([JCloneFlecheAdmin,JCloneAppelAdminDiv]);
        

    });
    
    $("#page").append(JCloneReglageAdmin);
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

    JCouleur = JCouleur == "silver" ? "Lightgray" : "silver";
    var JCloneRepetitionEnvoyer=JRepetitionEnvoyer.clone(true,true).hide().data("type_id",Reponse.id);
    var JCloneRepetitionRetour=JRepetitionRetour.clone(true,true).hide();
    var JCloneRepetitionDiv=JRepetitionDiv.clone(true,true).data("type_id",Reponse.id);
    var JCloneRepetitionTitre = JRepetitionTitre.clone(true,true).text(Reponse.titre);
    var JCloneRepetitionDate= JRepetitionDate.clone(true,true).text(Reponse.startDate);
    var JCloneRepetitionCommentaire=JRepetitionCommentaire.clone(true,true).text(Reponse.description);
    JCloneRepetitionCommentaire=ajouterTextOverflow(JCloneRepetitionCommentaire,80);
    var JCloneRepetitionPresent=JRepetitionPresent.clone(true,true);
    var JCloneRepetitionAbsent=JRepetitionAbsent.clone(true,true);
    var JCloneRepetitionJustification2=JRepetitionJustificationText.clone(true,true).hide();
    // EVENT CLICK
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
        console.log($(".repetition-justification").val());
        JCloneRepetitionEnvoyer.data("motif",$(".repetition-justification").val());
        JRecupId(context.target);
        JCloneRepetition.hide();

        EnvoyerJustif(JCloneRepetitionEnvoyer);
    
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
    var JCloneProfileArrow=JProfileArrow.clone(true,true).on("click",function(){JRevenirProfile();});
    $("#page").append([JCloneProfileArrow,JCloneProfileReglage]);

    var JCloneProfileProgress=JProfileProgress.clone(true,true);
    var JCloneProfileImage=JProfileImage.clone(true,true).attr("src",Reponse.photo);
    var JCloneProfilePourcentage=JProfilePourcentage.clone(true,true).data('aria-valuenow',Reponse.pourcentage+'%').css("width",Reponse.pourcentage+'%').html(Reponse.pourcentage+'%');
    var JCloneProfileNom=JProfileNom.clone(true,true).text(Reponse.firstName +" "+ Reponse.lastName);  
var JCloneProfileDivActivite=JProfileDivActivite.clone(true,true);


    if(Reponse.pourcentage<33)
            JCloneProfilePourcentage.css('background-color','red');

        else if(Reponse.pourcentage<63)
            JCloneProfilePourcentage.css('background-color','orange');

        else if(Reponse.pourcentage<100)
            JCloneProfilePourcentage.css('background-color','green');


        

    $("#page").append(JCloneProfileImage);
    $("#page").append(JCloneProfileNom);
    var i;
    for(i=0;i<Reponse.tag.length;i++)
    JCreerProfileTag(Reponse.tag[i]);


    $("#page").append(JCloneProfileProgress.append(JCloneProfilePourcentage));
    $("#page").append(JCloneProfileDivActivite);
    for(i=0;i<Reponse.activity.length;i++)
    {var activity = {"nom":Reponse.firstName,"prenom":Reponse.lastName,"activites":Reponse.activity[i],};
    JCreerProfileActivite(activity);
    }

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
    var JCloneProfileActivite=JProfileActivite.clone(true,true).css("background-color",JCouleur);
    var JCloneProfileActiviteImage=JProfileActiviteImage.clone(true,true).attr("src","Ressources/Footer/calendrier.png");
    var JCloneProfileActiviteContent=JProfileActiviteContent.clone(true,true).text(Reponse.nom+" "+Reponse.prenom+" a participé à l'évènement "+Reponse.activites.nom);
    JCloneProfileActiviteContent=ajouterTextOverflow(JCloneProfileActiviteContent,80);
    JCloneProfileActiviteContent.on("click",function(context){afficherToutleText(context);})
    JCloneProfileActivite.append([JCloneProfileActiviteContent,JCloneProfileActiviteImage]);
    $(".div-profile-activite").append(JCloneProfileActivite);
    if (JCouleur == 'silver')
    JCouleur='Lightgray';
else
    JCouleur='silver';
}

function JRefreshMessage(Reponse,JCloneMessageLayout,JCloneMessage){
JCloneMessageLayout.html("");
JCloneMessage.html("");

    var i=0;
    console.log(Reponse.messages.length);
var j=0;
    for(i=0;i<Reponse.messages.length;i++)
    {
        if(Reponse.messages[i].answerTo!=null)
        {   
            for(j=0;j<Reponse.messages.length;j++)
            {
                if(Reponse.messages[i].answerTo==Reponse.messages[j].id)
                    Reponse.messages[i].answerTo=Reponse.messages[j];
            }


        }

    
        



        
        if(Reponse.messages[i].pinned==1)
            JCreerMessageActif(Reponse.messages[i],JCloneMessageLayout,Reponse.color,0);

        if(Reponse.messages[i].author.id==Reponse.id)
        {
            JCreerMessageActif(Reponse.messages[i],JCloneMessage,Reponse.color,0);
        }
        
        else
        JCreerMessageParticipant(Reponse.messages[i],JCloneMessage,0);
    
    
    
    }

    if(i==nbmessages)
    {
        return ; }
    if(i>nbmessages)
    {
        nbmessages=i;
        window.scrollTo(0, 4000000000000000000000000);

    }
    else
    {
        nbmessages=i;
    return;}

    JCloneMessage.animate({scrollTop:0,})
   // JCloneMessage.on("click",function(){scroll=false;});

 


    //JCloneMessage.scrollBottom = JCloneMessage.scrollHeight;
    //JCloneMessage.scrollTop = JCloneMessage.scrollHeight;
}






function JCreerMessage(Reponse){
    var JCloneMessageHeader = JMessageHeader.clone(true,true);
    var JCloneMessageFleche =JMessageFleche.clone(true,true);
    var JCloneMessageReglage=JMessageReglage.clone(true,true);
    var JCloneMessageEpingle=JMessageEpingle.clone(true,true).show();
    var JCloneMessageLayout=JMessageLayout.clone(true,true);

    var JCloneMessageParticipant=JMessageParticipant.clone(true,true).text(Reponse.titre);

    var JCloneMessage=JMessage.clone(true,true);

    var JCloneMessageDown=JMessageDown.clone(true,true);
    var JCloneMessageInput=JMessageInput.clone(true,true);
    var JCloneMessageSend=JMessageSend.clone(true,true).attr("groupId",Reponse.groupId);

    JCloneMessageParticipant=ajouterTextOverflow(JCloneMessageParticipant,55);
    
    $(JCloneMessageHeader).append([JCloneMessageFleche,JCloneMessageParticipant,JCloneMessageReglage,JCloneMessageEpingle,JCloneMessageLayout]);
    $("#page").append(JCloneMessageHeader);
    $("#page").append(JCloneMessage);
    JCloneMessageDown.append([JCloneMessageSend,JCloneMessageInput]);
    $("#page").append(JCloneMessageDown);
    
    JCloneMessageLayout.html("");
    JCloneMessage.html("");

    var i=0;
    console.log(Reponse.messages.length);
    var j=0;
    for(i=0;i<Reponse.messages.length;i++)
    {
        if(Reponse.messages[i].answerTo!=null)
        {   
            for(j=0;j<Reponse.messages.length;j++)
            {
                if(Reponse.messages[i].answerTo==Reponse.messages[j].id)
                    Reponse.messages[i].answerTo=Reponse.messages[j];
            }


        }
        
        if(Reponse.messages[i].pinned==1)
            JCreerMessageActif(Reponse.messages[i],JCloneMessageLayout,Reponse.color,0);

        if(Reponse.messages[i].author.id==Reponse.id)
        {
            JCreerMessageActif(Reponse.messages[i],JCloneMessage,Reponse.color,0);
        }
        
        else
        JCreerMessageParticipant(Reponse.messages[i],JCloneMessage,0);
    
    
    }


    JCloneMessage.animate({scrollTop:0,})

    JCloneMessage.click(function(){window.scroll(0, 0);});


    window.scrollTo(0, 400000000000000);
    //je met le scroll en bas
    
    //cela ne fonctionne pas
    //met le scroll en haut



    //console.log($("#"+i).scrollTop());
    //JCloneMessage.animate({scrollTop:0,})

}

function JCreerMessageParticipant(Reponse,div,rep,Comm)
{   
    
    var JCloneMessageParticipantDiv=JMessageParticipantDiv.clone(true,true).css("background-color","lightgray").attr("id",Reponse.id);
    var JCloneMessageParticipantTitre=JMessageParticipantTitre.clone(true,true).text(Reponse.author.firstName+ " "+Reponse.author.lastName);
    var JCloneMessageParticipantContent=JMessageParticipantContent.clone(true,true).text(Reponse.content);
   
if(Comm==1)
    {   JCloneMessageParticipantDiv.addClass("reponse-commentaire");

}

    if(rep==1)
    {   JCloneMessageParticipantDiv.addClass("reponse-message-linked");
    JCloneMessageParticipantDiv.attr("href","#"+ JCloneMessageParticipantDiv.attr("id"));
    JCloneMessageParticipantDiv.attr("id","");
    JCloneMessageParticipantDiv.append([JCloneMessageParticipantTitre,JCloneMessageParticipantContent]);
    $(div).append(JCloneMessageParticipantDiv);
    return;
    }

    var JCloneMessageParticipantProfile=JMessageParticipantProfile.clone(true,true).attr("src",Reponse.author.photo).data("id-profile",Reponse.author.id);
    
    var JCloneMessageParticipantRep=JMessageParticipantRep.clone(true,true).attr("id_message",Reponse.id);
   
    var JCloneMessageParticipantEpingle=JMessageParticipantEpingle.clone(true,true).attr("id_message",Reponse.id);
    if(Reponse.pinned==1)
    JCloneMessageParticipantEpingle.attr("src","Ressources/Message/epingleNOIR.png")

   
    

    if(Reponse.answerTo!=null)
        { var answer=Reponse.answerTo;
            Reponse.answerTo=null;
            JCloneMessageParticipantDiv.append("<a>");
            $("a",JCloneMessageParticipantDiv).addClass("lien-message").attr("href","#"+answer.id);
        JCreerMessageActif(answer,$("a",JCloneMessageParticipantDiv),"blueviolet",1);}

        if(Comm==1)
        {   JCloneMessageParticipantDiv.addClass("reponse-commentaire");
    
        JCloneMessageParticipantDiv.append([JCloneMessageParticipantTitre,JCloneMessageParticipantContent]);
        $(div).append([JCloneMessageParticipantProfile,JCloneMessageParticipantDiv]); 
        return;       
        }


    JCloneMessageParticipantDiv.append([JCloneMessageParticipantTitre,JCloneMessageParticipantContent,JCloneMessageParticipantRep,JCloneMessageParticipantEpingle]);
    $(div).append([JCloneMessageParticipantProfile,JCloneMessageParticipantDiv]);


}

function JmodifCouleur(Reponse){
    $(".Actif-Div").css("background-color",Reponse);
    $(".reponse-message-linked").css("background-color","blueviolet");
}

function JCreerMessageActif(Reponse,div,couleur,rep)
{

    
        

    if(couleur==null)
        couleur="lightblue";

    var JCloneMessageActifDiv=JMessageActifDiv.clone(true,true).css("background-color",couleur).attr("id",Reponse.id);
    var JCloneMessageActifTitre=JMessageActifTitre.clone(true,true).text(Reponse.author.firstName+ " "+Reponse.author.lastName);
    var JCloneMessageActifContent=JMessageActifContent.clone(true,true).text(Reponse.content);
    
    if(rep==1)
    {   JCloneMessageActifDiv.addClass("reponse-message-linked");
   JCloneMessageActifDiv.attr("reference", JCloneMessageActifDiv.attr("id"));
   JCloneMessageActifDiv.attr("id","");
    JCloneMessageActifDiv.append([JCloneMessageActifTitre,JCloneMessageActifContent]);
    $(div).append(JCloneMessageActifDiv);
    return;
    }
    var JCloneMessageActifProfile=JMessageActifProfile.clone(true,true).attr("src",Reponse.author.photo).data("id-profile",Reponse.author.id);
    if(rep==2)
    {   JCloneMessageActifDiv.addClass("Commentaire-message-layout");
   JCloneMessageActifDiv.attr("reference", JCloneMessageActifDiv.attr("id"));
   JCloneMessageActifDiv.attr("id","");
   $(div).append(JCloneMessageActifProfile);
    JCloneMessageActifDiv.append([JCloneMessageActifTitre,JCloneMessageActifContent]);
    $(div).append(JCloneMessageActifDiv);
    return;
    }



    if(div.data("type")=="layout")
    {   JCloneMessageActifDiv.addClass("messages-layout-pinned");
        JCloneMessageActifDiv.append([JCloneMessageActifTitre,JCloneMessageActifContent]);
    $(div).append(JCloneMessageActifDiv);
    return;
    }
    
    
    var JCloneMessageActifRep=JMessageActifRep.clone(true,true).attr("id_message",Reponse.id);
    var JCloneMessageActifEpingle=JMessageActifEpingle.clone(true,true).attr("id_message",Reponse.id);
    if(Reponse.pinned==1)
    JCloneMessageActifEpingle.attr("src","Ressources/Message/epingleNOIR.png");

   


    if(Reponse.answerTo!=null)
    { var answer=Reponse.answerTo;
        Reponse.answerTo=null;
        JCloneMessageActifDiv.append("<a>");
        $("a",JCloneMessageActifDiv).attr("href","#"+answer.id).addClass("lien-message");
    JCreerMessageActif(answer,$("a",JCloneMessageActifDiv),"blueviolet",1);}

    JCloneMessageActifDiv.append([JCloneMessageActifTitre,JCloneMessageActifContent,JCloneMessageActifRep,JCloneMessageActifEpingle]);
    $(div).append([JCloneMessageActifProfile,JCloneMessageActifDiv]);



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
    JcreerHeader(null);
     $("#page").append(JCloneConnexion);
    

}




function JclickEpingle(target){
    
    if($(target).attr("src")=="Ressources/Message/epingleNOIR.png")
    { 
       $(target).attr("src","Ressources/Message/epingle.png");
       DesEpinglerUnMessage($(target).attr("id_message")); 
       return;
    }

    if($(target).attr("src")=="Ressources/Message/epingle.png")
    {
        $(target).attr("src","Ressources/Message/epingleNOIR.png");
        EpinglerUnMessage($(target).attr("id_message"));// à implémenter dans la couche client 
        return;
    }

    if($(target).attr("src")=="Ressources/Accueil/epingleNOIR.png")
    { 
        $(target).attr("src","Ressources/Accueil/epingle.png");
        DesEpinglerUnPost($(target).attr("id_post"));
        return;                                     
    }


    if($(target).attr("src")=="Ressources/Accueil/epingle.png")
    { 
        $(target).attr("src","Ressources/Accueil/epingleNOIR.png");
        EpinglerUnPost($(target).attr("id_post"));
        return;
    }


}



function JclickRep(target){
    var id = $(target).attr("id_message");
    nbmessages--;
    $(".Message-Down").empty();
    var JCloneMessageEnReponse=$("#"+id).clone(true,true).css("background-color","blueviolet").css("margin","0").css("margin-bottom","1%").addClass("Reponse-Message-layup");
    $("a",JCloneMessageEnReponse).remove();
    $(".Participant-Rep",JCloneMessageEnReponse).remove();
    $(".Participant-Epingle",JCloneMessageEnReponse).remove();
    var JCloneCroix=$("<img>").attr("src","Ressources/Message/croix.png").addClass("Croix-Message-Reponse Reponse-Message-layup").clone(true,true).on("click",function(){$(".Reponse-Message-layup").remove();});
    var JCloneMessageInput=JMessageInput.clone(true,true);
    var JCloneMessageSend=JMessageSend.clone(true,true);
    $(".Message-Down").append([JCloneMessageEnReponse,JCloneCroix,JCloneMessageSend,JCloneMessageInput]);



}





function JLayoutPinnedMessages(target){


if($(target).attr("src")=="Ressources/Message/epingle.png")
{   $(".Message-Layout").animate({left: '25%'}); 
$(target).attr("src","Ressources/Message/epingleNOIR.png");}

else
{$(".Message-Layout").animate({left: '100%'});//TODO RAJOUTER ANIMATION
$(target).attr("src","Ressources/Message/epingle.png");}




}


function JEnvoyerMessage(target){
if($(".Actif-Div",".Message-Down").attr("id")==undefined)
$(".Actif-Div",".Message-Down").attr("id","null");


$(target).attr("message",$(".Message-Input").val()).attr("rep",$(".Actif-Div",".Message-Down").attr("id"));
$(".Message-Input").val("");

console.log($(target).attr("groupId"));


EnvoyerMessage($(target).attr("groupId"),$(target).attr("message"),$(target).attr("rep"));// à impémenter dans la couche client 


}



function JReglageConv(){

    if($(".Reglage-Message-Layout").attr("type")=="divisionReglage")
    {   $(".Reglage-Message-Layout").attr("type","divisionReglageHide");
        $(".Reglage-Message-Layout").animate({left: '100%'});
        return ;}
        if($(".Reglage-Message-Layout").attr("type")=="divisionReglageHide")
        {   $(".Reglage-Message-Layout").attr("type","divisionReglage");
            $(".Reglage-Message-Layout").animate({left: '25%'});
            return ;}
    
    var JLabelTitre= JReglageMessageLabel.clone(true,true).text("Configuration").addClass("Reglage-Message-Titre");
    var JCloneReglageMessage=JReglageMessage.clone(true,true);
    var JLabelImage= JReglageMessageLabel.clone(true,true).text("Nouvelle image du groupe");
    var JCloneReglageMessageImage= JReglageMessageImage.clone(true,true);
    var JCloneReglageMessageImageSubmit=JReglageMessageImageSubmit.clone(true,true);
    var JLabelColor=JReglageMessageLabel.clone(true,true).text("Changer la couleur de message");
    var JCloneReglageMessageColor=JReglageMessageColor.clone(true,true);
    var JCloneReglageMessageColorSubmit=JReglageMessageColorSubmit.clone(true,true);
    var JLabelPerson=JReglageMessageLabel.clone(true,true).text("Ajouter une personne");
    var JCloneReglageMessagePerson=JReglageMessagePerson.clone(true,true);
    var JCloneReglageMessagePersonSubmit=JReglageMessagePersonSubmit.clone(true,true);


    JCloneReglageMessage.append([ JLabelTitre,JLabelImage,JCloneReglageMessageImage,JCloneReglageMessageImageSubmit,JLabelColor,JCloneReglageMessageColor,JCloneReglageMessageColorSubmit,JLabelPerson,
        JCloneReglageMessagePerson,JCloneReglageMessagePersonSubmit]);

$("#page").append(JCloneReglageMessage);
$(".Reglage-Message-Layout").animate({left: '25%'});

}

function JCreerCommentaireLayout(Reponse,user){
console.log("Reponse="+Reponse );
console.log(Reponse );

var JCloneCommentaireUp=JCommentairesUp.clone(true,true);
var JCloneCommentairesMiddle=JCommentairesMiddle.clone(true,true);

$(".card").css("filter","blur(20px)");
var JCloneCommentairesCroix=JCommentairesCroix.clone(true,true);
var JCloneCommentaires =JCommentaires.clone(true,true).css("filter","blur(0)");


JCloneCommentaireUp.append(JCloneCommentairesCroix);

var i;
for(i=0;i<Reponse.comments.length;i++)
{

    if(user==Reponse.comments[i].author.id)
    JCreerMessageActif(Reponse.comments[i],JCloneCommentairesMiddle,"lightblue",2);
    else
    JCreerMessageParticipant(Reponse.comments[i],JCloneCommentairesMiddle,"lightblue",1);

}
var JCloneCommentaireInput=JCommentaireInput.clone(true,true);
var JCloneCommentaireSend=JCommentaireSend.clone(true,true);
var JCloneCommentaireDown=JCommentaireDown.clone(true,true);



JCloneCommentaireDown.append([JCloneCommentaireSend,JCloneCommentaireInput]);


JCloneCommentaires.append([JCloneCommentaireUp,JCloneCommentairesMiddle,JCloneCommentaireDown]);


JCloneCommentaires.hide();
$(".commentaires").css("filter","blur(0px)");
$(".commentaires-layout-message").css("filter","blur(0px)");




$("#page").append(JCloneCommentaires);

JCloneCommentaires.fadeIn(1000);



window.scrollTo(0, 400000000000000);
}




function JClickLike(target){
    if($(target).attr("src")=="Ressources/Accueil/like.png")
    { 
    $(target).attr("src","Ressources/Accueil/likeBlanc.png");}
    
    else
    {//TODO RAJOUTER ANIMATION
    $(target).attr("src","Ressources/Accueil/like.png");}

    LikerPost($(target).attr("id_post"));

}


function JCreerReactionLayout(Reponse){
    $(".card").css("filter","blur(20px)")
    var JCloneReactionUp=JReactionUp.clone(true,true);
    var JCloneReactionMiddle=JReactionMiddle.clone(true,true);
    var JCloneReactionCroix=JReactionCroix.clone(true,true);
    var JCloneReaction =JReaction.clone(true,true);

    JCloneReactionUp.append(JCloneReactionCroix);
    JCloneReaction.append([JCloneReactionUp,JCloneReactionMiddle]);
    JCloneReaction.hide();
    $("#page").append(JCloneReaction);
    JCloneReaction.fadeIn(1000);
}






function JRevenirProfile(){
    if($("#Accueil").attr("src")=="Ressources/Footer/accueilGris.png")
    {  AfficherAccueil(); }
    else
    AfficherMessagerie();

}


function JReglageProfile()
{
if($(".Reglage-Profil-Layout").attr("type-position")=="visible")
    {   
        
        $(".Reglage-Profil-Layout").animate({left: '100%'});
        $(".Reglage-Profil-Layout").attr("type-position","hide");
        return;
    }
if($(".Reglage-Profil-Layout").attr("type-position")=="hide")
{

    $(".Reglage-Profil-Layout").animate({left: '25%'});
    $(".Reglage-Profil-Layout").attr("type-position","visible");
    return;

}



var JCloneReglageLabel=JReglageProfileLabel.clone(true,true).text("Configuration").addClass("Reglage-Profil-Titre");
var JCloneReglageProfile=JReglageProfileDiv.clone(true,true).attr("type-position","visible");
var JCloneReglageChangerImage=JReglageProfileLabel.clone(true,true).text("Changer d'image de profile");
var JCloneReglageProfileImage=JReglageProfileImage.clone(true,true);
var JCloneReglageProfileImageSubmit=JReglageProfileImageSubmit.clone(true,true).hide();
var JCloneReglageLabelMailTel=JReglageProfileLabel.clone(true,true).text("Changer d'adresse mail ou de téléphone");
var JCloneReglageProfileSelectTelMail=JReglageProfileSelectTelMail.clone(true,true);
var JCloneReglageProfileInputTelMail=JReglageProfileInputTelMail.clone(true,true).hide();
var JCloneReglageProfileSubmitTelMail=JReglageProfileSubmitTelMail.clone(true,true).hide();
var JCloneReglageLabelTag=JReglageProfileLabel.clone(true,true).text("Ajouter un Tag");
var JCloneReglageProfileTagSelect=JReglageProfileTagSelect.clone(true,true);
var JCloneReglageProfileTagSubmit=JReglageProfileTagSubmit.clone(true,true).hide();



JCloneReglageProfile.append([JCloneReglageLabel,JCloneReglageChangerImage,JCloneReglageProfileImage,JCloneReglageProfileImageSubmit,JCloneReglageLabelMailTel,JCloneReglageProfileSelectTelMail,JCloneReglageProfileInputTelMail,JCloneReglageProfileSubmitTelMail,JCloneReglageLabelTag,JCloneReglageProfileTagSelect,JCloneReglageProfileTagSubmit]);


$("#page").append(JCloneReglageProfile);
JCloneReglageProfile.animate({left: '25%'});


}





function JCreerGreetingsLayout(Reponse){


if($(".Greetings").attr("visible")=="true")
{   

    $(".Greetings").animate({right: '100%'});
    $(".Greetings").attr("visible","false");
    return;
}
if($(".Greetings").attr("visible")=="false")
{
    
        $(".Greetings").animate({right: '25%'});
        $(".Greetings").attr("visible","true");
        return;
}


var JCloneGreetings=JGreetings.clone(true,true).attr("visible","true");

var JCloneGreetingsParagraphe0=JGreetingsParagraphe.clone(true,true).text("Remerciements").addClass("Greetings-Titre");
JCloneGreetings.append(JCloneGreetingsParagraphe0);


var JCloneGreetingsParagraphe=JGreetingsParagraphe.clone(true,true).text("Design, Programmation et Graphisme par Lukas Grando, Tomás Salvado Robalo, Tomas Treny et Alexandre Fizel.");
JCloneGreetings.append(JCloneGreetingsParagraphe);

var JCloneGreetingsParagraphe2=JGreetingsParagraphe.clone(true,true).text("Projet Réalisé dans le cadre du Projet Informatique de 2ème année de IG2I Centrale Lille en 2022-2023");
JCloneGreetings.append(JCloneGreetingsParagraphe2);

var JCloneGreetingsParagraphe3=JGreetingsParagraphe.clone(true,true).text("Les icones ont été prises sur le site flaticon et réalisées par des artistes");
JCloneGreetings.append(JCloneGreetingsParagraphe3);


$("#page").append(JCloneGreetings);
JCloneGreetings.animate({right: '25%'});



}



function JClickHeaderMenu(Reponse){


if($(".Categorie").attr("affichage")=="true")
{
    $(".Categorie").attr("affichage","false");
    $(".Categorie").hide();
    return;
}

if($(".Categorie").attr("affichage")=="false")
{
    $(".Categorie").attr("affichage","true");
    $(".Categorie").show();
    return;
}



var JCloneCategorie=JCategorie.clone(true,true).attr("affichage","true");
//var JCloneCategorieSelect=JCategorieSelect.clone(true,true);

var i=0;

for(i=0;i<Reponse.roles.length;i++)
{
    JAddOptionsCategorie(JCloneCategorie,Reponse.roles[i]);
}



//JCloneCategorie.append(JCloneCategorieSelect);
$("#page").append(JCloneCategorie);


}


function JAddOptionsCategorie(div,Reponse){

    var JCloneCategorieOption=JCategorieOption.clone(true,true).on("click",function(context){
        console.log($(context.target).data("selected"));
        if( $(context.target).data("selected") == "true")
            {$(context.target).css("background-color","silver");
            $(context.target).data("selected","false");}
        else{$(context.target).css("background-color","gray");
            $(context.target).data("selected","true");}
        
                                                            });


    JCloneCategorieOption.text(Reponse.label);
    div.append(JCloneCategorieOption).on("click",function(){JClickHeaderMenu(null);});

}



//TODO à regarder pour comprendre comment je vous envoie les données



function JCreerPostPublier(target){


    $(target).data("titre",$(".divFormPostTitre").val());
    $(target).data("description",$(".divFormPostContent").val());
    $(target).data("visibilite",$(".divFormPostCheckbox").val());
    $(target).data("image",$(".form-control-file").val());

    CreerPost(target);

}



function JCreerEventPublier(target){

    $(target).data("titre",$(".divFormEventTitre").val());
    $(target).data("type",$(".divFormPostCheckBox").val());
    $(target).data("description",$(".divFormPostContent").val());
    $(target).data("date",$(".divFormEventDuree").val());
    $(target).data("duree",$(".divFormEventDate").val());
    

    
    CreerEvent(target);
    
    //TODO rajouter la fonction qui créé les posts 
    
    
    }


    function JCreerCreerConv(){
        var JCloneCreerConvImg=JCreerConvImg.clone(true,true);


        $("#page").append(JCloneCreerConvImg);
    }



    function JCreerConvCreer(){

        $("#page").empty();
        
        var JCloneConvCreer=JConvCreer.clone(true,true);
        var JCloneConvCreerTitreTitre=JConvCreerTitreTitre.clone(true,true);
        var JCloneConvCreerTitre = JConvCreerTitre.clone(true,true);
        var JCloneconvCreerMembreSubmit=JConvCreerMembreSubmit.clone(true,true);
        var JCloneConvCreerMembre=JConvCreerMembre.clone(true,true);
        var JCloneConvCreerSubmit=JConvCreerSubmit.clone(true,true);


        JCloneConvCreer.append([JCloneConvCreerTitreTitre,JCloneConvCreerTitre,JCloneConvCreerMembre,JCloneconvCreerMembreSubmit,JCloneConvCreerSubmit]);
        $("#page").append([JCloneConvCreer]);

        JCloneConvCreer.animate({top: '10%'});
        
    }




function JCreerProfilRecherche(Reponse){

    console.log(Reponse);
    var JCloneRechercheProfilDivUser=JRechercheProfilDivUser.clone().data("id-profile",Reponse.id).on("click",function(context){AfficherProfile(context.target);$(".Recherche-Profil").empty().hide();});
    var JCloneRechercheProfilDivUserImg=JRechercheProfilDivUserImg.clone().attr("src",Reponse.photo).data("id-profile",Reponse.id);
    var JCloneRechercheProfilDivUserNom=JRechercheProfilDivUserNom.clone().text( Reponse.firstName+ " " +Reponse.lastName ).data("id-profile",Reponse.id);
    ajouterTextOverflow(JCloneRechercheProfilDivUserNom,70);

    JCloneRechercheProfilDivUser.append([JCloneRechercheProfilDivUserImg,JCloneRechercheProfilDivUserNom]);
    $(".Recherche-Profil").append(JCloneRechercheProfilDivUser);


}