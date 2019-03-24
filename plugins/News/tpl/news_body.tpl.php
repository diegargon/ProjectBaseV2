<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
?>

<div id="news_container" class="newsrow">
    <div  class="clear bodysize page">
        <?php
        if (!empty($data['news_msg']) && !empty($data['news_msg'])) {
            ?>
            <div class="news_msg"><p><?= $data['news_msg'] ?></p></div>
            <?php
        }
        !empty($tpldata['ADD_TO_NEWSSHOW_TOP']) ? print $tpldata['ADD_TO_NEWSSHOW_TOP'] : null;
        if (!empty($data['news_breadcrum'])) {
            ?>
            <div id='news_breadcrum'>
                <ol <?= isset($data['ITEM_OL']) ? 'itemscope itemtype="http://schema.org/BreadcrumbList"' : null; ?> class='breadcrumb'>
                    <?= $data['news_breadcrum'] ?>
                </ol>
            </div>
            <?php
        }
        ?>
        <div class="sections_wrapper">
            <section class="article_body">
                <h1>
                    <?= !empty($data['title']) ? $data['title'] : null ?>
                </h1>

                <?php if (!empty($data['news_admin_nav'])) { ?>
                    <nav id='adm_nav'>
                        <ul>
                            <?= $data['news_admin_nav'] ?>
                        </ul>
                    </nav>
                <?php } ?>

                <div id="news_info">
                    <?php if (!empty($data['author_avatar'])) { ?> 
                        <div class='avatar'><img width='50' src='<?= $data['author_avatar']; ?>' alt='' /></div>                        
                    <?php } ?>
                    <?= !empty($tpldata['ADD_NEWS_INFO_POST_AVATAR']) ? $tpldata['ADD_NEWS_INFO_POST_AVATAR'] : null ?>
                    <div class="extra-small">
                        <?= $data['date'] ?> <br/>
                        <a href='/<?= $cfg['WEB_LANG'] ?>/profile&viewprofile=<?= $data['author_uid'] ?>'><?= $data['author'] ?></a>
                        <?= !empty($data['translator']) ? " | " . $LNG['L_NEWS_TRANSLATE_BY'] . $data['translator'] : null ?>
                        <?php if (!empty($data['news_sources'])) { ?>
                            | <span><?= $LNG['L_NEWS_SOURCE'] . ": " . $data['news_sources'] ?> </span>
                        <?php } ?>
                    </div>
                    <?= !empty($tpldata['ADD_NEWS_INFO_BOTTOM']) ? $tpldata['ADD_NEWS_INFO_BOTTOM'] : null ?>
                </div>
                <?php if (!empty($data['lead'])) { ?>
                    <p class="article_lead">
                        <?= $data['lead'] ?>
                    </p>
                <?php } ?>
                <hr/>
                <?= !empty($tpldata['NEWS_MAIN_PRE_TEXT']) ? $tpldata['NEWS_MAIN_PRE_TEXT'] : null ?>
                <div class="article_text">
                    <?= !empty($data['text']) ? $data['text'] : null ?>
                </div>
                <?= !empty($tpldata['NEWS_MAIN_AFTER_TEXT']) ? $tpldata['news_main__pre_text'] : null ?>
                <?php if (!empty($data['news_related'])) {
                    ?>
                    <div class="related">
                        <span><?= $LNG['L_NEWS_RELATED'] ?></span>
                        <ol>
                            <?= $data['news_related'] ?>
                        </ol>
                    </div>
                    <?php
                }
                !empty($data['pager']) ? print $data['pager'] : null;
                !empty($tpldata['ADD_TO_NEWSSHOW_BOTTOM']) ? print $tpldata['ADD_TO_NEWSSHOW_BOTTOM'] : null;
                ?>
            </section>
            <?php if ($cfg['news_side_news']) { ?>
                <section class="article_side">            
                    <?php
                    !empty($tpldata['ADD_TO_NEWS_SIDE_PRE']) ? print $tpldata['ADD_TO_NEWS_SIDE_PRE'] : null;
                    !empty($data['SIDE_NEWS']) ? print $data['SIDE_NEWS'] : null;
                    !empty($tpldata['ADD_TO_NEWS_SIDE_POST']) ? print $tpldata['ADD_TO_NEWS_SIDE_POST'] : null;
                    ?>
                </section>
            <?php } ?>
        </div>
    </div>
</div>