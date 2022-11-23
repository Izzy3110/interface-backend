<?php
session_start();

header("Content-Type: application/json");
$_SESSION["changes"] = [];
if (count($_SESSION["changes"]) > 0) {

    echo json_encode(array("success" => false));
} else {
    echo json_encode(array("success" => true));
}
exit(0);
