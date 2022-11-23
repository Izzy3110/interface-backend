<?php
session_start();

if(isset($_SESSION)) {
    if (isset($_POST["save"]) && isset($_SESSION["changes"]) && !empty($_SESSION["changes"])) {

        require_once "../inc/config.php";
        require_once "../inc/MySQLManager.class.php";
        $errors = [];
        $m = new MySQLManager($create_backup=false, $is_ajax=true);
        if(count($_SESSION["changes"]) > 0) {

            $m->backup_tables('*', true);

            $updated_changes = [];
            foreach ($_SESSION["changes"] as $change_key => $change) {

                $row_id = $change["id"];
                $row_name = $change["field"];
                $row_value = $change["value"];
                try {
                    $updated = $m->conn->query("UPDATE items SET `" . $row_name . "` = '" . $row_value . "' WHERE id = '" . $row_id . "'");
                    $updated_changes[] = $change_key;
                } catch (Exception $e) {
                    $errors[] = ($e->getMessage());
                }
            }
            if(count($errors) > 0) {
                header("Content-Type: application/json");
                echo json_encode([
                    "success" => false,
                    "updated" => count($updated_changes),
                    "errors" => $errors
                ]);
                exit(1);
            } else {
                header("Content-Type: application/json");
                echo json_encode([
                    "success" => true,
                    "updated" => count($updated_changes),
                    "errors" => $errors
                ]);
                exit(0);
            }
        }
    }
}