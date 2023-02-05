<?php

// CONSTANTES UPLOADIMAGE
define("IMAGE_MAXSIZE",25097152); // 25mb taille max
define("IMAGE_ERR_NOTIMAGE", -1);
define("IMAGE_ERR_SIZE", -2);
define("IMAGE_ERR_BADEXTENSION", -3);
define("IMAGE_ERR_UPLOAD", -4);

// CONSTANTE DIR
define("DIR_RESSOURCES", "/EvaNerd/Back Evanerd/ressources"); // Répertoire depuis localhost/
define("DIR_USERS", "./ressources/users/");
define("DIR_GROUPS", "./ressources/groups/");
define("DIR_POSTS", "./ressources/posts/");
/**
 * Vérifie l'existence (isset) et la taille (non vide) d'un paramétre dans un des tableaux GET, POST, COOKIES, SESSION
 * Renvoie false si le paramétre est vide ou absent
 * @note l'utilisation de empty est critique : 0 est empty !!
 * Lorsque l'on teste, il faut tester avec un ===
 * @param string $nom
 * @param string $type
 * @return string|boolean
 */
function valider($nom,$type="REQUEST") {
	
	// Fonctionnnement généralisé
	if(is_array($type)) {
		if(isset($type[$nom]) && !($type[$nom] == "")) 	
			return proteger($type[$nom]);
		
		else return false;
	}

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

function validUser($id) {
	if($id == false) sendError("Token invalide !", HTTP_BAD_REQUEST);
	else return $id;
}

function groupby($tab, $id){
    // Regourpe tout les tableaux associatifs de tab avec une même valeur de clé
    $res = array();
    // on parcours le tableau
    foreach($tab as $tabData) {
        // si on a aucun tableau à la clé id
        if(!isset($res[$tabData[$id]])){
            // on en crée un
            $res[$tabData[$id]] = array();
        }
        // on ajoute dans ce tableau l'enregistrement avec le même id
		$key = $tabData[$id];
		unset($tabData[$id]);
        array_push($res[$key], $tabData);
    }

    return $res;
}

/**
 * Fonction qui convertie une image sous format base64 en un fichier image
 * @param string $base64 l'image sous format base64
 * @param string $output le chemins de l'image
 */
function base64ToImage($base64, $output, $maxSize) {
    $data = base64_decode($base64);
	// On utilise la fonction strlen pour vérifier la taille des données décodées
	if(strlen($data) > $maxSize) {
		return false;
	}

    // On utilise la fonction finfo_buffer pour vérifier le type MIME des données décodées
    $finfo = finfo_open();
    $mimeType = finfo_buffer($finfo, $data, FILEINFO_MIME_TYPE);
	$extension = finfo_buffer($finfo, $data, FILEINFO_EXTENSION);
    finfo_close($finfo);
    // On vérifie si le type MIME est celui d'une image
    if (strpos($mimeType, 'image') === 0) {
		// on récupère l'extension approprié de l'image 
        $output = str_replace(pathinfo($output, PATHINFO_EXTENSION), $extension, $output);

        // On utilise la fonction file_put_contents pour écrire les données décodées dans un fichier
        file_put_contents($output, $data);
        return $output;
    } else {
        return false;
    }
}

function uploadImage($filename, $imageData) {
    // RETOURNE : 1 si succès
    // CODE ERREUR :
    // -1 type de fichier pas bon
    // -2 taile du fichier pas bon
    // -3 extension du fichier pas correct
    // -4 erreur survenu lors de l'upload
    
    $imageFileType = strtolower(pathinfo($imageData["name"],PATHINFO_EXTENSION)); // extension du fichier
    $file = $filename . ".$imageFileType"; // le chemin du fichier
    $finfo = finfo_open();
	$mime_type = finfo_file($finfo, $imageData["tmp_name"], FILEINFO_MIME_TYPE);
	finfo_close($finfo);
    $returnInfo["filename"] = $filename . ".$imageFileType";

    // Vérification du type : on ne voudrais pas que l'utilisateur upload un fichier php par exemple
    if(strpos($mime_type, 'image') !== 0) {
        $returnInfo["code"] = IMAGE_ERR_NOTIMAGE;
		$returnInfo["message"] = "Le fichier n'est pas une image !";
    }
    // Vérification de la taille
    if ($imageData["size"] > IMAGE_MAXSIZE) {// on récupère l'info de la taille dans la superglobale $_FILES
        $returnInfo["code"] = IMAGE_ERR_SIZE;
		$returnInfo["message"] = "La taille de l'image et invalide";
    }
    // Vérification de l'extension
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif" ) {
        $returnInfo["code"] = IMAGE_ERR_BADEXTENSION;
		$returnInfo["message"] = "L'extension du fichier est invalide !";
    }

    // Tout est bon
    if (move_uploaded_file($imageData["tmp_name"], $file)) {
        $returnInfo["code"] = 1;
		$returnInfo["message"] = "image OK !";

    }
    else {
        $returnInfo["code"] = IMAGE_ERR_UPLOAD;
		$returnInfo["message"] = "Échec de l'upload";
	}
	return $returnInfo;
}

/**
 * Retourne la chaine si la taille de la chaine est comprise entre 2 valeurs
 * @param string $string
 * @param int $max taille maximale de la chaine
 * @param int $min taille minimale de la chaine
 * @param string|false renvoie la chaine ou false
 */
function validString($string, $max, $min = 0) {
	//TODO : vérifier si $string est une chaine
	$lenght = strlen($string); 
	if($lenght <= $max && $lenght >= $min) return $string;
	
	return false;
}

/**
 * Retourne True si la chaine passé en paramètre est une adresse email
 * @param string $string
 * @return string|false retourne la chaine ou false si ce n'est pas une adresse email 
 */
function isEmail($string) {
	return filter_var($string, FILTER_VALIDATE_EMAIL);
}
/**
 * Retourne la racine des ressources du site
 */
function getBaseLink() {
	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";   
	$url.= $_SERVER['HTTP_HOST'] . DIR_RESSOURCES;
	return $url;
}

/**
 * Retourne la chaine si la chaine est est un numéro de téléphone
 * @param string $string
 * @return string|false
 */
function isPhoneNumber($string) {
	if(preg_match("/^[0-9]*$/", $string)) {
		return validString($string, 10, 10);
	}

	return false;
}

/**
 * Retourne le sexe de l'utilisateur si le sexe est bien compris entre {0,1,2}
 */
function isSex($sex) {
	$possible = [0,1,2];
	if(array_search($sex,$possible, true) !== false) return $sex;
	return false;
}

/**
 * Permet de rechercher un role spécifique
 * @param string $role role à rechercher
 * @param array $roleData tableau des role récupérer par la fonction selectUserRoles
 * @return int 
 */
function searchRole($role,$roleData) {
	return (array_search($role, array_column($roleData, "label")) !== false) ? 1 : 0;
}
?>
