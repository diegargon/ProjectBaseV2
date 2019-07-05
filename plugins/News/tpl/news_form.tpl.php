<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="clear bodysize page">
    <div class="standard_box submit_box">
        <form  id="form_news" action="#" autocomplete="on" method="post">
            <section>
                <h1><?= $data['news_form_title'] ?></h1>
                <div class="news_submit_center_wrapper">
                    <?= !empty($tpldata['NEWS_FORM_TOP_OPTION']) ? $tpldata['NEWS_FORM_TOP_OPTION'] : null ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_author"><?= $LNG['L_NEWS_AUTHOR'] ?> </label>
                            <input <?= $data['author_readonly'] ? "readonly=\"readonly\"" : null ?>  id="news_author" name="news_author" required="required" type="text"  maxlength="13" value="<?= $data['author'] ?>"/>
                            <input  name="news_author_id"  type="hidden" value="<?= $data['author_id'] ?>"/>
                        </p>
                    </div>
                    <?php if (!empty($data['translator'])) { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_translator"><?= $LNG['L_NEWS_TRANSLATOR'] ?> </label>
                                <input <?= $data['author_readonly'] ? "readonly=\"readonly\"" : null ?> id="news_translator" name="news_translator"  type="text"  maxlength="13" value="<?= $data['translator'] ?>"/>
                                <input  name="news_translator_id"  type="hidden" value="<?= $data['translator_id'] ?>"/>
                            </p>
                        </div>
                    <?php } ?>
                    <div class="submit_items">
                        <p>
                            <label for="news_title"><?= $LNG['L_NEWS_TITLE'] ?> </label>
                            <input value="<?= isset($data['title']) ? $data['title'] : null ?>"  minlength="<?= $cfg['news_title_min_length'] ?>" maxlength="<?= $cfg['news_title_max_length'] ?>" id="news_title" name="news_title" required="required" type="text" placeholder=""/>
                        </p>
                    </div>
                    <div class="submit_items">
                        <label for="news_lead"><?= $LNG['L_NEWS_LEAD'] ?> </label>
                        <textarea required="required"  minlength="<?= $cfg['news_lead_min_length'] ?>" maxlength="<?= $cfg['news_lead_max_length'] ?>" id="news_lead" name="news_lead" ><?= isset($data['lead']) ? $data['lead'] : null ?></textarea>
                    </div>
                    <div class="submit_items">
                        <label for="news_text"><?= $LNG['L_NEWS_TEXT'] ?> </label>
                        <?= isset($data['editor']) ? $data['editor'] : null; ?>
                    </div>
                    <?= !empty($tpldata['NEWS_FORM_MIDDLE_OPTION']) ? $tpldata['NEWS_FORM_MIDDLE_OPTION'] : null ?>

                    <?php if ($data['news_add_source']) { ?>
                        <div class="submit_items">
                            <p> 
                                <label for="news_source"><?= $LNG['L_NEWS_SOURCE'] ?> </label>
                                <input  value="<?= isset($data['news_source']) ? $data['news_source'] : null ?>"  minlength="<?= $cfg['news_link_min_length'] ?>" maxlength="<?= $cfg['news_link_max_length'] ?>" id="news_source" class="news_link" name="news_source" type="text" placeholder="http://site.com"/>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if ($data['news_add_related']) { ?>
                        <div class="submit_items">
                            <p>
                                <label for="news_new_related"><?= $LNG['L_NEWS_RELATED'] ?> </label>
                                <input  value="<?= isset($data['news_new_related']) ? $data['news_new_related'] : null ?>"  minlength="<?= $cfg['news_link_min_length'] ?>" maxlength="<?= $cfg['news_link_max_length'] ?>" id="news_new_related" class="news_link" name="news_new_related" type="text" placeholder="http://site.com"/>
                                <?= isset($data['news_related']) ? $data['news_related'] : null ?>
                            </p>
                        </div>
                    <?php } ?>
                    <?= !empty($tpldata['NEWS_FORM_BOTTOM_OPTION']) ? $tpldata['NEWS_FORM_BOTTOM_OPTION'] : null ?>
                    <div class="submit_items">
                        <p>
                            <span class="submit_others_label"><?= $LNG['L_NEWS_OTHER_OPTIONS'] ?> </span>
                            <?php if (!empty($data['select_categories'])) { ?>
                                <span  class="lang_label"><?= $LNG['L_NEWS_CATEGORY'] ?></span>
                                <?= $data['select_categories'] ?>
                            <?php } ?>
                            <?php if (defined('MULTILANG') && !empty($data['select_langs'])) { ?>
                                <span  class="lang_label"><?= $LNG['L_NEWS_LANG'] ?></span>
                                <?= $data['select_langs'] ?>
                            <?php } ?>
                            <?php if (!empty($data['select_acl'])) { ?>
                                <span  class="acl_label"><?= $LNG['L_ACL'] ?></span>
                                <?= $data['select_acl'] ?>
                                <span  class="featured_label"><?= $LNG['L_NEWS_FEATURED'] ?></span>
                                <input <?= !empty($data['featured']) ? "checked" : false ?> type="checkbox" name="news_featured" id="news_featured" value="1"/>
                            <?php } ?>
                        </p>
                        <?= !empty($tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION']) ? $tpldata['NEWS_FORM_BOTTOM_OTHER_OPTION'] : null ?>
                        <p>
                            <a href="<?= $data['terms_url'] ?>" target="_blank"><?= $LNG['L_TOS'] ?></a>
                            <input <?= !empty($data['tos_checked']) ? "checked" : null ?> id="tos" name="tos" required="required" type="checkbox"/>
                        </p>
                    </div>
                    <div class="submit_buttom">
                        <p>
                            <input type="submit" id="newsFormSubmit" name="newsFormSubmit" class="btnSubmitForm" value="<?= $LNG['L_SUBMIT_NEWS'] ?>" />
                        </p>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>