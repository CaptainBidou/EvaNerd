/************************************************************************/
/*                 CDN SCRIPT                                           */
/***********************************************************************/

src="Bootstrap/js/bootstrap.min.js";
src="Jquery/jquery-3.6.2.min.js";
src="Jquery/jquery-ui.min.js";


/************************************************************************/
/*                 DECLARATION DES VARIABLES                           */
/***********************************************************************/


//variables pour les posts
var JPost =$("<div>").addClass("card").data('type','post').css('width','90%').css('background-color','WhiteSmoke').css('margin-left','auto').css('margin-right','auto');
var JPostTitre=$("<h2>").addClass("card-title").data('type','post_titre').css("float","left").css("margin-left","15%").css("margin-top","4%").css('font-size','400%');//TODO :rajouter des données pour quand on clique
var JPostBody=$("<div>").addClass("card-body").data('type','post_body');
var JPostImage=$("<img>").addClass("card-img-top").data('type','post_image');
var JPostDescription=$("<p>").addClass("card-text").data('type','post_text').css('font-size','300%').css("margin-top","2%");
var JPostProfile = $("<img>").addClass('rounded-circle').data("type","post_profile").css("float","left").css("width","15%");//TODO :rajouter des données pour quand on clique
var JPostEpingle= $("<img>").data("type","post_epingle").attr('src','Ressources/Accueil/epingle.png').css("float","right").css('width','12%');
var JPostLike= $("<img>").data("type","post_like").attr('src','Ressources/Accueil/like.png').css("width","12%").css("float","right").css('margin-left','5%');
var JPostReaction= $("<img>").data("type","post_reaction").attr('src','Ressources/Accueil/reaction.png').css("width","12%").css("float","right").css('margin-left','5%');
var JPostCommentaire= $("<img>").data("type","post_commentaire").attr('src','Ressources/Accueil/commentaire.png').css("width","12%").css("float","right").css('margin-left','5%').css('margin-bottom','1%');



//variables pour le footer
var JFooter =$("<nav>").addClass("navbar").css("background-color","red").data("type","footer").css('height','10%').css('padding','1%').css('position','sticky').css('top','92%').css('z-index','10').css('box-shadow','0px -2px 10px darkred').on("click",function(context){JClickFooter(context);});
var JFooterAccueil= $("<img>").data("type","footer_accueil").css('float','left').attr('src','Ressources/Footer/accueilGris.png').css('height','60%').attr('id','Accueil'); //TODO :rajouter des données pour quand on clique 
var JFooterAppel=$("<img>").data("type","footer_appel").css('float','left').attr('src','Ressources/Footer/appel.png').css('height','60%').attr('id','Appel');//TODO :rajouter des données pour quand on clique
var JFooterCreer=$("<img>").data("type","footer_creer").css('float','left').attr('src','Ressources/Footer/creer.png').css('height','60%').attr('id','Creer');//TODO :rajouter des données pour quand on clique
var JFooterAgenda=$("<img>").data("type","footer_agenda").css('float','right').attr('src','Ressources/Footer/calendrier.png').css('height','60%').attr('id','Calendrier');//TODO :rajouter des données pour quand on clique
var JFooterMail=$("<img>").data("type","footer_mail").css('float','right').attr('src','Ressources/Footer/email.png').css('height','60%').attr('id','Mail');//TODO :rajouter des données pour quand on clique






//variables pour le header
var JHeader =$("<nav>").addClass("navbar").css("background-color","red").data("type","header").css('height','10%').css('padding','1%').css('position','sticky').css('top','0px').css('z-index','10').css('box-shadow','0px 2px 10px darkred').css("margin","0");
var JHeaderLogo = $("<img>").addClass("rounded-circle").data("type","header_logo").css('float','left').attr('src','Ressources/Header/logo.png').css('height','90%');//TODO :rajouter des données pour quand on clique 
var JHeaderProfile=$("<img>").addClass("rounded-circle").data("type","header_profile").css('float','right').css('height','90%');//TODO :rajouter des données pour quand on clique 


var JHeaderTag=$("<button>").data("type","header_tag").attr("type","button").addClass("btn btn-primary dropdown-toggle").val("Categorie").css("background",'darkred').html("Categorie").css('height','50%').css("width","20%").css("font-size","200%");
var JHeaderMenu=$("<div>").addClass("dropdown-menu").data("type",'header_menu');
var JHeaderItem=$("<input>").addClass("dropdown-item").text("dfhskldfjhksjdfhkjh").attr("type","checkbox").data("type",'header_item');
var JHeaderSearch=$("<input>").data("type","header_search").attr("type","text").addClass("form-control").attr("placeholder","Rechercher").css("width","35%").css('height','50%').css("font-size","200%");





//.css('position','absolute').css('bottom','30%').css('left','20%')
//.css("margin-right","39%")



















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
 *              'nom':'Michel',
 *              'prenom':'Jean',
 *              'epingle':1, // boolean
 *              'image':'lien/de/image.png',
 *              'like':'1',//boolean
 *              'reaction':'smiley',//null si aucune réaction
 *              'profile':'lien/de/image.png', //image de profile
 *              'description':'Aujourd hui je fais un concert ça va être génial venez nombreux !!',
 * 
 *          };
 * 
 *  
 */




function JcreerPost(Reponse){
var jClonePost=JPost.clone(true,true);
var jClonePostTitre=JPostTitre.clone(true,true).text(Reponse.prenom+" "+Reponse.nom);
var jClonePostBody=JPostBody.clone(true,true);
var jClonePostImage=JPostImage.clone(true,true).attr('src',Reponse.image);
var jClonePostDescription=JPostDescription.clone(true,true).text(Reponse.description);
var jClonePostProfile=JPostProfile.clone(true,true).attr('src',Reponse.profile);

var jClonePost2=JPost.clone(true,true);
var jClonePostBody2=JPostBody.clone(true,true);


var jClonePostEpingle=JPostEpingle.clone(true,true);
var jClonePostComm=JPostCommentaire.clone(true,true);
var jClonePostLike=JPostLike.clone(true,true);
var jClonePostReact=JPostReaction.clone(true,true);

//.append(jClonePostProfile)
jClonePostBody2.append(jClonePostProfile).append(jClonePostTitre).css("vertical-align","middle").append(jClonePostEpingle);
jClonePost2.append(jClonePostBody2).css("width","100%").css("background-color","lightgray").css("border-radius","5px 5px 0 0");

jClonePostBody.append(jClonePostImage).append(jClonePostDescription).append(jClonePostComm).append(jClonePostLike).append(jClonePostReact);
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
 *          'profile'='lien/de/image.png',
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


if(Reponse.profile!=null){
    var JCloneHeaderProfile=JHeaderProfile.clone(true,true).attr('src',Reponse.profile);
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
    }
    if($(context.target).data("type")=="footer_agenda")
    {JSetFooterColorBlack();
        
        $("#Calendrier").attr("src",'Ressources/Footer/calendrierGris.png');
    }
    if($(context.target).data("type")=="footer_creer")
    {JSetFooterColorBlack();
        
        $("#Creer").attr("src",'Ressources/Footer/creerGris.png');
    }
    if($(context.target).data("type")=="footer_appel")
    {JSetFooterColorBlack();
        
        $("#Appel").attr("src",'Ressources/Footer/appelGris.png');
    }
    if($(context.target).data("type")=="footer_accueil")
    {JSetFooterColorBlack();
        $("#Accueil").attr("src","Ressources/Footer/accueilGris.png");
    
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





