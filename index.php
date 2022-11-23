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
		if($_POST["user"] == "demo" && $_POST["pass"] == "demo") {
			$m = new MySQLManager($create_backup=false, $auth_only=true);
			$username = $_POST["user"];
			$res = $m->conn->query("SELECT * FROM auth WHERE username = '".mysqli_real_escape_string($m->conn, $username)."'");
			$_SESSION["user"] = $username;
			$m->unrestrict_tables();
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
