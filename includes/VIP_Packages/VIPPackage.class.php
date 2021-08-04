<?php

if(!defined('__ROOT__'))
    define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
ini_set("maximum_execution_time", 180);

class VIPPackage{
    
    protected $pID = 0;
    protected $pIsLoaded = false;
    protected $pDescription = "";
    protected $pName = "";
    protected $pPrice = 0.0;
    
    public function IsLoaded(){
        return $this->pIsLoaded;
    }
    public function ID(){
        return $this->pID;
    }
    
    public function VIPPackage($ID = 0){
        if($ID > 0)
            self::Load($ID);
    }
    
    public function Name(){
        return $this->pName;
    }
    
    public function Description(){
        return $this->pDescription;
    }
    
    public function Price(){
        return $this->pPrice;
    }
    
    protected function reset(){
        $this->pIsLoaded = false;
        $this->pID = 0;
        $this->pName = "";
        $this->pDescription = "";
        $this->pPrice = 0.0;
    }
    
    public function Load($ID){
        /*
        */
        include(__ROOT__.'includes/bddConnect.php');
        
        $req = $bdd->prepare('SELECT * FROM vip_packages WHERE ID=?');
        $req->execute(array($ID));
        
        if($donnees = $req->fetch()){
            
            $pIsLoaded = true;
            $this->pID = $ID;
            $this->pDescription = $donnees['Description'];
            $this->pName = $donnees['Name'];
            $this->pPrice = $donnees['Price'];
            
        }else{
            reset();
        }
        
    }
    
    public static function DynamicNew($ID){
        $ClassName = 'VIPPackage_' . $ID;
        $ClassFilename = __ROOT__.'includes/VIP_Packages/VIPPackage.' . $ID . '.class.php';
        if(file_exists($ClassFilename)){
            require_once($ClassFilename);
            if(class_exists($ClassName))
                return new $ClassName($ID);
        }
        
        return new VIPPackage($ID);
    }
    
    public function GetPhoto(){
        /*
        */
        if(file_exists("images/VIP_Packages/" . $this->pID . ".png")){
            return "images/VIP_Packages/" . $this->pID . ".png";
        }else if(file_exists("images/VIP_Packages/" . $this->pID . ".jpg")){
            return "images/VIP_Packages/" . $this->pID . ".jpg";
        }else if(file_exists("images/VIP_Packages/" . $this->pID . ".jpeg")){
            return "images/VIP_Packages/" . $this->pID . ".jpeg";
        }else if(file_exists("images/VIP_Packages/" . $this->pID . ".gif")){
            return "images/VIP_Packages/" . $this->pID . ".gif";
        }
        return "images/VIP_Packages/0.png";
    }
    
    public function Apply($IDCompte){
        /*
        * C'est dans cette fonction que tout se produit.
        */
    }
    
    protected static function RegisterPlayerAsVIP($IDCompte, $IDPackage, $DurationSeconds){
        /**
        * Cette fonction entre dans la BD le nouveau package appliqué au joueur donné
        */
        include(__ROOT__.'includes/bddConnect.php');
        
        $req = $bdd->prepare('INSERT INTO vip_active_packages (IDCompte, IDPackage, TimestampEnd) VALUES (:IDCompte, :IDPackage, :TimestampEnd)');
        $req->execute(array(
            ':IDCompte' => $IDCompte,
            ':IDPackage' => $IDPackage,
            ':TimestampEnd' => (time() + $DurationSeconds)
        ));
        
    }
    
}

?>