<?php 



    Class Compiler extends BasePage{
        private $url = "textedit";


        public static function setHandlers(){
            $jsfodler = $_SERVER['DOCUMENT_ROOT']."/static/js/";
            $fileList = glob($jsfodler.'*.js', GLOB_BRACE);
            $content = "";
            foreach($fileList as $file){
                $content .= file_get_contents($file);
            }
            $handlers_array = array(
                'get/static/js/all.js' => function() use ($content){
                    header('Content-Type: application/javascript');
                    return $content;
                }
            );

            return $handlers_array;
        }
    }


    PageList::register('Compiler');

    
?>