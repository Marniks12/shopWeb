<?php
class Db {
    private static $conn = null;
    public static function getConnection(){
        if(self :: $conn ==null){
            
            
            self::$conn = new PDO ('mysql:host=autorack.proxy.rlwy.net;dbname=railway', 'root', 'PLbHxNbRwwWziDDYpeBrWBnmDhbhsOGZ');
            return self :: $conn;
        }
        else{
           
            return self::$conn;
        }
    }
}


