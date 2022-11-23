<section id="content">
<div id="controls">
    <h2>Backup-Management</h2>
    <div class="main_table">
        <div class="main_row">
            <input id="create_backup_to_file_btn" class="controls_btn" type="button" value="Create Backup">
        </div>
        <div class="main_row">
            <div class="cell">
                <!-- <label for="select_file">Datei: </label> -->

                <?php
                $use_class = false;
                if(isset($m)) {
                    $sql_files = $m->get_sql_files($reversed=true);
                    $use_class = true;
                }
                sort($sql_files);
                ?>
                <select class="controls_btn" id="select_file" data-use-class="<?php echo $use_class; ?>">
                    <?php
                    $i = 0;
                    foreach ($sql_files as $sql_file) {

                        ?>
                        <option data-order-id="<?php echo $i; ?>" value="<?php echo $sql_file["file"]; ?>"><?php echo $sql_file["label"]; ?></option>
                        <?php
                        $i++;
                    }
                    ?>
                </select>
                <script>
                    function sort_select(element) {
                        let $select = $(element);

                        $select.append($select.find("option").remove()
                            .sort(function(a, b) {
                                var at = $(a).text(),
                                    bt = $(b).text();
                                var atime = new Date(Date.parse(at.split(" - ")[0])).getTime(),
                                    btime = new Date(Date.parse(bt.split(" - ")[0])).getTime()
                                return (atime < btime) ? 1 : ((atime > btime) ? -1 : 0);
                            }));

                        $select.find($('option').each(function() {
                            let spl_ = this.text.split("<?php echo DB_NAME; ?>");
                            let is_save = spl_[1].includes("save") ? true : false;
                            let new_option_ = spl_[0]+" <?php echo DB_NAME; ?>"+ (is_save ? " (before save)" : " (created)")
                            this.setAttribute("data-option-text", this.text)
                            $(this).text(new_option_ + '...');

                        }))


                        $select.val($("select#select_file option:first").val());
                    }

                    // sort select
                    sort_select($("select#select_file"))
                </script>
            </div>
        </div>
        <div class="main_row">
            <div class="cell">
                <input class="controls_btn" id="load_backup_from_file_btn" type="button" value="Load Backup from selected File">
            </div>
        </div>
    </div>
</div>
<br>

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
