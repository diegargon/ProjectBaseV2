<?php
/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
?>

<?= !empty($data['editor_bar']) ? $data['editor_bar'] : null ?>
<textarea required="required"  minlength="<?= $cfg['minieditor_min_length'] ?>" maxlength="<?= $cfg['minieditor_max_length'] ?>" id="editor_text" name="editor_text" ><?= isset($data['text']) ? $data['text'] : null ?></textarea>
<div id="EditorBtnBottomContainer">
    <input class="btnPreview" type='button' id="btnEditorPreview" value="<?= $LNG['L_EDITOR_PREVIEW'] ?>"/>
    <input class="btnPreview" type='button' id="btnEditorHiddePreview" value="<?= $LNG['L_EDITOR_HIDDE_PREVIEW'] ?>"/>
</div>
<div id="editor_preview"></div>