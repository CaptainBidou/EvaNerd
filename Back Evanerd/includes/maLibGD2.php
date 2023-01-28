<?php

// TODO : Ajouter des couleurs 

enum COLOR:string{
    case LIGHT_PURPLE = "#aa47bc";
    case PURPLE = "#7a1fa2";
    case LIGHT_RED = "#ec407a";
    case RED = "#c2175b";
    case LIGHT_BLUE = "#0288d1";
    case ORANGE = "#f5511e";
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
 * Génere la photo de profile par défaut d'un utilisateur sous format png
 * @param string $output fichier de sortie de l'image
 * @param string $firstname prénom de l'utilisateur
 * @param string $lastName nom de famille de l'utilisateur
 * @return void
 */
function defaultPicture($ouput, $firstName, $lastName) {

}

?>