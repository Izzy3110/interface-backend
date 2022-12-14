<?php

require_once "vendor/autoload.php";

use phpseclib3\Crypt\RSA;

class SecManager {
    public mixed $private = NULL;
    public mixed $public = NULL;
    private string $private_key_filename = "keys/private_rsa-4096.key";
    private string $public_key_filename = "keys/public_rsa-4096.key";
    private bool $private_key_loaded = false;
    private bool $public_key_loaded = false;

    public function __construct() {
        $this->init_keys();
    }

    private function init_keys(): void
    {
        if(!is_file($this->private_key_filename)) {
            echo "written: ".$this->private_key_filename."<br><br>";
            $fp = fopen($this->private_key_filename, "w");
            $this->private = RSA::createKey(4096);
            fwrite($fp, $this->private->__toString());
            fclose($fp);
        } else {
            $this->private = RSA::loadPrivateKey(file_get_contents($this->private_key_filename));
            $this->private_key_loaded = true;
        }


        if(!is_file($this->public_key_filename)) {
            $fp = fopen($this->public_key_filename, "w");
            $this->public = $this->private->getPublicKey();
            fwrite($fp, $this->public->__toString());
            fclose($fp);
        } else {
            $content = file_get_contents($this->public_key_filename);
            $this->public = RSA::loadPublicKey($content);
            $this->public_key_loaded = true;
        }

    }

    public function decrypt_string($ciphertext_b64): string
    {
        if($this->private_key_loaded) {
            return $this->private->decrypt(base64_decode($ciphertext_b64));
        }
        return "";
    }

    public function encrypt_string($text): string
    {
        if($this->public_key_loaded) {
            return base64_encode($this->public->encrypt($text));
        }
        return base64_encode($this->private->getPublicKey()->encrypt($text));
    }
}

class MySQLManager {
    public mysqli $conn;
    public bool $connected = false;
    public array $cols = array();
    public bool $debug = false;
    public bool $backup_mark_set = false;
    public array $table_results = array();

    public array $tables_excluded = [
        "auth",
        "fahrzeuge",
        "citys",
        "adds",
        "client_orders",
        "eissorten",
        "feasts",
        "food_variants",
        "itemcats",
        "items_old",
        "orders",
        "opening_hours",
        "pizza_zutaten_preise",
        "price_types",
        "pizza_zutaten"
    ];

    public array $first_cols = array(
        "items" => "id"
    );

    public array $hidden_cols = array(
        "items" => [
            "price_type",
            "category_id",
            "in_menu_1",
            "in_menu_2",
            "in_menu_3",
            "zutaten"]
    );

    public array $input_fields = [
        "add_name",
        "city",
        "price",
        "price_m",
        "price_l",
        "price_xl",
        "price_xxl",
    ];

    public mixed $is_ajax = false;
    /**
     * @var false
     */
    public bool $display_as_table = true;
    private bool $restricted = false;
    /**
     * @var array|false[]
     */
    private array $users;

    function __construct($create_backup=null, $is_ajax=null, $auth_only=null)
    {
        $this->users = [];
        if (!is_dir("../backups")) {
            mkdir("../backups");
        }


        if($auth_only != null) {
            $this->restricted = true;
        }
        if($is_ajax != null) {
            $this->is_ajax = $is_ajax;
        }
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS,DB_NAME);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->connected = true;

        if($create_backup) {
            $this->backup_mark_set = true;

        } else {
            if(!$this->is_ajax) {
                if($this->debug) {

                echo "&raquo; system notice: not creating backup unless some changes are made";

                }
            }
            $this->backup_mark_set = false;
        }
    }

    public function get_sql_files($reversed=null): array
    {

        $sql_files = [];
        foreach(scandir("backups") as $entry) {
            if($entry != "." && $entry != "..") {
                if (str_ends_with($entry, ".sql")) {
                    $spl = explode("-", $entry);
                    $data = [];
                    foreach ($spl as $splitted_item) {
                        if(str_ends_with($splitted_item, ".sql")) {
                            $data["file"] = $entry;
                            $data["label"] = date("Y-m-d H:i:s", explode(".sql", $splitted_item)[0])." - ".$entry;
                            break;
                        }
                    }

                    $sql_files[] = $data;
                }
            }
        }
        if($reversed) {
            return array_reverse($sql_files);
        }
        return $sql_files;
    }

    public function get_tables(): mysqli_result|bool
    {
        $sql = "SHOW TABLES";
        return $this->conn->query($sql);
    }

    function backup_tables($tables = '*', $is_save=null): void
    {
        $data = "\n/*---------------------------------------------------------------".
            "\n  SQL DB BACKUP ".date("d.m.Y H:i")." ".
            "\n  HOST: {".DB_HOST."}".
            "\n  DATABASE: {".DB_NAME."}".
            "\n  TABLES: ".$tables.
            "\n  ---------------------------------------------------------------*/\n";
        $this->conn->query( "SET NAMES `utf8` COLLATE `utf8_general_ci`"); // Unicode

        if($tables == '*'){
            $tables = array();
            $result = $this->conn->query("SHOW TABLES");
            while($row = $result->fetch_row()){
                $tables[] = $row[0];
            }
        }else{
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        foreach($tables as $table){
            $data.= "\n/*---------------------------------------------------------------".
                "\n  TABLE: `$table`".
                "\n  ---------------------------------------------------------------*/\n";
            $data.= "DROP TABLE IF EXISTS `$table`;\n";
            $res = $this->conn->query("SHOW CREATE TABLE `$table`");
            $row = $res->fetch_row();
            $data.= $row[1].";\n";

            $result = $this->conn->query("# noinspection SqlResolveForFile

# noinspection SqlResolve
SELECT * FROM `$table`");
            $num_rows = $result->num_rows;

            if($num_rows>0){
                $values = Array(); $z=0;
                for($i=0; $i<$num_rows; $i++){
                    $items = $result->fetch_row();
                    $values[$z]="(";
                    for($j=0; $j<count($items); $j++){
                        if (isset($items[$j])) { $values[$z].= "'".mysqli_real_escape_string( $this->conn, $items[$j] )."'"; } else { $values[$z].= "NULL"; }
                        if ($j<(count($items)-1)){ $values[$z].= ","; }
                    }
                    $values[$z].= ")"; $z++;
                }
                $data.= "# noinspection SqlResolve
INSERT INTO `$table` VALUES ";
                $data .= "  ".implode(";\nINSERT INTO `".$table."` VALUES ", $values).";\n";
            }
        }
        if(!$this->is_ajax) {
            echo "<div style='padding: 1em; border: 1px solid #333; background: #ececec;'>";
            echo "<h2>System Message</h2>";
            echo "<ul><li>writing backup file</li></ul>";
            echo "</div>";
        }
        /*
        if($is_save != null) {
            $this->write_backup_file($data, "save-2211070719-");
        } else {
            $this->write_backup_file($data);
        }
        */

        if($is_save != NULL) {
            $date_ = date("Ymd-His");
            $this->write_backup_file($data, $date_."-save-");
        } else {
            $date_ = date("Ymd-His");
            $this->write_backup_file($data, $date_."-");
        }
    }

    public function load_backup_file($file_location): bool
    {
        $commands = file_get_contents($file_location);
        return $this->conn->multi_query($commands);
    }



    public function write_backup_file($my_backup, $filename_part=false): void
    {
        if(!$filename_part || !isset($filename_part)) {
            $filename_part = "";
        }
        $backup_file = '../backups/' . DB_NAME . '-'.$filename_part.'backup-' . time() . '.sql';
        $handle = fopen($backup_file,'w+');
        fwrite($handle,$my_backup);
        fclose($handle);
    }

    public function unrestrict_tables(): void
    {
        if($this->restricted) {
            $this->restricted = false;
        }
    }

    public function safe_query($query) : mysqli_result|bool {
            $result = $this->conn->query($query);
            if($result->num_rows > 0) {
                return $result;
            }
            return false;
    }

    public function query_item($row_id, $row_name) {
        return $this->safe_query("SELECT ".$row_name." FROM items WHERE id = '".$row_id."'");
    }

    public function update_item($row_id, $row_name, $new_value) {
        $result = $this->query_item($row_id, $row_name);
        if($result) {
            $data = $result->fetch_assoc();

            $row = $row_name;
            $input_post = $new_value;

            if ($data[$row] != $input_post) {
                if($input_post == NULL || strlen(trim($input_post)) == 0) {
                    return $this->conn->query("UPDATE items SET {$row} = NULL WHERE id = {$row_id}");
                } else {
                    return $this->conn->query("UPDATE items SET {$row} = '{$input_post}' WHERE id = {$row_id}");
                }

            } else {
                return false;
            }
        }
    }



    public function output_html_tables(): void
    {
        if($this->restricted)
            return;

        $html_ = "";

        $tables_result = $this->get_tables();
        $all_tables = [];
        if ($tables_result->num_rows > 0) {
            while ($table_result = $tables_result->fetch_assoc()) {

                if (!in_array($table_result["Tables_in_" . DB_NAME], $all_tables) && !in_array($table_result["Tables_in_" . DB_NAME], $this->tables_excluded)) {
                    $all_tables[] = $table_result["Tables_in_" . DB_NAME];
                }

            }

            foreach ($all_tables as $current_table) {
                $html_ .= "<div class='table_name'><h2>" . $current_table . "</h2></div>";
                $html_ .= "<hr>";

                $data_query = "SELECT * FROM " . $current_table;

                $this->table_results[$current_table] = $this->conn->query($data_query);

                $struct_ = [];
                $table_structure_query = "DESCRIBE " . $current_table;
                $result_struct = $this->conn->query($table_structure_query);
                while ($row_struct = $result_struct->fetch_assoc()) {
                    if (!in_array($row_struct["Field"], $struct_)) {
                        $struct_[] = $row_struct["Field"];
                    }
                }
                if(!isset($this->already_changed)) {
                    $this->already_changed = [];
                }
                if(isset($_SESSION) && array_key_exists("changed_items", $_SESSION) && count($_SESSION["changed_items"]) > 0) {
                    // $html_ .= "<h1>&raquo; has changed items</h1>";
                    // $html_ .= implode(",", array_keys($_SESSION["changed_items"]));
                    foreach ($_SESSION["changed_items"] as $key => $val) {
                        if(is_array($val)) {

                            $row_id = $val["row"];
                            if (!in_array($row_id, $this->already_changed)) {
                                $this->already_changed[] = array("id" => $row_id, "old" => $val["old_value"], "field" => $val["field"]);
                            }
                        }
                        //} else {
                        //    echo "key: ".$key."  value: ".$val;
                        //}
                    }

                }
                /*
                 *
                 else {
                    $html_ .= "<h1>&raquo; no changed items</h1>";
                }
                */


                $html_ .= '<table id="base_table" class="display table table-striped table-bordered responsive" style="width: 100%">';
                $html_ .= "<thead>";
                $html_ .= "<tr>";

                foreach ($struct_ as $row_item) {
                    if(!in_array($row_item, $this->hidden_cols[$current_table])) {

                        $html_ .= "<th>".$row_item."</th>";
                        $this->cols[] = $row_item;

                    }
                }

                $html_ .= "<th>save row</th>";

                $html_ .= "</tr>";
                $html_ .= "</thead>";



                $html_ .= "<tbody>";

                if(isset($_GET["from_session"])) {
                    echo "<pre>";
                    if(isset($_SESSION["items"]) && !empty($_SESSION["items"])) {


                        if ($_SESSION["items"] ==  $this->table_results[$current_table]->fetch_all()) {
                            $items_assoc = [];
                            foreach ($_SESSION["items"] as $k_i => $session_item) {
                                foreach ($struct_ as $k => $row_item) {
                                    $items_assoc[$k_i][$row_item] = $_SESSION["items"][$k_i][$k];
                                }
                            }
                            $s = $this->conn->query("SELECT * FROM ".$current_table." WHERE id = '".$items_assoc[0]["id"]."'");
                            if($s->num_rows > 0) {
                                var_dump($s->fetch_assoc() == $items_assoc[0]);
                            }
                        }



                    }

                    echo "</pre>";

                }




                while ($data_row = $this->table_results[$current_table]->fetch_assoc()) {
                    $found_key = false;
                    $found_field = "";
                    $founds_ = [];
                    $id_results = [];
                    $olds = [];
                    foreach ($this->already_changed as $key => $value) {
                        if($value["id"] == $data_row[$this->first_cols[$current_table]]) {
                            // var_dump($value);
                            $id_results[$value["id"]][] = array(
                                "field" => $value["field"],
                                "old" => $value["old"]
                            );
                            $olds[$value["field"]] = $value["old"];
                        }
                    }
                    if(count(array_keys($id_results)) > 0) {
                        // var_dump($id_results);
                        $new_attr = "";
                        foreach ($id_results[$data_row[$this->first_cols[$current_table]]] as $field_data) {
                            $new_attr .= "old-".$field_data["field"]."='". $field_data["old"]."' ";
                        }
                        $html_ .="<tr class='already_changed' {$new_attr}>";
                    } else {
                        $html_ .="<tr>";
                    }


                    foreach ($this->cols as $row_item) {

                        if (in_array($row_item, array_keys($olds))) {
                            $field_class = " class='already_changed_td'";
                        } else {
                            $field_class = "";
                        }

                            $html_ .="<td ".$field_class.">";



                        if (in_array($row_item, $this->input_fields)) {
                            $data_field = "<input type='text' data-id='" . $data_row[$this->first_cols[$current_table]] . "' data-old='" . $data_row[$row_item] . "' data-field='" . $row_item . "' value='" . $data_row[$row_item] . "'>";
                        } else {
                            $data_field = $data_row[$row_item];
                        }
                        $html_ .= $data_field;
                        $html_ .="</td>";
                    }

                    $html_ .="<td>";
                    $html_ .='<input data-ident="save_btn" data-id="'.$data_row["id"].'" onclick="save_row('.$data_row["id"].')" type="button" value="save">';
                    $html_ .="</td>";
                    $html_ .="</tr>";
                }

                $html_ .= "</tbody>";

                $html_ .= "<tfoot>";
                $html_ .= "<tr>";

                foreach ($struct_ as $row_item) {
                    if(!in_array($row_item, $this->hidden_cols[$current_table])) {

                        $html_ .= "<th>".$row_item."</th>";
                    }
                }

                $html_ .= "<th>save row</th>";

                $html_ .= "</tr>";
                $html_ .= "</tfoot>";

                $html_ .= '</table>';
            }
        }
        echo $html_;
    }

    public function shutdown(): void
    {
        if($this->backup_mark_set) {
            $this->backup_tables();
        }
        $this->conn->close();
    }

    public function get_usernames(): array
    {
        $auth_table_name = "auth";
        $search = ["id", "username"];
        $query_base = "SELECT ".implode(", ", $search)." FROM ". $auth_table_name;
        $result = $this->safe_query(
            $query_base
        );
        if($result) {
            $data_result = $result->fetch_all();
            return array(
                "success" => true,
                "data" => $data_result,
            );
        }
        return array(
            "success" => false
        );
    }

    public function compare_user_password($username, $password): bool
    {
        $res = $this->conn->query("SELECT * FROM auth WHERE username = '".$username."'");
        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();
            $sec_man_d = new SecManager();
            $exploded = explode(":", base64_decode($sec_man_d->decrypt_string($data["pass_hash"])));
            return $exploded[1] == $password;
        }
        return false;
    }

    public function get_username_by_id($user_id): string|bool
    {
        $auth_table_name = "auth";
        $search = ["id", "username"];
        $query_base = "SELECT ".implode(", ", $search)." FROM ". $auth_table_name;
        $result = $this->safe_query(
            $query_base." WHERE id = ".$user_id
        );
        if($result->num_rows > 0) {
            $ds = $result->fetch_assoc();
            return $ds["username"];
        }
        return false;
    }

    public function update_user_password(int $user_id, string $new_password) : bool
    {
        $update_user_password["table_name"] = "auth";
        $update_user_password["columnn_name"] = "pass_hash";
        $update_user_password["ident"] = "id";
        $update_user_password["query"] = "UPDATE ".$update_user_password["table_name"]." SET ". $update_user_password["columnn_name"]." = '".mysqli_real_escape_string($this->conn, $new_password)."', pass_hash_last_update = ".time()." WHERE ".$update_user_password["ident"]." = '".$user_id."'";
        return $this->conn->query($update_user_password["query"]);
    }

    public function generate_users_select_option_html(): string
    {
        $option_html = "";
        if(!$this->users) {
            $this->users = $this->get_usernames();
        }

        if($this->users["success"]) {
            foreach($this->users["data"] as $user) {
                $option_html .= '<option value="'.$user[0].'">'.$user[1].'</option>';
            }
        }
        return $option_html;
    }

    public function encrypt_user_password($user_id, $pass_clear)
    {
        $sec_man = new SecManager();
        return $sec_man->encrypt_string(base64_encode($this->get_username_by_id($user_id).":".$pass_clear));
    }
}
