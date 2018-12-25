
<?php
    function getArrayDatabase($from,array $what=array("id", "name")){
        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
        $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        $whatF = "";
        $i = 0;
        foreach($what as $param){
            $whatF .= (($i === 0)?"":",")." ".$param;
            $i++;
        }
        $sql = "SELECT $whatF FROM $from";
        $r = $conn->query($sql);
        $result = array();
        $x = 0;
        while($row =$r->fetch_assoc() ){
            $result[$x]["id"] = $row["id"];
            $result[$x]["name"] = $row["name"];
            $x++;
        }
        $conn->close();
        return $result;
    }

    function getColumnCount($from){
        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
        $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        return $conn->query("SELECT COUNT(*) AS total FROM $from")->fetch_object()->total;
    }

    function getLastId($url){
        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
        $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        $sql = "SELECT page_order FROM content WHERE page_id=(SELECT id FROM page WHERE url='$url')ORDER BY id DESC LIMIT 0, 1";
        return $conn->query($sql)->fetch_object()->page_order; 
      }

?>