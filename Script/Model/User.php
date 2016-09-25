<?php
require_once('./Database.php');
class User
{
    public $ID;
    public $Username;
    public $Hash;
    public $PublicKey;
    
    private $Database;
    
    function __construct($ID = 0)
    {
        $this->Database = new Database;
        
        if(intval($ID))
        {
            $this->loadData($ID);
        }
        else
        {
            //New User set default values
        }
    }
    public function apply($result)
    {
        $this -> ID = $result['ID'];
        $this -> Username = $result['Username'];
        $this -> Hash = $result['Hash'];
        $this -> PublicKey = $result['PublicKey'];
    }
    private function loadData($ID)
    {
        $query =" SELECT * FROM Users WHERE ID=".intval($ID);
        $stmt = $this->Database->prepare($query); 
        $stmt->execute(); 
        $result = $stmt->fetch();
        
        //print "result = ".json_encode($result)."";
        
        if(!intval($result["ID"])) return;
        
        $this->apply($result);
    }
    public function save()
    {
        $query = "INSERT INTO Users ".
                "(
                    ID,
                    Username,
                    Hash,
                    PublicKey
                )
                VALUES
                (
                    :ID,
                    :Username,
                    :Hash,
                    :PublicKey
                ) 
                 ON DUPLICATE KEY UPDATE 
                    Hash=:Hash,
                    PublicKey=:PublicKey
                ";
        $stmt = $this -> Database ->prepare($query);
        $stmt->bindParam(':ID', intval($this->ID));
        $stmt->bindParam(':Username', $this->Username);
        $stmt->bindParam(':Hash', $this->Hash);
        $stmt->bindParam(':PublicKey', $this->PublicKey);
        $this->Database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt->execute();
        
        $this -> $Database ->lastInsertId(); // Need this to get the ID of a new User needs to be fixed to only do it when its a new user
    }
}
?>