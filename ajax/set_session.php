<?php
session_start();
if(isset($_POST)) {
    if(array_key_exists("action", $_POST)) {

            if(!array_key_exists("changes", $_SESSION)) {
                $_SESSION["changes"] = [];

                echo json_encode(
                    array(
                        "success" => true,
                        "changes created"
                    )
                );
                exit(0);
            }

            $ids = [];
            foreach($_SESSION["changes"] as $change_item) {
                $ids[] = $change_item["id"];
            }
            $found_item_ = false;
            if($_POST["action"] == "add") {
                $mes = "add";
                $found_key = false;
                $found = false;
                foreach ($_SESSION["changes"] as $change_key => $change) {
                    if($change["id"] == $_POST["changes_data"] && $change["field"] == $_POST["field"]) {
                        $found = true;
                        $found_key = $change_key;
                    }
                }
                if(!$found) {
                    $_SESSION["changes"][] = array(
                        "id" => intval($_POST["changes_data"]),
                        "value" => $_POST["current_val"],
                        "field" => $_POST["field"],
                    );
                } else {
                    if(is_numeric($found_key)) {
                        if($_POST["field"] == $_SESSION["changes"][$found_key]["field"])  {
                            if($_POST["current_val"] != $_SESSION["changes"][$found_key]["value"]) {
                                $_SESSION["changes"][$found_key] = array(
                                    "id" => intval($_POST["changes_data"]),
                                    "value" => $_POST["current_val"],
                                    "field" => $_POST["field"],
                                );
                            }
                        }

                    }
                }

            } else {
                $ret = false;
                if($_POST["action"] == "remove") {
                    $mes = "rem";
                    $found_item = 0;
                    foreach ($_SESSION["changes"] as $change_key => $change) {
                        if($change["id"] == $_POST["changes_data"] && $change["field"] == $_POST["field"]) {
                            $found_item_ = true;
                            unset($_SESSION["changes"][$change_key]);

                            break;
                        }
                    }

                    if(!$found_item_) {
                        header("Content-Type: application/json");
                        echo json_encode(
                            array(
                                "success" => false,
                                "action" => $_POST["action"],
                            )
                        );
                        exit(0);
                    } else {

                        header("Content-Type: application/json");
                        echo json_encode(
                            array(
                                "success" => true,
                                "action" => $_POST["action"],
                                "found" => $found_item_,
                            )
                        );
                        exit(0);
                    }
                }
            }
            header("Content-Type: application/json");
            echo json_encode(
                array(
                    "success" => true,
                    "action" => $_POST["action"],
                    "changes" => $_SESSION["changes"],
                )
            );

        }
} else {
    header("Content-Type", "application/json");
    echo json_encode(
        array(
            "success" => false,
            "action" => $_POST["action"],
        )
    );
}
