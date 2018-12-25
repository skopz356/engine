<?php
abstract Class DatabaseMixin{
    function __construct(){
        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
        $this->conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        self::$conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
    }

}




?>
            