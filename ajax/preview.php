<?php
session_start();

require_once "inc/config.php";
require_once "inc/MySQLManager.class.php";

$session_array = [];

$error = false;
if(!isset($_SESSION["changes"])) {
    $error = true;
} else {
    $session_array = $_SESSION["changes"];
}

function get_data_key($data_items, $row_id): bool|int|string
{
    foreach ($data_items as $key => $data_item) {
        if($data_item["id"] == $row_id) {
            return $key;
        }
    }
    return false;
}


if(!$error) {

    $m = new MySQLManager($create_backup = false);

    $results = $m->conn->query("SELECT * FROM items");
    $data_items = [];
    while ($row = $results->fetch_assoc()) {
        $data_items[] = $row;
    }

    if(count($session_array) > 0)  {

        $html = '<table style="color: #0c0c0c; border: 1px solid #333; background: #FFF; margin-bottom: 1em; width: 100%; " class="closed">
            <tr>
                <td width="50"><b>id:</b></td>
                <td width="250"><b>name:</b></td>
                <td width="150"><b>field:</b></td>
                <td width="150"><b>value:</b></td>
                <td width="150"><b>old value:</b></td>
                <td></td>
            </tr>';

            foreach ($session_array as $row_id => $item) {
                $data = $data_items[get_data_key($data_items, $item["id"])];
                $old_value = ($data[$item["field"]] != NULL) ? $data[$item["field"]] : "<b>-</b>";

                $html .= '
                <tr>
                    <td>'.$item["id"].'</td>
                    <td>'.$data["name"].'</td>
                    <td>'.$item["field"].'</td>
                    <td>'.$item["value"].'</td>
                    <td>'.$old_value.'</td>
                    <td></td>
                </tr>';

            }

        $html .= '</table>';
            header("Content-Type: application/json");
        echo json_encode(
            array(
                "success" => true,
                "changes"=> $session_array,
                "html" => $html
            ),
            JSON_UNESCAPED_SLASHES);
        exit(0);

    } else {
        echo json_encode(
            array(
                "success" => false,
                "message" => "no changes or recently reset _SESSION"
            )
        );
        exit(1);
    }

} else {
    header("Content-Type: application/json");
    echo json_encode(
        array(
            "success" => false,
            "message" => "session not available"
        )
    );

}
