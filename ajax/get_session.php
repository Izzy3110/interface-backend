<?php

session_start();

if(count(array_keys($_SESSION)) > 0) {
    echo json_encode(array(
        "success" => true,
        "session_data" => $_SESSION));
    exit(0);
}
echo json_encode(array(
    "success" => false,
    "session_data" => $_SESSION));
exit(1);