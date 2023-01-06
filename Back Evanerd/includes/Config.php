<?php

include_once  "model/Database.php";

/*
Cette classe statique permet de pouvoir recupérer la base de donnée dans les différents fichiers php sans devoir à chaque fois déclarer une instance ou utiliser
une variable globale
*/
class Config {
    private static $BDD_host="localhost"; // Host de la base de donnée (généralement localhost)
    private static $BDD_user="root"; // Nom de l'utilisateur de la base de donnée
    private static $BDD_password=""; // Mot de passe du compte utilisateur
    private static $BDD_base=""; // nom de la base de donnée 
    private static $db = null; // attribut contenant l'instance de la base de donnée une fois initialisé
    


    /**
     * Méthode statique qui permet de récupérer l'instance de la base de donnée avec toutes les méthodes CRUD 
     */
    static function getDatabase() {
        if(!self::$db){
            self::$db = new Database(self::$BDD_base, self::$BDD_user, self::$BDD_password, self::$BDD_host);
        }
        
        return self::$db; 
    }
}

?>