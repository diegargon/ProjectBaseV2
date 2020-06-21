<?php
/**
 *  SimpleFrontend template
 *
 *  Body Template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage SimpleFrontend
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div class="container">
    <div class="row1">
        <header id="header" class="clear">
            <?= isset($tpldata['ADD_HEADER_BEGIN']) ? $tpldata['ADD_HEADER_BEGIN'] : null ?>  
            <?php if ($cfg['tplbasic_nav_menu']) { ?>
                <script>
                    function toggleMenu() {
                        var x = document.getElementById("drop_content");
                        if (x.style.display === "block") {
                            x.style.display = "none";
                        } else {
                            x.style.display = "block";
                        }
                    }
                    window.onscroll = function () {
                        document.getElementById("drop_content").style.display = "none";
                    };
                </script>
                <?php
                isset($data['header_menu']) ? print $data['header_menu'] : null;
            }

            if (!empty($cfg['WEB_NAME'])) {
                ?>
                <div id="brand">                        
                    <a href="<?=($cfg['FRIENDLY_URL']) ?  $cfg['REL_PATH'] . $cfg['WEB_LANG'] . '/' :  $cfg['REL_PATH'] . $cfg['CON_FILE'] . '?lang=' . $cfg['WEB_LANG'];?>">
                        <?= $cfg['WEB_NAME'] ?></a><br/>
                    <span>
                        <?= !empty($cfg['WEB_DESC']) ? $cfg['WEB_DESC'] : null; ?>
                    </span>
                </div>

                <?php
            }
            ?>
            <?= isset($data['sections_menu']) ? $data['sections_menu'] : null; ?>
            <?= isset($tpldata['ADD_HEADER_END']) ? $tpldata['ADD_HEADER_END'] : null ?>

        </header>
    </div> 
    <!-- FIN ROW1 -->
    <?php
    !empty($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : null;
    !empty($tpldata['ADD_TO_BODY']) ? print $tpldata['ADD_TO_BODY'] : print '<div class="row2"><p>Hello World</p></div>';
    !empty($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : null;

    