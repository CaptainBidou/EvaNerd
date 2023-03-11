<?php

// TODO : Ajouter des couleurs 

enum COLOR:string{
    case LIGHT_PURPLE = "#aa47bc";
    case PURPLE = "#7a1fa2";
    case LIGHT_RED = "#ec407a";
    case RED = "#c2175b";
    case DARK_RED = "#9D0800";
    case LIGHT_BLUE = "#0288d1";
    case BLUE = "#0039a3";
    case ORANGE = "#f5511e";
    case LIGHTGREEN ="#95ff15";
    case GREEN = "#228b22";
    case PINK = "#fc6c9f";

}

/**
 * Convertit en valeurs RGB une chaine de couleur hexadecimale
 * @param string $hex couleurs hexa
 * @param int &$r Composante rouge passé par référence
 * @param int &$g Composante verte passé par référence 
 * @param int &$b Composante bleue passé par référence
 * @return void
 */
function hexToRGB($hex, &$r, &$g, &$b) {
    preg_match("/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i", $hex, $matches);
    $r = hexdec($matches[1]);
    $g = hexdec($matches[2]);
    $b = hexdec($matches[3]);
}

/**
 * Génère un objet GD à partir d'un fichier image
 * @param string $filename chemin du fichier image
 * @return GdImage|false
 */
function genGDObject($filename) {
    $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch($type) {
        case "bmp":
            return imagecreatefrombmp($filename);
        case "gif":
            return imagecreatefromgif($filename);
        case "jpg":
            return imagecreatefromjpeg($filename);
        case "png":
            return imagecreatefrompng($filename);
        default:
            return false;
    }
}

/**
 * Fonction retournant les info d'un texte à incruster en fonction 
 * de la taille de l'image cible
 * @param int $imgL largeur de l'image cible
 * @param int $imgH hauteur de l'image cible
 * @param string $text texte à écrire sur l'image
 * @param string $font la police utilisé
 * @param $fontSizeMax taille de police maximum sur l'image cible
 * @param $step degrée de diminution de la police (doit être multiple de $fontSizeMax) 
 */
function getTexteInfo($imgL, $imgH, $text, $font, $fontSizeMax = 180, $step = 5) {
	$fontSize = $fontSizeMax;
	$textBox = imagettfbbox($fontSizeMax, 0, $font, $text);
	$textWidth = abs($textBox[4] - $textBox[0]);
	$textHeight= abs($textBox[5] - $textBox[1]);
	
	while( $textWidth > $imgL || $textHeight > $imgH) {
		// on abaisse la police d'écriture
		$fontSize = $fontSize-5;
		// on recalcule la taille
		$textBox = imagettfbbox($fontSize, 0, $font, $text);
		$textWidth = abs($textBox[4] - $textBox[0]);
		$textHeight= abs($textBox[5] - $textBox[1]);
	}

	return ["fontSize" => $fontSize, "height" => $textHeight, "width" => $textWidth];

}

/**
 * Génere la photo de profile par défaut d'un utilisateur sous format png
 * @param string $output fichier de sortie de l'image
 * @param string $firstname prénom de l'utilisateur
 * @param string $lastName nom de famille de l'utilisateur
 * @return void
 */
function defaultPicture($output, $firstName, $lastName, $font= "arial.ttf") {
    // Tirage d'une couleur aléatoire
    $bgColor = array_column(COLOR::cases(), "value");
    $bgHex = $bgColor[array_rand($bgColor)];
    hexToRGB($bgHex, $r, $v, $b);

    // Créer une nouvelle image de 450x450 pixels
    $image = imagecreate(450, 450);

    // Définir la couleur de fond en violet
    $bg = imagecolorallocate($image, $r, $v, $b);

    // Remplir l'image avec la couleur de fond
    imagefill($image, 0, 0, $bg);

    // Définir la couleur du texte en blanc
    $textColor = imagecolorallocate($image, 255, 255, 255);
    // Définir la couleur du contour en noir
    $borderColor = imagecolorallocate($image, 0, 0, 0);
    // Définir la police à utiliser pour le texte
    putenv('GDFONTPATH=' . realpath('./fonts'));
	$font = realpath("./fonts/$font");
    // Obtenir les initiales du nom et prénom
    $initials = strtoupper($firstName[0] . $lastName[0]);
    // Calcul taille du texte
    $textInfo = getTexteInfo(400,400, $initials, $font, 200, 1);
    $textHeight = $textInfo["height"];
    $textWidth = $textInfo["width"];
    $fontSize = $textInfo["fontSize"];
    // Ajouter le text à l'image
    imagettftext($image, $fontSize, 0, (450/2 - ((int) $textWidth/2)) + ((int) $textWidth*0.05), 450/2 + ((int) $textHeight/2), $borderColor, $font, $initials);
    imagettftext($image, $fontSize, 0, 450/2 - ((int) $textWidth/2), 450/2 + ((int) $textHeight/2), $textColor, $font, $initials);
    // Ajouter un contour autour du texte
    // Enregistrer l'image
    imagepng($image, $output);
    // Libérer la mémoire utilisée par l'image
    imagedestroy($image);
}
/**
 * Ajoute un bandeau rouge à l'image passé en paramètre
 * @param string $output chemin de sortie de l'image
 * @param string $image l'image à modifier
 * @return void
 */
function caPicture($output, $image, $thickness = 10) {
    $im = genGDObject($image);
    $width  = imagesx($im);
    $height = imagesy($im);
    $darkRed = imagecolorallocate($im, 139,0,0);
    imagefilledrectangle($im, 0, 0, $thickness, $height, $darkRed); // bordure gauche
    imagefilledrectangle($im, $width-$thickness, 0, $width, $height, $darkRed); // bordure droite
    imagefilledrectangle($im, 0, 0, $width, $thickness, $darkRed); // bordure haute
    imagefilledrectangle($im, 0, $height-$thickness, $width, $height, $darkRed); // bordure basse
    imagepng($im, $output);
    imagedestroy($im);
}
?>
