<?php
/**
 *  MiniEditor Bar template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)  
 */
?>
<div class="editorBar">
    <?= !empty($tpldata['EDITOR_BAR_PRE']) ? $tpldata['EDITOR_BAR_PRE'] : null ?>
    <button class="btnEditor" type="button" value="[b]$1[/b]" tabindex="-1"><?= $LNG['L_EDITOR_BOLD'] ?></button>
    <button class="btnEditor" type="button" value="[i]$1[/i]" tabindex="-1"><?= $LNG['L_EDITOR_ITALIC'] ?></button>
    <button class="btnEditor" type="button" value="[u]$1[/u]" tabindex="-1"><?= $LNG['L_EDITOR_UNDERLINE'] ?></button>
    <button class="btnEditor" type="button" value="[p]$1[/p]" tabindex="-1"><?= $LNG['L_EDITOR_PARAGRAPH'] ?></button>
    <button class="btnEditor" type="button" value="[h2]$1[/h2]" tabindex="-1"><?= $LNG['L_EDITOR_H2'] ?></button>
    <button class="btnEditor" type="button" value="[h3]$1[/h3]" tabindex="-1"><?= $LNG['L_EDITOR_H3'] ?></button>
    <button class="btnEditor" type="button" value="[h4]$1[/h4]" tabindex="-1"><?= $LNG['L_EDITOR_H4'] ?></button>
    <button class="btnEditor" type="button" value="[pre]$1[/pre]" tabindex="-1"><?= $LNG['L_EDITOR_PRE'] ?></button>
    <button class="btnEditor" type="button" value="[size=14]$1[/size]" tabindex="-1"><?= $LNG['L_EDITOR_SIZE'] ?></button>
    <?php if ($cfg['minieditor_parser_allow_ext_img']) { ?>
        <button class="btnEditor" type="button" value="[img]$1[/img]" tabindex="-1"><?= $LNG['L_EDITOR_IMG'] ?></button>
    <?php } ?>
    <?php if ($cfg['minieditor_parser_allow_ext_url']) { ?>
        <button class="btnEditor" type="button" value="[url]$1[/url]" tabindex="-1"><?= $LNG['L_EDITOR_URL'] ?></button>
    <?php } ?>
    <button class="btnEditor" type="button" value="[list]$1[/list]" tabindex="-1"><?= $LNG['L_EDITOR_LIST'] ?></button>
    <button class="btnEditor" type="button" value="[style]$1[/style]" tabindex="-1"><?= $LNG['L_EDITOR_STYLE'] ?></button>
    <button class="btnEditor" type="button" value="[blockquote]$1[/blockquote]" tabindex="-1"><?= $LNG['L_EDITOR_QUOTE'] ?></button>
    <button class="btnEditor" type="button" value="[code]$1[/code]" tabindex="-1"><?= $LNG['L_EDITOR_CODE'] ?></button>
    <button class="btnEditor" type="button" value="[div class=?]$1[/div]" tabindex="-1"><?= $LNG['L_EDITOR_DIVCLASS'] ?></button>
    <?= !empty($tpldata['EDITOR_BAR_POST']) ? $tpldata['EDITOR_BAR_POST'] : null ?>
</div>