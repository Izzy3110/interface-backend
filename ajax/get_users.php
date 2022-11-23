<?php
require_once "../inc/config.php";
require_once "../inc/MySQLManager.class.php";

header("Content-Type: application/json");

    $m=new MySQLManager($create_backup=false);
    $table_name = "auth";
    $search = ["id", "username"];
    $query_base = "SELECT ".implode(", ", $search)." FROM ".$table_name;

    if(isset($_GET["id"]) && !empty($_GET["id"])) {
        $user_id = $_GET["id"];
        $result = $m->get_username_by_id($user_id);
        echo $result;
    } else {
        $result = $m->get_username_by_id();
        echo $result;
    }
