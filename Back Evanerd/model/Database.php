<?php

/*
Objet representant une base de donnée mySQL
*/
class Database {

    private $dbh; // Attribut de connexion à la base de donnée



    // Constructeur de notre objet Database qui initialise une connexion à la base
    // En cas d'erreur de connexion à la base de donnée : le script s'arrête avec le message d'erreur PDO 
    public function __construct($BDD_base, $BDD_user, $BDD_password, $BDD_host = "localhost") {
        
        // On tente une connexion
        try {
            // Connection à la base de donnée, en indiquant que la connection est persistante
            $this->dbh = new PDO("mysql:host=$BDD_host;dbname=$BDD_base", $BDD_user, $BDD_password, [PDO::ATTR_PERSISTENT => true]);
        } 
        // Cas ou il y a une erreur
        catch (PDOException $e) {
            die("<font color=\"red\">initDB : Erreur de connexion : " . $e->getMessage() . "</font>");
        }

        //$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
        // Lors d'un update qui ne change pas l'enregistrement, il n'est pas compté par rowcount, de ce fait on l'active
        $this->dbh->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
        // Lors d'une erreur SQL, PDO/PDOStatement renvoie un PDOException (pratique pour capturer les erreurs)
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->dbh->exec("SET CHARACTER SET utf8");
        
    }

    /**
    * Méthode privé (accessible que dans la définition de la classe) qui exécute une requête préparée
    * @return PDOStatement|false
    * @param string $query la requete SQL préparée ou non
    * @param array $params les paramètres passés à la requete préparée
    * Note : En cas d'erreur, la méthode retourne false
    */
    private function query($query, $params = []) {
        $res = $this->dbh->prepare($query); // on prépare la requête 
        $res->execute($params); // si tout va bien on execute la requête
        return $res; // retourne PDOStatement ou false si pb lors de l'éxecution
    }


    /**
    * Exécuter une requête UPDATE. Renvoie le nb de modifs ou faux si pb
    * On testera donc avec === pour différencier faux de 0 
    * @return le nombre d'enregistrements affectés, ou false si pb...
    * @param string $query la requete SQL préparée ou non
    * @param string $params les paramètres passés à la requete préparée
    */
    public function SQLUpdate($query, $params = []) {
        try {
            $res = $this->query($query, $params);
        }catch(PDOException $e) {  
		    die("<font color=\"red\">SQLUpdate/Delete: Erreur de requete : " . $e->getMessage() . "</font>");
	    }
    	$nb = $res->rowCount(); // on compte le nombre de lignes affectés par la requete 

        if ($nb != 0) return $nb; // on le retourne si différent de 0
	    else return false;
    }

    // Un delete c'est comme un Update
    public function SQLDelete($query, $params = []) {
        return $this->SQLUpdate($query, $params);
    }

    /**
    * Exécuter une requête INSERT 
    * @param string $query la requete SQL préparée ou non
    * @param string $params les paramètres passés à la requete préparé
    * @return Renvoie l'insert ID ... utile quand c'est un numéro auto
    */
    function SQLInsert($query, $params) {
        try {
            $this->query($query, $params);
        }catch(PDOException $e) {
		    die("<font color=\"red\">SQLInsert: Erreur de requete : " . $e->getMessage() . "</font>");
	    }
        
        $lastInsertId = $this->dbh->lastInsertId(); 
	    return $lastInsertId;
    }

    /**
    * Effectue une requete SELECT dans une base de données SQL SERVER, pour récupérer uniquement un champ (la requete ne doit donc porter que sur une valeur)
    * Renvoie FALSE si pas de resultats, ou la valeur du champ sinon
    * @param string $query la requete SQL préparés ou non
    * @param string $params les paramètres passés à la requete préparé
    * @return false|string
    */
    function SQLGetChamp($query, $params = []) {
        try {
            $res = $this->query($query, $params);
        }catch(PDOException $e) {
		    die("<font color=\"red\">SQLGetChamp: Erreur de requete : " . $e->getMessage() . "</font>");
	    }

	    $num = $res->rowCount();
        if ($num==0) return false;
        
        $res->setFetchMode(PDO::FETCH_NUM);
        $ligne = $res->fetch();

        if ($ligne == false) return false;
	    
        else return $ligne[0];

    }

    /**
     * Effectue une requete SELECT dans une base de données SQL SERVER
     * Renvoie FALSE si pas de resultats, ou la ressource sinon
     * @pre Les variables  $BDD_login, $BDD_password $BDD_chaine doivent exister
     * @param string $query la requete SQL préparée ou non
     * @param string $params les paramètres passés à la requete préparé
     * @return boolean|PDOStatement
     */
    function SQLSelect($query, $params = []) {	
        try {
            $res = $this->query($query, $params);
        }catch(PDOException $e) { 
		    die("<font color=\"red\">SQLSelect: Erreur de requete : " . $e->getMessage() . "</font>");
	    }
	
	    $num = $res->rowCount();

	    if ($num==0) return false;
	    else return $res;
    }


    /**
    * Méthode statique (pour l'appeler Database::parcoursRS($result)) qui 
    * Parcours les enregistrements d'un résultat mysql et les renvoie sous forme de tableau associatif
    * On peut ensuite l'afficher avec la fonction print_r, ou le parcourir avec foreach
    * @param resultat_Mysql $result
    */
    static function parcoursRs($result)
    {
	    if  ($result == false) return array();

	    $result->setFetchMode(PDO::FETCH_ASSOC);
	    while ($ligne = $result->fetch()) 
		    $tab[]= $ligne;

	    return $tab;
    }

    /** 
     * Méthode statique qui
     * Renvoie les enregistrements d'un resultat mysql sous forme d'un objet JSON
     * @param resultat_Mysql $result
     * @return string une chaine representant un objet json
     *  
    **/
    static function toJSON($result) {
        return json_encode(self::parcoursRs($result, JSON_FORCE_OBJECT)); // On force l'objet json dans le cas d'un tableau vide
    }
}
?>