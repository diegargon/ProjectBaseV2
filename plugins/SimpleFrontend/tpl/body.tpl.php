<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>
<body id="Top">
    <div class="container">
        <div class="row1">
            <header id="header" class="clear">
                <?= isset($tpldata['ADD_HEADER_BEGIN']) ? $tpldata['ADD_HEADER_BEGIN'] : null ?>  
                <?php if ($cfg['tplbasic_nav_menu']) { ?>
                    <script>
                        function toggleMenu() {
                            document.getElementsByClassName("header-menu")[0].classList.toggle("responsive");
                        }
                    </script>
                    <div id="header-menu">
                        <ul class="header-menu">
                            <li class="nav_right resp-icon"><a rel="nofollow" href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a></li>
                            <?php if ($cfg['tplbasic_header_menu_home']) { ?>
                                <li class='nav_left lihome zero'>
                                    <a href='/<?= $cfg['FRIENDLY_URL'] ? $cfg['WEB_LANG'] : "?lang={$cfg['WEB_LANG']}"; ?>/'>
                                        <img width=20 height=20 src='<?= $cfg['tplbasic_img_home'] ?>' alt='<?= $LNG['L_HOME'] ?>' />
                                    </a></li>
                            <?php } ?>
                            <?= isset($tpldata['HEADER_MENU_ELEMENT']) ? $tpldata['HEADER_MENU_ELEMENT'] : null ?>
                        </ul>
                    </div>
                    <?php
                }

                if (!empty($cfg['WEB_NAME'])) {
                    ?>
                    <div id="brand">                        
                        <a href='/<?= $cfg['FRIENDLY_URL'] ? $cfg['WEB_LANG'] : "?lang={$cfg['WEB_LANG']}"; ?>'><?= $cfg['WEB_NAME'] ?></a><br/>
                        <span>
                            <?= !empty($cfg['WEB_DESC']) ? $cfg['WEB_DESC'] : null; ?>
                        </span>
                    </div>

                    <?php 
                }
                    if (!empty($tpldata['SECTIONS_NAV'])) { ?>
                        <nav id="sections_nav">
                            <ul>
                                <?= $tpldata['SECTIONS_NAV'] ?>
                            </ul>
                        </nav>
                        <?php
                    }
                    if (!empty($tpldata['SECTIONS_SUBMENU'])) {
                        ?>
                        <nav id="sections_submenu">
                            <ul>
                                <?= $tpldata['SECTIONS_SUBMENU'] ?>
                            </ul>
                        </nav>
                    <?php } ?>
                    <?= isset($tpldata['ADD_HEADER_END']) ? $tpldata['ADD_HEADER_END'] : null ?>

                </header>
            </div> 
            <!-- FIN ROW1 -->
            <?php
            !empty($tpldata['PRE_ACTION_ADD_TO_BODY']) ? print $tpldata['PRE_ACTION_ADD_TO_BODY'] : null;
            !empty($tpldata['ADD_TO_BODY']) ? print $tpldata['ADD_TO_BODY'] : print "<div class='row2'><p>Hello World</p></div>";
            !empty($tpldata['POST_ACTION_ADD_TO_BODY']) ? print $tpldata['POST_ACTION_ADD_TO_BODY'] : null;
            
