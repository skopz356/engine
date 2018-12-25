<?php
    require_once("abstract.php");
    
    use claviska\SimpleImage;
    class Image extends DatabaseMixin{
        private $url;

        function __construct(){
            parent::__construct();
        }





        public static function removeImage($path){
            if(Login::isLogin()){
                unlink($path);
            }

        }
    }
?>