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
var JPost =$("<div>").addClass("card").data('type','post').css('width','90%').css('background-color','WhiteSmoke').css('margin-left','auto').css('margin-right','auto');
var JPostTitre=$("<h2>").addClass("card-title").data('type','post_titre').css("float","left").css("margin-left","3%").css("margin-top","3%").css('font-size','400%');//TODO :rajouter des données pour quand on clique
var JPostBody=$("<div>").addClass("card-body").data('type','post_body');
var JPostImage=$("<img>").addClass("card-img-top").data('type','post_image');
var JPostDescription=$("<p>").addClass("card-text").data('type','post_text').css('font-size','300%').css("margin-top","2%");
var JPostProfile = $("<img>").addClass('rounded-circle').data("type","post_profile").css("float","left").css("width","15%");//TODO :rajouter des données pour quand on clique
var JPostEpingle= $("<img>").data("type","post_epingle").attr('src','Ressources/Accueil/epingle.png').css("float","right").css('width','12%');
var JPostLike= $("<img>").data("type","post_like").attr('src','Ressources/Accueil/like.png').css("width","12%").css("float","right").css('margin-left','5%');
var JPostReaction= $("<img>").data("type","post_reaction").attr('src','Ressources/Accueil/reaction.png').css("width","12%").css("float","right").css('margin-left','5%');
var JPostCommentaire= $("<img>").data("type","post_commentaire").attr('src','Ressources/Accueil/commentaire.png').css("width","12%").css("float","right").css('margin-left','5%').css('margin-bottom','1%');



//variables pour le footer
var JFooter =$("<nav>").addClass("navbar").css("background-color","red").data("type","footer").css('height','10%').css('padding','1%').css('position','fixed').css('top','91%').css('z-index','10').css('box-shadow','0px -2px 10px darkred').on("click",function(context){JClickFooter(context);}).css("width","100%");
var JFooterAccueil= $("<img>").data("type","footer_accueil").css('float','left').attr('src','Ressources/Footer/accueilGris.png').css('height','60%').attr('id','Accueil'); //TODO :rajouter des données pour quand on clique 
var JFooterAppel=$("<img>").data("type","footer_appel").css('float','left').attr('src','Ressources/Footer/appel.png').css('height','60%').attr('id','Appel');//TODO :rajouter des données pour quand on clique
var JFooterCreer=$("<img>").data("type","footer_creer").css('float','left').attr('src','Ressources/Footer/creer.png').css('height','60%').attr('id','Creer');//TODO :rajouter des données pour quand on clique
var JFooterAgenda=$("<img>").data("type","footer_agenda").css('float','right').attr('src','Ressources/Footer/calendrier.png').css('height','60%').attr('id','Calendrier');//TODO :rajouter des données pour quand on clique
var JFooterMail=$("<img>").data("type","footer_mail").css('float','right').attr('src','Ressources/Footer/email.png').css('height','60%').attr('id','Mail');//TODO :rajouter des données pour quand on clique






//variables pour le header
var JHeader =$("<nav>").addClass("navbar").css("background-color","red").data("type","header").css('height','10%').css('padding','1%').css('position','sticky').css('top','0px').css('z-index','10').css('box-shadow','0px 2px 10px darkred').css("margin-bottom","2%");
var JHeaderLogo = $("<img>").addClass("rounded-circle").data("type","header_logo").css('float','left').attr('src','Ressources/Header/logo.png').css('height','90%');//TODO :rajouter des données pour quand on clique 
var JHeaderProfile=$("<img>").addClass("rounded-circle").data("type","header_profile").css('float','right').css('height','90%');//TODO :rajouter des données pour quand on clique 
var JHeaderTag=$("<button>").data("type","header_tag").attr("type","button").addClass("btn btn-primary dropdown-toggle").val("Categorie").css("background",'darkred').html("Categorie").css('height','50%').css("width","20%").css("font-size","200%");
var JHeaderMenu=$("<div>").addClass("dropdown-menu").data("type",'header_menu');
var JHeaderItem=$("<input>").addClass("dropdown-item").text("dfhskldfjhksjdfhkjh").attr("type","checkbox").data("type",'header_item');
var JHeaderSearch=$("<input>").data("type","header_search").attr("type","text").addClass("form-control").attr("placeholder","Rechercher").css("width","35%").css('height','50%').css("font-size","200%");





//variables pour les Convs
var JConv =$("<nav>").addClass("navbar").css("background-color","lightgray").data("type","conv").css('height','10%').css('padding','1%').css('box-shadow','0px 2px 10px gray').css("margin-bottom","2%");
var JConvImg = $("<img>").addClass("rounded-circle").data("type","conv_img").css('position','absolute').css('height','80%');//TODO :rajouter des données pour quand on clique 
var JConvp=$("<p>").addClass("navbar-text").data("type","conv_p").css("font-size","3em").css("float","left").css("margin-left","20%").css("text-overflow","ellipsis").css("direction","ltr").css("width","60%").css("white-space","nowrap").css("overflow","hidden");



//variables pour les concerts 
var JConcert =$("<nav>").addClass("navbar").css("background-color","lightgray").data("type","concert").css('height','auto').css('padding','1%').css('box-shadow','0px 2px 10px gray').css("margin-bottom","2%");
var JCouleur="silver";
var JConcertTitre =$("<h2>").addClass("card-title").data('type','concert_titre').css("float","left").css("margin-left","3%").css("margin-top","3%").css('font-size','400%').css("text-decoration","underline");
var JConcertCommentaire=$("<p>").addClass("navbar-text").attr("type","concert_comm").css("font-size","3em").css("float","left").css("margin-left","3%");
var JConcertJeviens=$("<button>").data("type","button").attr("type","concert_jeviens").addClass("btn btn-danger ").val("jeviens").html("Je viens").css('height','10%').css("font-size","200%").css("position","relative").css("margin-left","30%").css("background",'darkred');
var JConcertJevienspas=$("<button>").data("type","button").attr("type","concert_jevienspas").addClass("btn btn-danger ").val("jevienspas").html("Je viens pas").css('height','10%').css("font-size","200%").css("position","relative").css("background",'darkred');
var JConcertJevienspeutetre=$("<button>").data("type","button").attr("type","concert_jevienspe").addClass("btn btn-danger ").val("jevienspe").html("Je viens peut être").css('height','10%').css("font-size","200%").css("position","relative").css("left","0").css("background",'darkred');
var JConcertpourcentage=$("<div>").attr("type","concert_pourcentage").addClass("progress-bar progress-bar-striped progress-bar-animated").data("aria-valuemin","0").data("aria-valuemax","100").css("font-size","300%").css("background-color","red");
var JConcertProgress = $("<div>").attr("type","concert_progress").addClass("progress").css("width","100%").css("height","8%").css("margin-top","5%");
var JConcertDate=$("<p>").attr("type","concert_date").css("font-size","300%").css("color","Gray");







//variables pour les appels

var JRepetition = $("<nav>").addClass("navbar").addClass("navbar").css("background-color","silver").data("type","appel").css('box-shadow','0px 2px 10px gray').css('padding','1%').css("margin-bottom","2%");
var JRepetitionTitre = $("<h2>").addClass("card-title").data('type','appel_titre').css("float","left").css("margin-left","3%").css("margin-top","3%").css('font-size','400%').css("text-decoration","underline");
var JRepetitionDate=$("<p>").attr("type","appel_date").css("font-size","300%").css("color","Gray");
var JRepetitionCommentaire=$("<p>").addClass("navbar-text").attr("type","appel_comm").css("font-size","3em").css("float","left").css("margin-left","3%").css("margin-right","20%");
var JRepetitionPresent=$("<button>").data("type","button").addClass("btn btn-danger ").val("Present").css('height','10%').css("font-size","200%").css("float","right").css("margin-top","2%").css("margin-right","5%").attr("type","present").css("background",'darkred');
var JRepetitionAbsent=$("<button>").data("type","button").addClass("btn btn-danger ").val("Absent").css('height','10%').css("font-size","200%").css("float","right").css("margin-right","5%").css("margin-top","2%").attr("type","absent").css("background",'darkred');
var JRepetitionJustificationText=$("<textarea>").addClass("form-control").attr("placeholder","Motif de l'absence").attr("type","motif").css("font-size","200%").css("margin-left","2%").css("width","90%");
var JRepetitionEnvoyer=$("<button>").data("type","button").addClass("btn btn-danger ").val("Envoyer").html("Envoyer").css('height','10%').css("font-size","200%").css("float","right").css("margin-right","5%").css("margin-top","2%").css("background",'darkred').attr("type","envoyer");
var JRepetitionDiv=$("<div>").css("width","100%");
var JRepetitionRetour=$("<button>").data("type","button").addClass("btn btn-danger ").val("Retour").html("Retour").css('height','10%').css("font-size","200%").css("float","right").css("margin-right","5%").css("margin-top","2%").css("background",'darkred').attr("type","retour");





//variables pour le dropUpCreer
var JDropUpCreer = $("<div>").css("width","30%").css("position","fixed").css("bottom","0%").css("left","35%");
var JDropUpCreerPost=$("<button>").addClass("btn btn-danger ").text("Post").css("margin-bottom","0%").css("font-size","300%").css("width","100%").css("background",'darkred');
var JDropUpCreerEvenement=$("<button>").addClass("btn btn-danger ").text("Evenement").css("margin-bottom","0%").css("font-size","300%").css("width","100%").css("background",'darkred');






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
var jClonePostTitre=JPostTitre.clone(true,true).text(Reponse.prenom+" "+Reponse.nom).css("text-overflow","ellipsis").css("direction","ltr").css("width","60%").css("white-space","nowrap").css("overflow","hidden");
var jClonePostBody=JPostBody.clone(true,true);
var jClonePostImage=JPostImage.clone(true,true).attr('src',Reponse.image);
var jClonePostDescription=JPostDescription.clone(true,true).text(Reponse.description).on("click",function(context){afficherToutleText(context);});
jClonePostDescription=ajouterTextOverflow(jClonePostDescription,100);
var jClonePostProfile=JPostProfile.clone(true,true).attr('src',Reponse.profile);

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



var JCloneConvImage = JConvImg.clone(true,true);
if(Reponse.image!=null)
{JCloneConvImage.attr("src",Reponse.image);}
var JCloneConvp=JConvp.clone(true,true);
if(Reponse.nom==null){
    var i =0;
    JCloneConvp.text(Reponse.participants[i].nom+" "+Reponse.participants[i].prenom);
    for(i=1;i<Reponse.participants.length;i++)
    {
        JCloneConvp.text(JCloneConvp.text()+", "+Reponse.participants[i].nom+" "+Reponse.participants[i].prenom);
    }
}
else
{
    JCloneConvp.text(Reponse.nom);
    
}






JCloneConv.append(JCloneConvImage).append(JCloneConvp);
$("#page").append(JCloneConv);





}

/**
 * 
 * @param {*} Reponse est un json contenant un tableau "message " de json dans lesquels il y a le nom de celui qui a envoyé le message, sa couleur son message sa pdp cette liste va donc du plus ancien message au plus récent 
 * 
 * 
 * Reponse = {'message':[
 * {nom:Mathieu,prenom:Somet,message:"salut les geeks",profile:"Ressources/Test/profile2.jpg"},
 * {nom:Norman,prenom;Thavaux,message"moi aussi jaime les enfants",profile:"Ressources/Test/profile3.jpg"}
 * 
 * 
 * 
 * ]}
 * 
 * 
 * 
 * 
 */
function JAfficherMessageConv(Reponse){





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
    console.log($(context.target).prev().prop('tagName'));
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
var JCloneDropUpCreer=JDropUpCreer.clone(true,true);
var JCloneDropUpCreerPost=JDropUpCreerPost.clone(true,true);
var JCloneDropUpCreerEvenement=JDropUpCreerEvenement.clone(true,true);

JCloneDropUpCreer.append(JCloneDropUpCreerPost).append(JCloneDropUpCreerEvenement).animate({bottom: '9.1%'});

$("#page").append(JCloneDropUpCreer);




}






































