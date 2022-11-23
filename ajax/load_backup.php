<?php
session_start();

header("Content-Type: application/json");
if(isset($_POST)) {
    if(isset($_POST["file"]) && !empty($_POST["file"])) {
        $filename = $_POST["file"];
        if(is_file("../backups/".$filename)) {
            require_once "../inc/config.php";
            require_once "../inc/MySQLManager.class.php";

            $m = new MySQLManager($create_backup=false, $is_ajax=true);
            $res = $m->load_backup_file("../backups/".$filename);
            if($res) {
                if(array_key_exists("changes", $_SESSION)) {
                    $_SESSION["changes"] = [];
                }
                if(array_key_exists("changed_items", $_SESSION)) {
                    $_SESSION["changed_items"] = [];
                }
            }
            echo json_encode([
                "success" => $res
            ]);
            exit(0);
        } else {
            $message = "no_file";
        }
    } else {
        $message = "no_file_specified";
    }
} else {
    $message = "no_post";
}

echo json_encode([
    "success" => false,
    "message" => $message
]);
exit(1);

