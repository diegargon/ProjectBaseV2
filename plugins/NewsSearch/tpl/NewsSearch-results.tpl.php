<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if (!empty($data['TPL_FIRST'])) {
    ?>
    <div  class="clear bodysize page">
        <div id="searchResult">
            <section>
                <table class="searchTable">
                    <tr>
                        <td ><h2><?= $LNG['L_NS_SEARCH_RESULT'] ?></h2></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <a href="<?= $data['url'] ?>">
                            <div class="s_news_title"><?= $data['title'] ?></div>
                            <div class="s_news_lead"><?= $data['lead'] ?></div>
                        </a>
                    </td>
                </tr>
                <?php if (!empty($data['TPL_LAST'])) { ?>
                </table>
            </section>
        </div>
    </div>
    <?php
}
