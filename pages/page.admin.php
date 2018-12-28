<?php 
    require_once("pages/page.main.php");
    require_once("pages/abstract.php");
    require_once("engine/base/routes.php");
    require_once("vendor/autoload.php");
    require_once("engine/base/login.php");
    use Pecee\SimpleRouter\SimpleRouter;

    class Admin extends BasePage{
        private  $handlers = array();
        private  $url = "admin";

        function _construct(){
            $this->setHandlers();
        }

         public static  function setHandlers(): array{

            $handlers_array = array(
                'form/admin' => function(){
                    if(SimpleRouter::request()->getMethod() === "post"){
                        $username = SimpleRouter::request()->getInputHandler()->value("username");
                        $password = SimpleRouter::request()->getInputHandler()->value("password");
                        Login::login($username, $password);
                    }elseif(Login::isLogin()){
                        Route::renderPage(Route::getTemplateName("adminlogin"));
                    }else{
                        Route::renderPage(Route::getTemplateName(Route::getPageName()));
                    }
                },
                'post/add-page' => function(){
                    require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                    $name = SimpleRouter::request()->getInputHandler()->value("page_name");
                    $url = SimpleRouter::request()->getInputHandler()->value("page_url");
                    $parent_id = SimpleRouter::request()->getInputHandler()->value("parent_id");
                    $count = getColumnCount("page")+1;
                    
                    if($parent_id === "null"){
                        $parent_id = "null";
                        $sql = "INSERT INTO page(name, url, position) VALUES ('$name', '$url',  $count)";
                    }else{
                        $parent_id = (int) $parent_id;
                        $sql = "INSERT INTO page(name, url, parent_id, position) VALUES ('$name', '$url', $parent_id, $count)";
                    }
                    if ($conn->query($sql) === TRUE) {
                        echo "Succ";
                    }
                    else {
                        echo $sql;
                        echo "Error updating record: " . $conn->error;
                    }
                    $conn->close();
                },
                'post/add-content' => function(){
                    require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                    $name = $_POST["page_heading"];
                    $url = $_POST["page_text"];
                    $page_id = $_POST["page_id"];
                    $last_id =  getLastId($conn->query("SELECT url FROM page WHERE id=$page_id")->fetch_object()->url)+1; 
                    $sql = "INSERT INTO content(heading, text, page_id, page_order) VALUES ('$name', '$url', (SELECT id FROM page WHERE id=$page_id), $last_id)";
                    if ($conn->query($sql) === TRUE) {
                        echo "Succ";
                    }
                    else {
                        echo "Error updating record: " . $conn->error;
                    }
                    
                    $conn->close();
                },
                'post/position' => function(){
                    require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                    $position = SimpleRouter::request()->getInputHandler()->value("position");
                    $id = SimpleRouter::request()->getInputHandler()->value("id");
                    $visible = SimpleRouter::request()->getInputHandler()->value("visible");
                    if(!isset($visible)){
                        $visible = 0;
                    }
                    $sql = "UPDATE page SET position='$position', visible=$visible WHERE id=$id";
                    if ($conn->query($sql) === TRUE) {
                        echo "Succ";
                    }
                    else {
                        echo $sql;
                        echo "Error updating record: " . $conn->error;
                    }
                    $conn->close();
                },
                'post/delete_page' => function(){
                    require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                    $id = SimpleRouter::request()->getInputHandler()->value("id");
                    $sql = "DELETE FROM page WHERE id=$id";
                    if ($conn->query($sql) === TRUE) {
                        echo "Succ";
                    }
                    else {
                        echo $sql;
                        echo "Error updating record: " . $conn->error;
                    }
                    $conn->close();
                },
            );
            return $handlers_array;
        }
        
    }

    PageList::register('Admin');


?>