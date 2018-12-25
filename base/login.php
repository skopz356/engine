<?php 
    foreach (glob("engine/base/*.php") as $filename)
    {
        include_once $filename;
    }

    require_once($_SERVER['DOCUMENT_ROOT']."/conn.php");
    Class Login extends DatabaseMixin{
        public static $conn;
        function __construct(){

        }

        public static function login($name, $password){
            self::setConnection();
            $name = mysqli_real_escape_string(self::$conn,$name);
            $password = hash('sha256', mysqli_real_escape_string(self::$conn, $password));
            $sql = "SELECT * FROM users WHERE name=? AND password=?";
            if($stmt = self::$conn->prepare($sql)){
                $stmt->bind_param('ss', $name, $password);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows === 0){
                    echo "Bad";
                }else{
                    self::setLogin();
                    echo "Succ";
                }
                $stmt->close();
            }else{
                $error = self::$conn->errno . ' ' . self::$conn->error;
                echo $error; // 1054 Unknown column 'foo' in 'field list'
            }
        }

        public static function logout(){
            session_start();
            if(isset($_SESSION["login"])){
                unset($_SESSION["login"]);
            }
            header("Location: http://$_SERVER[HTTP_HOST]");
        }

        public static function setLogin(){
            session_start();
            $_SESSION["login"] = TRUE;
            
        }

        private function setConnection(){
            require($_SERVER['DOCUMENT_ROOT']."/conn.php");
            self::$conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        }

        public static function createUser($name, $password){
            self::setConnection();
            $name = mysqli_real_escape_string(self::$conn,$name);
            $password = hash('sha256', mysqli_real_escape_string(self::$conn, $password));
            $sql = "INSERT INTO users(name, password) VALUES(?, ?)";
            if($stmt = self::$conn->prepare($sql)){
                $stmt->bind_param('ss', $name, $password);
                $stmt->execute();
                $stmt->close();
            }else{
                $error = self::$conn->errno . ' ' . self::$conn->error;
                echo $error; // 1054 Unknown column 'foo' in 'field list'
            }
        }

        public static function isLogin(){
            if(session_status()!=PHP_SESSION_ACTIVE) session_start();
            if(isset($_SESSION["login"])){
                if($_SESSION["login"] === TRUE){
                    return TRUE;
                }
            }else{
                return FALSE;
            }
        }
        


    }


?>