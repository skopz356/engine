<?php 
session_start();
if (isset($_SESSION["login"])){
    if ($_SESSION["login"] == TRUE){
        if(isset($_POST["path"])){
            unlink($_SERVER['DOCUMENT_ROOT'].$_POST["path"]);
            echo "Succ";

        }
    }
}
?>