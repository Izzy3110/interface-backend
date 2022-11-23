<style>
    pre {
        white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
        white-space: -pre-wrap; /* Opera */
        white-space: -o-pre-wrap; /* Opera */
        white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
        word-wrap: break-word; /* IE 5.5+ */
    }
    div[class^='pre_wrap'] {
        width: 20%;
        padding: 1em;
    }
    div.pre_wrap {
        background: rgba(255,24,24,0.85);
        color: #e8e8e8;
    }
    div.pre_wrap_success {
        background: #1FFF18D8;
        color: #0c5460;
    }
    .textarea_main {
        width: 76vw;
        min-height: 30vh;
        padding: .3em;
    }
</style>
<?php
/*
 *  $res = $m->compare_user_password("izzy3110", "test");
echo "<div style='width: 20%; background: #1FFF18D8; color: #0c5460; padding: 1em'><pre>";
var_dump($res);
echo "</pre></div>";

$res = $m->compare_user_password("izzy3110", "test1");
echo "<div style='width: 20%; background: #740F0FD8; color: #FFFFFF; padding: 1em'><pre>";
var_dump($res);
echo "</pre></div>";
 *
 */
require_once "../inc/config.php";
require_once "../inc/MySQLManager.class.php";


?>
<?php
$m = new MySQLManager($create_backup=false);
$option_html = $m->generate_users_select_option_html();

?>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <label for="select_user">Select User:</label>
        <select id="select_user" name="user">
            <?php
            echo $option_html;
            ?>
        </select>
        <label for="password_input"> Passwort:</label>
        <input id="password_input" type="text" name="text_input">
        <input type="submit"><br>
    </form>
<?php

if(isset($_POST) && !empty($_POST["user"])) {
    if(isset($_POST["text_input"])) {
        $encrypted = $m->encrypt_user_password($_POST["user"], $_POST["text_input"]);
        $textarea = "";
        $textarea = $encrypted.PHP_EOL.PHP_EOL. $_POST["text_input"];
        //$decrypted = $sec_man->decrypt_string($encrypted);
        //$exploded_decrypted = explode(":", base64_decode($decrypted));
        //if ($_POST["text_input"] == $exploded_decrypted[1]) {
            $m->update_user_password($user_id=$_POST["user"], $new_password=$encrypted);
        // }

        ?>
        <label>
        <textarea class="textarea_main"><?php echo $textarea; ?></textarea>
        </label><?php

    }
} else {
    $debug = true;
    if($debug) {
        $res = $m->compare_user_password("izzy3110", "test");
        echo "<div class='pre_wrap_success'>tested password, true expected\n";
        echo "<pre>";
        var_dump($res);
        echo "</pre>\n";
        echo "</div>";

        $res = $m->compare_user_password("izzy3110", "test1");
        echo "<div class='pre_wrap'>tested password, false expected\n";
        echo "    <pre>";
        var_dump($res);
        echo "    </pre>\n";
        echo "</div>";
    }
}