<?php
/**
 *  GoogleAnalytics template script code
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage GoogleAnalytics
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', '<?= $cfg['GOOGLE_ANALYTICS_CODE'] ?>', 'auto');
    ga('send', 'pageview');
</script>
