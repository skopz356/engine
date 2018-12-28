<?php
use Pecee\SimpleRouter\SimpleRouter;

require_once "vendor/autoload.php";
require_once "login.php";
require_once "pages/page.main.php";
include $_SERVER['DOCUMENT_ROOT'] . "/obsah.php";

//BECAUSE PHP SUCKS
$GLOBALS["HOME_URL"] = $HOME_URL;
$GLOBALS["debug"] = $debug;
$GLOBALS["head"] = $head;
$GLOBALS["admin_form"] = $admin_form;

class Route
{
    private static $conn;
    private $router;
    private $list_routes = array();
    private const tem_file = "templates/template.";

    public function __construct()
    {
        require $_SERVER['DOCUMENT_ROOT'] . "/conn.php";
        //$this->conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        self::$conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n" . $conn->error);
        $this->custom_pages = PageList::getPages();
    }

    private function array_push_assoc($array, $key, $value)
    {
        $array[$key] = $value;
        return $array;
    }

    private static function renderMenu()
    {
        $sql = "SELECT name, url FROM page WHERE visible=1 ORDER BY position ASC";
        $result = self::$conn->query($sql);
        echo "<nav>" . "\xA";
        echo '<button class="hamburger">Menu</button>' . "\xA";
        echo '<a href="./" class="logo"><div class="logo-img"></div></a>' . "\xA";
        echo '<ul id="menu">' . "\xA";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $x = getPageName();
                if ($x == "home") {
                    $x = "./";
                }
                $d = $row["url"];
                $i = $row["url"];
                if (substr($i, 0, 1) === "#") {
                    $i = substr($i, 1);
                }
                echo '<li><a id="' . $i . '-link" href="' . $d . '"' . (($row["url"] == $x) ? ' class="selected"' : "") . '>' . $row["name"] . '</a></li>' . "\xA";

            }
            if (Login::isLogin()) {
                echo '<li><a href="odhlasit">Odhlasit</a></a></li>';
            }
        }
        echo "</ul>" . "\xA";
        echo "</nav>" . "\xA";
    }

    public function renderView()
    {
        $sql = "SELECT url, parent_id FROM page";
        $result = self::$conn->query($sql);
        $required = false;
        while ($row = $result->fetch_assoc()) {
            if ($row["parent_id"] === null) {
                SimpleRouter::get($row['url'], function () use ($row) {
                    self::renderPage(self::getTemplateName($row["url"]));
                });
                $this->list_routes = $this->array_push_assoc($this->list_routes, $row['url'], 'get');
            } else {
                $parent_name = self::$conn->query("SELECT url FROM page WHERE id=" . $row["parent_id"])->fetch_object()->url;
                if ($parent_name === $GLOBALS["HOME_URL"]) {
                    $parent_name == "";
                }
                SimpleRouter::get($parent_name . '/' . $row['url'], function () use ($row) {
                    self::renderPage(self::getTemplateName($row["url"]));
                });
                $this->list_routes = $this->array_push_assoc($this->list_routes, $parent_name . "/" . $row['url'], 'get');
            }
        }
        foreach ($this->custom_pages as $handler => $function) {
            $method = substr($handler, 0, strpos($handler, '/'));
            $url = substr($handler, strpos($handler, '/') + 1);
            $this->list_routes = $this->array_push_assoc($this->list_routes, $method, $url);
            if ($method === "get") {
                SimpleRouter::get($url, $function);
            } elseif ($method === "post") {
                SimpleRouter::post($url, $function);
            } elseif ($method === "form") {
                SimpleRouter::form($url, $function);
            }
        }
        SimpleRouter::get('odhlasit', function () {
            Login::logout();
        });

        SimpleRouter::get('list_routes', function () {
            if (Login::isLogin()) {
                foreach ($this->list_routes as $key => $value) {
                    echo "$key $value <br>";
                }
                echo "<br>";
                print_r($this->custom_pages[0]);

            } else {
                http_response_code(404);
                self::renderPage(self::getTemplateName('not_found'));
            }

        });
        SimpleRouter::error(function ($request, $exception) {
            error_reporting(0);
            self::renderPage(self::getTemplateName('not_found'));
        });
        SimpleRouter::start();
    }

    public static function getTemplateName($url)
    {
        /**
         * Get template by url
         * @param string url of page
         * >return string name of template
         */

        if (file_exists(self::tem_file . $url . ".php")) {
            return self::tem_file . $url . ".php";
        } elseif (file_exists("engine/" . self::tem_file . $url . ".php")) {
            return "engine/" . self::tem_file . $url . ".php";
        } elseif ($url === "/") {
            return self::tem_file . "index.php";
        } else {
            return self::tem_file . "base_view.php";
        }
    }

    public function getPageName()
    {
        /**
         * Get url of page by url
         * >return string url of page
         */

        $backslash = strripos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "/");
        $ret = substr(str_replace("-", "_", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"), $backslash + 1);
        if (($ret == "")) {
            $ret = "home";
        }
        return $ret;
    }

    public static function getMethod()
    {
        /**
         * Get actual method of request
         * >return string method
         */

        return $_SERVER['REQUEST_METHOD'];
    }

    public static function renderPage($template)
    {
        /**
         * Render site with specific template
         * @param string $template
         *
         *
         *
         */
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        echo "<!DOCTYPE html>";
        echo $GLOBALS["head"];
        echo "<body>" . "\xA";
        self::renderMenu();
        if (isset($GLOBALS["debug"])) {
            if ($GLOBALS["debug"] === true) {
                echo '<div class="debug-window">' . $template . '</div>';
            }
        }

        if ($template === "engine/" . self::tem_file . "admin.php") {
            if (Login::isLogin()) {
                require "engine/admin.php";
            } else {
                echo $GLOBALS["admin_form"];
            }

            require "engine/" . self::tem_file . "admin.php";
        } else {
            try {
                require $template;
            } catch (Exception $e) {
                echo "File does not exist" . $e->getMessage();
            }
        }
        require self::tem_file . "footer.php";
        if (Login::isLogin()) {
            require_once "engine/templates/template.textedit.php";
        }
        echo "</body>" . "\xA";
        echo "</html>" . "\xA";

    }
}