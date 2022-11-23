<?php
session_start();

require_once "../inc/config.php";
require_once "../inc/MySQLManager.class.php";

header("Content-Type: application/json");

if(isset($_POST["update"]) && $_POST["update"]) {

    $m=new MySQLManager($create_backup=false, $is_ajax=true);

    $post_keys = (array_keys($_POST));

    if(in_array("new_values", $post_keys)) {
        $vals_keys = array_keys($_POST["new_values"]);
        $vals = [];
        $current_result = $m->safe_query(
                "SELECT * FROM items WHERE id = '".$_POST["current_row"]."'"
        );
        if($current_result) {

            if(!isset($_SESSION["changed_items"]) && !is_array($_SESSION["changed_items"])) {
                if(isset($_SESSION["backup_created"]) && !$_SESSION["backup_created"]) {
                        $m->backup_tables("*", true);
                        $_SESSION["backup_created"] = true;
                }
            }

            $result_arr = $current_result->fetch_assoc();
            foreach (array_keys($result_arr) as $current_value_key) {
                if(in_array($current_value_key, $vals_keys)) {
                    if((float)$_POST["new_values"][$current_value_key]["new"] == 0) {
                        $new_input = NULL;
                    } else {
                        $new_input = number_format((float)$_POST["new_values"][$current_value_key]["new"], 2, ',');
                    }
                    $vals[$current_value_key."_result"] = $m->update_item($_POST["current_row"], $current_value_key, $new_input);
                    if($vals[$current_value_key."_result"]) {
                        if(!$_SESSION["changed_items"] || !is_array($_SESSION["changed_items"])) {
                            $_SESSION["changed_items"] = [];
                        }
                        $found_item = false;
                        if(count($_SESSION["changed_items"]) > 0) {

                            foreach ($_SESSION["changed_items"] as $item_key => $item) {
                                if($item["row"] == $_POST["current_row"] && $item["field"] == $current_value_key) {
                                        $_SESSION["changed_items"][$item_key] = array(
                                            "row" => $_POST["current_row"],
                                            "field" => $current_value_key,
                                            "value" => $new_input,
                                            "old_value" => $result_arr[$current_value_key]
                                        );
                                        $found_item = true;
                                }
                            }
                        }
                        if(!$found_item) {
                            $_SESSION["changed_items"][] = array(
                                "row" => $_POST["current_row"],
                                "field" => $current_value_key,
                                "value" => $new_input,
                                "old_value" => $result_arr[$current_value_key]
                            );
                        }
                    }
                }
            }
            echo json_encode(array(
                "vals_keys" => $vals_keys,
                "vals" => $vals,
                //"keys" => $post_keys)
            ));
        } else {
            echo json_encode(
                array(
                    "success" => false,
                    "message" => "no result"
                )
            );
        }

        exit(0);
    }

    echo json_encode(array("keys" => $post_keys));

} else {
    echo json_encode(array("success" => false));
}
