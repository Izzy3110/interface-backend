<section id="content">
    <?php
    $site = false;
    if(isset($_GET["site"]) && !empty($_GET["site"])) {
        $site = $_GET["site"];
    }
    if($site && is_file("modules/sections/" . $site . ".php")) {
        require_once "modules/sections/" . $site . ".php";
    }
    ?>
<div id="changes_detected"></div>
<div id="container" class="container-fluid col-lg-12">
    <div id="tab-1" class="col-md-12">
        <?php

        if(!isset($m)) {
            $m = new MySQLManager($create_backup = false);
        }
        if (isset($m)) {
            $m->display_as_table = false;
            if(isset($_GET["table"])) {
                $m->display_as_table = true;
            }

            $m->output_html_tables();
            $m->shutdown();
        }
        ?>
    </div>
</div>
</section>

