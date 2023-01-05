/************************************************************************/
/*                 CDN SCRIPT                                           */
/***********************************************************************/

src="Bootstrap/js/bootstrap.min.js";
src="Jquery/jquery-3.6.2.min.js";
src="Jquery/jquery-ui.min.js";


/************************************************************************/
/*                 DECLARATION DES VARIABLES                           */
/***********************************************************************/

var JPost =$("<div>").addClass("card").attr('type','post').css('width','60%').css('background-color','WhiteSmoke');
var JPostTitre=$("<h5>").addClass("card-title").attr('type','post_titre').css("vertical-align","middle");
var JPostBody=$("<div>").addClass("card-body").attr('type','post_body');
var JPostImage=$("<img>").addClass("card-img-top").attr('type','post_image');
var JPostDescription=$("<p>").addClass("card-text").attr('type','post_text');
var JPostProfile = $("<img>").addClass('rounded-circle').attr("type","post_profile").css("float","left").css("width","15%");



















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



jClonePostBody2.append(jClonePostProfile).append(jClonePostTitre);
jClonePost2.append(jClonePostBody2).css("width","100%");

jClonePostBody.append(jClonePostImage).append(jClonePostDescription);
jClonePost.append(jClonePost2).append(jClonePostBody);



$("body").append(jClonePost);
}


