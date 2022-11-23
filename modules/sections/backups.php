<div id="controls">
    <div id="btn_close_backups"></div>
    <script>

        function removeURLParameter(url, parameter) {
            let urlparts = url.split('?');
            if (urlparts.length >= 2) {
                let prefix = encodeURIComponent(parameter) + '=';
                let pars = urlparts[1].split(/[&;]/g);

                for (let i = pars.length; i-- > 0;) {
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        pars.splice(i, 1);
                    }
                }

                return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
            }
            return url;
        }


        $(document).ready(function () {
            $("#btn_close_backups").on('click', function (ev) {
              ev.preventDefault();
                try {
                    window.location.href = new URL(document.referrer).toString()
                } catch (Exception) {
                    if (new URL(document.location).searchParams.get('site') === "backups") {
                        window.location.href = removeURLParameter(document.location.href, "site")
                    }
                }
            })
        })
    </script>
    <h2>Backup-Management</h2>
    <div class="main_table">
        <div class="main_row">
            <div class="cell">
                <h4>&raquo; Create Backup from Database</h4>
            </div>
        </div>
        <div class="main_row">
            <input id="create_backup_to_file_btn" class="controls_btn" type="button" value="Create">
        </div>
        <div style="display: table-row; height: .4vh">
            <div style="display:  table-cell; width: 100%; font-size: 1px;"></div>
        </div>
        <div class="main_row">
            <div class="cell">
                <h4>&raquo; Load Backup from selected File</h4>
            </div>
        </div>
        <div class="main_row">
            <div class="cell">
                <label for="select_file">Datei auswählen: </label>

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
                <input class="controls_btn" id="load_backup_from_file_btn" type="button" value="Load File">
            </div>
        </div>
    </div>
</div>
