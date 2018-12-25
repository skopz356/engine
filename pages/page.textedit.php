<?php

require_once("engine/base/login.php");
require_once("pages/abstract.php");
require_once("engine/base/login.php");


    Class Textedit extends BasePage{
        private $url = "textedit";


        public static function setHandlers(){
            $handlers_array = array(
                'post/textedit' => function(){
                    if(Login::isLogin()){
                        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                        $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                        $page = $_POST["page"];
                        if($page == "home"){
                            $page = "/";
                        }
                        $sql = "UPDATE content SET ".$_POST["type"]."="."'".$_POST["editarea"]."'"." WHERE (page_order=".$_POST["id"].") AND (page_id=(SELECT id FROM page WHERE url='$page'))";
                        if ($conn->query($sql) === TRUE) {
                            echo "Succ";
                        } else {
                            echo "Error updating record: " . $conn->error;
                            echo $sql;
                        }
                    }
                }
            );

            return $handlers_array;
        }
    }


    PageList::register('Textedit');

?>