<script>
    function display_waiting_div() {
        let page = document.getElementsByTagName('body')[0];
        let overlay_waiting = document.createElement("div")
        let preparing_div = document.createElement("div")
        let img = document.createElement('img')

        preparing_div.className = "preparing_div_class"
        overlay_waiting.appendChild(preparing_div)

        img.src = "/images/Triangles-1s-200px.svg"
        overlay_waiting.appendChild(img)
        overlay_waiting.id = "waiting_div"
        overlay_waiting.className = "waiting_div_class"
        document.body.append(overlay_waiting)

        $(page).css({
            overflow: 'hidden'
        })
    }


    display_waiting_div()

</script>
<nav role="navigation">
			<?php
            if(isset($_SESSION)) {
                ?>
                <div id="login_link_row" class="row col-sm-1">
                    <a id="login_link" href="<?php echo $_SERVER["PHP_SELF"]; ?>?logout">&raquo; logout</a>
                </div>
            <?php
            if(isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            ?>
                <script>
                    window.location.reload()
                </script>
                <?php
            }
            }
            ?>
    <div id="menuToggle">
        <!--
        A fake / hidden checkbox is used as click reciever,
        so you can use the :checked selector on it.
        -->
        <input type="checkbox" />

        <!--
        Some spans to act as a hamburger.

        They are acting like a real hamburger,
        not that McDonalds stuff.
        -->
        <span></span>
        <span></span>
        <span></span>

        <!--
        Too bad the menu has to be inside of the button
        but hey, it's pure CSS magic.
        -->
        <ul id="menu">
            <a href="/"><li>&raquo; Items (Home)</li></a>
            <a href="/?site=backups"><li>Backups</li></a>
            <a class="link_inactive"><li>About</li></a>
            <!--
            <a class="link_inactive"><li>Info</li></a>
            <a class="link_inactive"><li>Contact</li></a>
            -->
            <a style="margin-top: .2rem;" href="/?logout"><li>&raquo; Logout</li></a>
            <div id="cr">© 2022 - L&S Design</div>
        </ul>
    </div>
</nav>
<section id="header">
    <div id="header_headline">
        <div id="header_fluid_container" class="container-fluid">
            <div class="row col-sm-4">
        <h1>Pizzaservice-Illmensee.de</h1>
            </div>
            <div class="row col-sm-7">
        <h3>Data Management</h3>
            </div>
            
        </div>
    </div>
</section>

<!--
<div class="preview_container_btn">

    <input id="save_changes_btn" class="changes_btn save_btn" type="button" value="Änderungen speichern" onclick="update_database()" disabled="disabled">
    <input class="changes_btn" type="button" value="Änderungen anzeigen" onclick="fadeInElement(this)">
    <input class="changes_reset_btn changes_btn" type="button" value="Änderungen zurücksetzen" onclick="resetChanges()">
</div>
-->