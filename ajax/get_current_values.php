<?php
require_once "../inc/config.php";
require_once "../inc/MySQLManager.class.php";

header("Content-Type: application/json");

if(isset($_GET["data_id"]) && !empty($_GET["data_id"])) {

    $m=new MySQLManager($create_backup=false);

    $result = $m->safe_query(
        "SELECT * FROM items WHERE id = '".$_GET["data_id"]."'"
    );
    if($result) {
        $data_result = $result->fetch_assoc();
        $ds = [];
        foreach (array_keys($data_result) as $rs_key) {
            if(str_starts_with($rs_key, "price")) {
                $ds[$rs_key] = $data_result[$rs_key];
            }
        }
        echo json_encode(array(
            "success" => true,
            "data" => $ds,
        ));
    }
} else {
    echo json_encode(array("success" => false,
        "message" => "not a valid id"));
}
