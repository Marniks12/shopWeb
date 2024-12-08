<?php
class Db {
    private static $conn = null;
    public static function getConnection(){
        if(self :: $conn ==null){
            
            
<<<<<<< HEAD
            self::$conn = new PDO ('mysql:host=localhost;dbname=webshop1', 'root', '',);
=======
            self::$conn = new PDO ('mysql:host=autorack.proxy.rlwy.net;dbname=railway', 'root', 'PLbHxNbRwwWziDDYpeBrWBnmDhbhsOGZ');
>>>>>>> 6f492e7436267b068b80a7f134e8a0effc97306c
            return self :: $conn;
        }
        else{
           
            return self::$conn;
        }
    }
}



