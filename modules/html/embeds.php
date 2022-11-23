<?php

$script_files = array(
    "jquery" => "https://code.jquery.com/jquery-3.6.1.min.js;integrity=sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=;crossorigin=anonymous",
    "popper" => "https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js;integrity=sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1;crossorigin=anonymous",
    "bootstrap" => "js/bootstrap.bundle.js",
    "datatables" => "js/datatables.min.js"
);

foreach ($script_files as $script_key => $script_file) {
    $script_file_expl = explode(";", $script_file);
    if(count($script_file_expl) > 1) {
        $script_file_ = strstr($script_file_expl[0], ".js");
        $script_file_integrity = strstr($script_file_expl[1], "integrity=");
        $script_file_crossorigin = strstr($script_file_expl[2], "crossorigin=");

        $integrity = false;
        if(strlen($script_file_integrity) > 0) {
            $expl_integrity = explode("integrity=", $script_file_integrity);
            $integrity = $expl_integrity[1];
        }

        $crossorigin = false;
        if(strlen($script_file_crossorigin) > 0) {
            $expl_crossorigin = explode("crossorigin=", $script_file_crossorigin);
            $crossorigin = $expl_crossorigin[1];
        }

        echo '<script src="'.$script_file_expl[0].'"'.($integrity ? " integrity=".$integrity : "").($crossorigin ? " crossorigin=".$crossorigin : "").'></script>';
    } else {
        echo '<script src="'.$script_file.'"></script>';
    }
}

$css_styles = array(
    "style" => array(
        "rel" => "stylesheet",
        "href" => "css/style.css",
        "type" => "text/css"
    ),
    "style_media" => array(
        "rel" => "stylesheet",
        "href" => "css/style.media.css",
        "type" => "text/css"
    ),
    "style_colors" => array(
        "rel" => "stylesheet",
        "href" => "css/style.colors.css",
        "type" => "text/css"
    ),
    "datatables" => array(
        "rel" => "stylesheet",
        "href" => "css/datatables.min.css",
        "type" => "text/css"
    ),
    "bootstrap-reboot" => array(
        "rel" => "stylesheet",
        "href" => "css/bootstrap-reboot.css",
        "type" => "text/css"
    ),
    "bootstrap_cdn" => array(
        "rel" => "stylesheet",
        "href" => "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css",
        "type" => "text/css"
    ),
    "bootstrap_datatables" => array(
        "rel" => "stylesheet",
        "href" => "https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap.min.css",
        "type" => "text/css"
    ),

    "dark" => array(
        "rel" => "stylesheet",
        "href" => "css/dark.css",
        "type" => "text/css"
    ),

);
foreach ($css_styles as $key => $css_style) {
    echo '<link rel="'.$css_style["rel"].'" type="'.$css_style["type"].'" href="'.$css_style["href"].'">';
}
