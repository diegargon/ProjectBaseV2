<?php
/**
 *  NewsSearch results page template
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsSearch
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;
?>
<div  class="bodysize page">
    <div class="search_msg"><p><?= (!empty($data['msg'])) ? $data['msg'] : null ?></p></div>
    <div class="searchContainer searchPage">
        <form id="search" action="" method="get">
            <input id="searchTextInputPage" maxlength="255" type="text" name="q" value="<?= !empty($data['q']) ? $data['q'] : null ?>" />
            <button type="submit">
                <svg width="20px" height="20px" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"  xml:space="preserve">
                    <g><g transform="translate(0.000000,511.000000) scale(0.100000,-0.100000)"><path d="M3064.1,4921.3c-743.2-102.2-1409.8-430.2-1944.3-958.3c-528.1-519.6-849.7-1135.1-983.9-1884.7c-46.8-270.5-49-830.5,0-1107.4C383-455.9,1424.3-1535.6,2855.4-1844.4c193.8-40.5,285.4-46.9,670.8-46.9c379.1,0,479.2,6.4,664.4,46.9c449.3,98,864.6,268.3,1216,502.6l178.9,117.1l21.3-63.9c10.6-34.1,38.3-72.4,59.6-83c29.8-14.9,40.4-53.2,46.9-174.6c4.2-104.4,23.4-187.4,53.2-251.3c34.1-70.3,430.2-477,1437.5-1482.2c1316.1-1314,1397-1390.6,1505.6-1424.7c142.7-46.9,340.7-34.1,460,27.7c46.9,23.4,215.1,176.7,376.9,338.6c268.3,270.5,291.8,304.5,325.8,419.5c23.4,83,31.9,161.8,25.6,230c-27.7,232.1-12.8,215.1-1458.8,1663.2C6925-509.2,6976.2-553.9,6695-549.6c-132,0-157.6,6.4-191.7,49c-21.3,27.7-66,51.1-98,51.1c-31.9,2.1-57.5,8.5-57.5,12.8c0,4.3,42.6,74.5,95.8,159.7C6639.7,40.3,6793,423.6,6882.5,832.5c55.4,247,78.8,856.1,42.6,1124.4C6673.7,3848,4940.3,5176.8,3064.1,4921.3z M4058.6,4099.2c1020.1-206.6,1822.9-1015.8,2048.7-2063.6c46.9-225.7,53.2-766.7,8.5-990.3C5905-23.6,5102.1-835,4026.7-1065c-249.2-53.2-749.6-53.2-1000.9,0c-545.2,112.9-969,340.7-1354.4,726.2C1266.7,63.7,1024,525.8,928.1,1064.6c-40.5,238.5-40.5,677.2,0,909.3C1151.7,3266.6,2323,4212.1,3622.1,4152.5C3745.6,4146.1,3941.5,4122.7,4058.6,4099.2z"/></g></g>
                </svg>
            </button>
        </form>
    </div>            
</div>