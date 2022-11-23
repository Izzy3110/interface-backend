<?php
header("Content-Type: application/json");

if(isset($_POST)) {
    if(isset($_POST["backup"]) && !empty($_POST["backup"])) {

        require_once "../inc/config.php";
        require_once "../inc/MySQLManager.class.php";

        $m = new MySQLManager($create_backup=false, $is_ajax=true);
        $m->backup_tables();

        echo json_encode([
            "success" => true
        ]);
        exit(0);
    } else {
        $message = "no_backup";
    }
} else {
    $message = "no_post";
}

echo json_encode([
    "success" => false,
    "message" => $message
]);
exit(1);
