<?php
require_once "vendor/autoload.php";
use \Eventviva\ImageResize;
require_once $_SERVER['DOCUMENT_ROOT'] . "/engine/base/routes.php";
function getDates($blok)
{
    require $_SERVER['DOCUMENT_ROOT'] . "/conn.php";
    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n" . $conn->error);
    $send;
    $page = getPageName();
    if ($page == "home") {
        $page = "/";
    }
    $sql = "SELECT text FROM content WHERE (page_order=" . $blok . ") AND (page_id=(SELECT id FROM page WHERE url='" . $page . "' ))";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $send = $row["text"];
        }
    }
    if (isset($send)) {
        return '<div class="editable" value="' . $blok . '" >' . $send . '</div>';
    } else {
        return "Nenalezen žádný blok";
    }
}

function getHeading(int $blok)
{
    require $_SERVER['DOCUMENT_ROOT'] . "/conn.php";
    $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n" . $conn->error);
    $send;
    $page = getPageName();
    if ($page == "home") {
        $page = "/";
    }
    $sql = "SELECT heading FROM content WHERE (page_order=" . $blok . ") AND (page_id=(SELECT id FROM page WHERE url='" . $page . "' ))";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $send = $row["heading"];
        }
    }
    if (isset($send)) {
        return '<div class="editable" value="' . $blok . '" >' . $send . '</div>';
    } else {
        return "Nenalezen žádný blok";
    }
}

function imageUrl($path, $width, $height)
{
    if (!(substr($path, 0, 1) === "/")) {
        $path = "/" . $path;
    }
    $name = substr(substr($path, strrpos($path, '/')), 1);
    $path = $_SERVER['DOCUMENT_ROOT'] . $path;
    $res_path = $_SERVER['DOCUMENT_ROOT'] . "/" . "resized" . "/" . "_" . $width . "_" . $height . "_" . $name;
    $r_path = "/" . "resized" . "/" . "_" . $width . "_" . $height . "_" . $name;
    if (!file_exists($res_path)) {
        $image = new ImageResize($path);
        $image->resize($width, $height);
        $image->save($res_path);
        echo $r_path;
    } else {
        echo $r_path;
    }

}
