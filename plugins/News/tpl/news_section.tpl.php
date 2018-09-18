<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <div  class="clear bodysize page">
        <?php
        isset($tpldata['ADD_TOP_SECTION']) ? print $tpldata['ADD_TOP_SECTION'] : false;
    }
    ?>
    <?php if ($data['START_SECTION']) { ?>
        <div class="cols col<?= $data['SECTIONS'] ?>">            
        <?php } ?>

        <a href="<?= $data['url'] ?>">
            <article class="newsbox <?= $data['featured'] ? "featured" : null ?>">
                <p class='p-small'><?= $data['date'] ?></p>
                <h3><?= $data['title'] ?></h3>
                <p><?= $data['lead'] ?></p>
            </article>
        </a>

        <?php if ($data['END_SECTION']) { ?>       
        </div>
    <?php } ?>
    <?php
    if ($data['TPL_FOOT'] == 1) {
        isset($tpldata['ADD_BOTTOM_SECTION']) ? print $tpldata['ADD_BOTTOM_SECTION'] : null;
        ?>    
    </div>
<?php } ?>