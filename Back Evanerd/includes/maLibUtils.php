<?php


/**
 * @file maLibUtils.php
 * Ce fichier définit des fonctions d'accés ou d'affichage pour les tableaux superglobaux
 */

/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramétre dans un des tableaux GET, POST, COOKIES, SESSION
 * Renvoie false si le paramétre est vide ou absent
 * @note l'utilisation de empty est critique : 0 est empty !!
 * Lorsque l'on teste, il faut tester avec un ===
 * @param string $nom
 * @param string $type
 * @return string|boolean
 */
function valider($nom,$type="REQUEST")
{	
	switch($type)
	{
		case 'REQUEST': 
		if(isset($_REQUEST[$nom]) && !($_REQUEST[$nom] == "")) 	
			return proteger($_REQUEST[$nom]); 	
		break;
		case 'GET': 	
		if(isset($_GET[$nom]) && !($_GET[$nom] == "")) 			
			return proteger($_GET[$nom]); 
		break;
		case 'POST': 	
		if(isset($_POST[$nom]) && !($_POST[$nom] == "")) 	
			return proteger($_POST[$nom]); 		
		break;
		case 'COOKIE': 	
		if(isset($_COOKIE[$nom]) && !($_COOKIE[$nom] == "")) 	
			return proteger($_COOKIE[$nom]);	
		break;
		case 'SESSION': 
		if(isset($_SESSION[$nom]) && !($_SESSION[$nom] == "")) 	
			return $_SESSION[$nom]; 		
		break;
		case 'SERVER': 
		if(isset($_SERVER[$nom]) && !($_SERVER[$nom] == "")) 	
			return $_SERVER[$nom]; 		
		break;
	}
	return false; // Si pb pour récupérer la valeur 
}


/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramétre dans un des tableaux GET, POST, COOKIE, SESSION
 * Prend un argument définissant la valeur renvoyée en cas d'absence de l'argument dans le tableau considéré

 * @param string $nom
 * @param string $defaut
 * @param string $type
 * @return string
*/
function getValue($nom,$defaut=false,$type="REQUEST")
{
	// NB : cette commande affecte la variable resultat une ou deux fois
	if (($resultat = valider($nom,$type)) === false)
		$resultat = $defaut;

	return $resultat;
}

/**
*
* Evite les injections SQL en protegeant les apostrophes par des '\'
* Attention : SQL server utilise des doubles apostrophes au lieu de \'
* ATTENTION : LA PROTECTION N'EST EFFECTIVE QUE SI ON ENCADRE TOUS LES ARGUMENTS PAR DES APOSTROPHES
* Y COMPRIS LES ARGUMENTS ENTIERS !!
* @param string $str
*/
function proteger($str)
{
	// attention au cas des select multiples !
	// On pourrait passer le tableau par référence et éviter la création d'un tableau auxiliaire
	if (is_array($str))
	{
		$nextTab = array();
		foreach($str as $cle => $val)
		{
			$nextTab[$cle] = addslashes($val);
		}
		return $nextTab;
	}
	else 	
		return addslashes ($str);
	//return str_replace("'","''",$str); 	//utile pour les serveurs de bdd Crosoft
}



function tprint($tab)
{
	echo "<pre>\n";
	print_r($tab);
	echo "</pre>\n";	
}
/*
function debug($nom,$val) {
	global $data;

	if (isset($_REQUEST["debug-data"])) {
		$data["debug"][$nom] = $val;
	}
	if (isset($_REQUEST["debug"])) {
		echo "$nom: $val\n<br/>\n";
	}
}*/

function debug($nom,$val) {
	global $data;

	if (valider("debug-data")) {
		$data["debug"][$nom] = $val;
	} 
	if (valider("debug")) {
		echo "$nom: $val\n<br/>\n";
	}
	if (valider("HTTP_DEBUG_DATA","SERVER")) {
		$data["debug"][$nom] = $val;
	} 
	if (valider("HTTP_DEBUG","SERVER")) {
		echo "$nom: $val\n<br/>\n";
	}	
}


function is_id($i) {
	$i = intval($i);
	return (is_int($i) && ($i!=0) );
}

function is_check($i) {
	$i = intval($i);
	return ( ($i==0) || ($i==1)); 
}




?>
