<div class="login_form_container">
    <div class="header_headline_container">
        <h2>Pizzaservice-Illmensee.de</h2>
        <h4 class="text_right">&raquo; Data Management</h4>
    </div>
    <div style="width: 100%; padding: 1em;">
        <form id="login_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="on">
            <label id="label_login_user" for="input_user">User: </label>
            <input id="input_user" type="text" name="user"><br>
            <label id="label_login_pass" for="input_pass">Pass: </label>
            <input id="input_pass" type="password" name="pass" autocomplete="off"><br><br>
            <input type="submit" value="Login">
        </form>
    </div>
    <script>
        max_l = 0
        $("#input_pass, #input_user").on('focus', function (ev) {
            ev.preventDefault();

            current_len = $(this).val().length
            if(current_len > max_l) {
                max_l = current_len
            }
            if(max_l != 0) {
                console.log($(this).select())
            }

            max_l = 0
        })
    </script>
</div>
