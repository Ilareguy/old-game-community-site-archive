<?php

class _BDDCONNECT{
    
    public static $initd = false;
    public static $bdd;
    
    public static function init(){
        
        try{
            self::$bdd = new PDO('mysql:host=127.0.0.1;dbname=jordan_diamond_old', 'jordan_jordan', 'Y1OfYEZ2');
        }catch (Exception $e){
            die('Erreur : ' . $e->getMessage());
        }
        
        function PHPShutdown(){
            // Affectation des transactions avec MySQL:
            _BDDCONNECT::$bdd->commit();
        }
        
        // Démarrage d'une transaction avec MySQL:
        self::$bdd->beginTransaction();
        register_shutdown_function("PHPShutdown");
        
        // Initialisation terminée:
        self::$initd = true;
        
    }
    
}

?>
