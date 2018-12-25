<?php 
session_start();
if (isset($_SESSION["login"])){
    if ($_SESSION["login"] == TRUE){
        if(isset($_POST["path"])){
            unlink($_SERVER['DOCUMENT_ROOT'].$_POST["path"]);
            echo "Succ";

        }
        if(isset($_POST["id"])){
            require($_SERVER['DOCUMENT_ROOT']."/conn.php");
            $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
            $id = $_POST["id"] + 1;
            $sql = "DELETE FROM news WHERE id=$id";
            if($conn->query($sql) === TRUE){
                echo "Succ";

            }
        }
    }
}
?>