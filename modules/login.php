<div class="login_form_container">
    <div class="header_headline_container">
        <h2>Pizzaservice-Illmensee.de</h2>
        <h4 class="text_right">&raquo; Data Management</h4>
    </div>
    <form id="login_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="on">
        <label for="input_user">User: </label>
        <input id="input_user" type="text" name="user"><br>
        <label for="input_pass">Pass: </label>
        <input id="input_pass" type="password" name="pass"><br>
        <input type="submit" value="Login">
    </form>
</div>
<script>
    $(document).ready(function () {
        $.fn.allchange = function (callback) {
            var me = this;
            var last = "";
            var infunc = function () {
                var text = $(me).val();
                if (text != last) {
                    last = text;
                    callback();
                }
                setTimeout(infunc, 100);
            }
            setTimeout(infunc, 100);
        };

        $("#input_user, #input_pass").allchange(function () {
            // console.log($("#input_user").val())
            $("#login_form").submit();
        });
    })
</script>