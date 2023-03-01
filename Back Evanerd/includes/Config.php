<?php

include_once  "model/Database.php";
include_once "includes/Email.php";

/*
Cette classe statique permet de pouvoir recupérer la base de donnée dans les différents fichiers php sans devoir à chaque fois déclarer une instance ou utiliser
une variable globale
*/
class Config {

    // DATABASE CONFIG
    private static $BDD_host="localhost"; // Host de la base de donnée (généralement localhost)
    private static $BDD_user="root"; // Nom de l'utilisateur de la base de donnée
    private static $BDD_password=""; // Mot de passe du compte utilisateur
    private static $BDD_base="evanerd"; // nom de la base de donnée 
    private static $db = null; // attribut contenant l'instance de la base de donnée une fois initialisé
    
    // SMTP CONFIG
    private static $SMTP_HOST = "mail.evanerds.fr"; // Specify main SMTP server
    private static $SMTP_USERNAME = "lukas"; 
    private static $SMTP_PASSWORD = "PASSWORD HERE";
    private static $SMTP_SENDER = "lukas@evanerds.fr";
    private static $SMTP_PORT = 587;
    private static $email = null;

    /**
     * Méthode statique qui permet de récupérer l'instance de la base de donnée avec toutes les méthodes CRUD 
     */
    static function getDatabase() {
        if(!self::$db){
            self::$db = new Database(self::$BDD_base, self::$BDD_user, self::$BDD_password, self::$BDD_host);
        }
        
        return self::$db; 
    }

    static function getEmail() {
        if(!self::$email) {
            self::$email = new Email(self::$SMTP_HOST, self::$SMTP_USERNAME, self::$SMTP_PASSWORD, self::$SMTP_PORT, self::$SMTP_SENDER);
        }

        return self::$email;
    }
}

?>