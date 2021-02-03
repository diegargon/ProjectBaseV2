<?php
/**
 *  MiniEditor editor template file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
?>

<?= !empty($data['editor_bar']) ? $data['editor_bar'] : null ?>
<textarea required="required"  minlength="<?= $cfg['minieditor_min_length'] ?>" maxlength="<?= $cfg['minieditor_max_length'] ?>" id="editor_text" name="editor_text" ><?= isset($data['text']) ? $data['text'] : null ?></textarea>
<div id="EditorBtnBottomContainer">
    <input id="btnEditorPreview" class="btnPreview" type="button" value="<?= $LNG['L_EDITOR_PREVIEW'] ?>"/>    
    <input id="btnEditorHiddePreview" class="btnPreview" type="button"  value="<?= $LNG['L_EDITOR_HIDDE_PREVIEW'] ?>"/>
    <?php if (isset($data['save_button'])) {
        ?>
        <input id="btnEditorSave" class="btnSave" type="button" value="<?= $LNG['L_EDITOR_SAVE'] ?>"/>
        <?php
    }
    ?>
</div>
<div id="editor_preview"></div>