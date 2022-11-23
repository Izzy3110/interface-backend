<?php
session_start();
$session_id = "";
if (session_status() !== PHP_SESSION_NONE)
{
    $session_id = session_id();
}

require_once "inc/config.php";
require_once "inc/MySQLManager.class.php";


if(!isset($_SESSION["user"])) {
    if(isset($_POST) && isset($_POST["user"]) && isset($_POST["pass"])) {
        if(!empty($_POST["user"])) {
            $m = new MySQLManager($create_backup = false, $auth_only = true);
            $rall = $m->conn->query("SELECT * FROM items");
            $all = $rall->fetch_all();
            $_SESSION["items"] = $all;
            $_SESSION["items_t"] = time();
            $pass_ok = $m->compare_user_password($_POST["user"], $_POST["pass"]);
            echo "<pre>";
            echo "!!!";
            if ($pass_ok) {
                $_SESSION["user"] = $_POST["user"];

            }

        }
        header("Location: index.php");
    } else {


        ?>
    <html lang="de" data-session-id="<?php echo $session_id; ?>">
        <?php
        require_once "modules/html/head.php";
        ?>
        <body>
        <?php

        require_once "modules/login.php";
    }

} else {

?>
    <html lang="de" data-session-id="<?php echo $session_id; ?>">
        <?php
            require_once "modules/html/head.php";
        ?>
        <body>
    <?php

    $m = new MySQLManager($create_backup=false);

    require_once "modules/sections/header.php";
    require_once "modules/sections/main_content.php";
    require_once "modules/sections/footer.php";

?>
        </body>
    </html>
    <?php

}
