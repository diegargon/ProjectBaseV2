<?php
/*
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */
!defined('IN_WEB') ? exit : true;

if ($data['TPL_CTRL'] == 1) {
    ?>
    <br/>
    <p><?= isset($data['STATE_MSG']) ? $data['STATE_MSG'] : null; ?></p>
    <h2>Crear Bloque</h2>
    <form id="addBlock" action="#" method="POST">
        <div class="divTable">
            <div class="divTableHeadRow">
                <div class="divTableHeadCell"><?= $LNG['L_BLK_PAGE'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_BLK_SECTION'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_NAME'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_DESC'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_BLK_WEIGHT'] ?></div>
                <div class="divTableHeadCell"><?= $LNG['L_BLK_CANUSERDISABLE'] ?></div>
            </div>
            <div class="divTableBody">
                <div class="divTableRow">  
                    <div class="divTableCell">
                        <select id="block_page" name="block_page" onchange="this.form.submit()">
                            <?= $data['page_options'] ?>
                        </select>
                    </div>
                    <div class="divTableCell">
                        <select id="block_section" name="block_section">
                            <?= $data['sections'] ?>
                        </select>
                    </div>
                    <div class="divTableCell">
                        <select id="blockname" name="blockname" onchange="this.form.submit()">
                            <?= $data['reg_blocks'] ?>    
                        </select>
                    </div>
                    <div class="divTableCell">
                        <p><?= $data['block_desc'] ?></p>
                    </div>                    
                    <div class="divTableCell">
                        <select name="block_weight">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option selected value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>                    
                        </select>
                    </div>
                    <div class="divTableCell"><input type="checkbox" name="disable_by_user" value="1"/></div>
                </div>
            </div> <!-- TableBody -->
            <div class="divTableFootRow">
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
                <div class="divTableFootCell"></div>
                <!-- <div class="divTableFootCell"></div> -->
            </div>                
        </div>
        <?= isset($data['block_config_request']) ? $data['block_config_request'] : null ?>
        <br/>
    </form>
    <br/>
    <h2>Eliminar Bloque</h2>
    <div class="divTable">
        <div class="divTableHeadRow">
            <div class="divTableHeadCell"><?= $LNG['L_BLK_PAGE'] ?></div>
            <div class="divTableHeadCell"><?= $LNG['L_BLK_SECTION'] ?></div>
            <div class="divTableHeadCell"><?= $LNG['L_NAME'] ?></div>
            <div class="divTableHeadCell"><?= $LNG['L_BLK_WEIGHT'] ?></div>
            <div class="divTableHeadCell"><?= $LNG['L_BLK_CANUSERDISABLE'] ?></div>
            <div class="divTableHeadCell"><?= $LNG['L_BLK_ACTIONS'] ?></div>
        </div>
    <?php } ?> <!--- TPL_HEAD -->
    <?php if ($data['blocks_notempty']) { ?>    
        <div class="divTableBody">
            <div class="divTableRow">  
                <div class="divTableCell">
                    <?= $data['page'] ?>
                </div>
                <div class="divTableCell">
                    <?= $data['section'] ?>
                </div>
                <div class="divTableCell">                    
                    <?= $data['block'] ?>    
                </div>
                <div class="divTableCell">
                    <?= $data['weight'] ?>
                </div>
                <div class="divTableCell">
                    <?= $data['canUserDisable'] ?>
                </div>
                <div class="divTableCell">
                    <form id="delBlock" action="#" method="POST">
                        <input type="hidden" name="block_id" value="<?= $data['block_id'] ?>"/>
                        <input type="hidden" name="editblockname" value="<?= $data['block'] ?>"/>
                        <input type="submit" name="btnEditBlock" value="<?= $LNG['L_EDIT'] ?>"/>
                        <input type="submit" name="btnDelBlock" value="<?= $LNG['L_DELETE'] ?>"/>
                    </form>
                </div>
            </div>              
        </div> <!-- TableBody -->
    <?php } ?>    
    <?php if ($data['TPL_FOOT'] == 1) { ?>
        <div class="divTableFootRow">
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
            <div class="divTableFootCell"></div>
        </div>                
    </div>    

<?php } ?>
