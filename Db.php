<?php
class Db {
    private static $conn = null;
    public static function getConnection(){
        if(self :: $conn ==null){
            
            
            self::$conn = new PDO ('mysql:host=localhost;dbname=webshop1', 'root', '',);
            return self :: $conn;
        }
        else{
           
            return self::$conn;
        }
    }
}



