<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div class="row3">
    <footer id="footer" class="clear">
        <?php
        isset($tpldata['ADD_TO_FOOTER']) ? print $tpldata['ADD_TO_FOOTER'] : null;
        isset($cfg['FOOT_COPYRIGHT']) ? print "<small class=\"fl_right\">" . $cfg['FOOT_COPYRIGHT'] . "</small>" : null;

        if (isset($cfg['WEB_URL']) && isset($cfg['TITLE'])) {
            print "<small class=\"fl_right\"><a href=\"" . $cfg['WEB_URL'] . "\">" . $cfg['TITLE'] . "</a></small>";
        }
        ?>        
    </footer>
</div> <!-- FIN ROW3 -->
</div> <!-- Container -->
<?= isset($tpldata['SCRIPTS_BOTTOM']) ? $tpldata['SCRIPTS_BOTTOM'] : null ?>
<br/><br/>
</body>
</html>