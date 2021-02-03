<?php

/**
 *  MiniEditor main class file
 * 
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage MiniEditor
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net)  
 */
!defined('IN_WEB') ? exit : true;

/**
 * Editor class
 */
class Editor {

    private $mark_codes = [
        '~\[p\](.*?)\[/p\]~si' => '<p>$1</p>',
        '~\[b\](.*?)\[/b\]~si' => '<span class="bold">$1</span>',
        '~\[i\](.*?)\[/i\]~si' => '<span class="italic">$1</span>',
        '~\[u\](.*?)\[/u\]~si' => '<span class="underline">$1</span>',
        '~\[pre\](.*?)\[/pre\]~si' => '<pre>$1</pre>',
        '~\[pre class=(.*?)\](.*?)\[/pre\]~si' => '<pre class="$1">$2</pre>',
        '~\[size=((?:[1-9][0-9]?[0-9]?))\](.*?)\[/size\]~si' => '<span style="font-size:$1px;">$2</span>',
        '~\[color=((?:[a-zA-Z]|#[a-fA-F0-9]{3,6})+)\](.*?)\[/color\]~si' => '<span style="color:$1;">$2</span>',
        '~\[localimg\](.*?)\[\/localimg\]~si' => '<p><img class="user_image_link" src="{STATIC_SRV_URL}$1" alt="$1" /></p>',
        '~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si' => '<p><img class="user_image_link" width="$1" src="{STATIC_SRV_URL}$2" alt="$2" /></p>',
        '~\[list\](.*?)\\[\\/list\\]~si' => '<ul>$1</ul>',
        '~\[olist\](.*?)\\[\\/olist\\]~si' => '<ol>$1</ol>',
        '~\[\*\](.*)~i' => '<li>$1</li>',
        '~\[style=((?:[a-zA-Z-_:;])+)\]~si' => '<div style="$1">',
        '~\[/style\]~si' => '</div>',
        '~\[h2\](.*?)\[/h2\]~si' => '<h2>$1</h2>',
        '~\[h3\](.*?)\[/h3\]~si' => '<h3>$1</h3>',
        '~\[h4\](.*?)\[/h4\]~si' => '<h4>$1</h4>',
        '~\[h2 id=(.*?)\](.*?)\[/h2\]~si' => '<h2 id=$1>$2</h2>',
        '~\[h3 id=(.*?)\](.*?)\[/h3\]~si' => '<h3 id=$1>$2</h3>',
        '~\[div class=((?:[a-zA-Z-_\s])+)\](.*?)\[/div\]~si' => '<div class="$1">$2</div>',
        '~\[blockquote\](.*?)\[/blockquote\]~si' => '<blockquote>$1</blockquote>',
        '~\[blockquote class=(.*?)\](.*?)\[/blockquote\]~si' => '<blockquote class="$1">$2</blockquote>',
        '~\[code\](.*?)\[/code\]~si' => '<code>$1</code>',
        '~\[code class=(.*?)\](.*?)\[/code\]~si' => '<code class="$1">$2</code>',
        '~\[br\]~si' => '<br/>',
        '~\[youtube\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>',
        '~\[youtube w=((?:[1-9][0-9]?[0-9]?)) h=((?:[1-9][0-9]?[0-9]?))\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe width="$1" height="$2" src="https:\/\/www.youtube.com\/embed\/$3" frameborder="0" allowfullscreen></iframe></div>',
        '~\[iurl=#(.*?)\](.*?)\[/iurl\]~si' => '<a href="#$1">$2</a>',
        '~\[iurl=/(.*?)\](.*?)\[/iurl\]~si' => '<a href="$1">$2</a>',
        '~\[warn\](.*?)\[/warn\]~si' => '<div class="warn"><div class="warn_svg"></div><div class="warn_text">$1</div></div>',
        '~\[tip\](.*?)\[/tip\]~si' => '<div class="tip"><div class="tip_svg"></div><div class="tip_text">$1</div></div>',
        '~\[spoiler\](.*?)\[/spoiler\]~si' => '<input class="spoilerbutton" type="button" value="+" onclick="this.value=this.value==\'+\'?\'-\':\'+\';"/><div class="spoiler"><div>$1</div></div>',
    ];
    private $srv_url;
    private $img_platform;

    public function __construct() {
        global $cfg;

        $this->srv_url = $cfg['STATIC_SRV_URL'];
        $this->img_platform = $cfg['img_selector'];

        if ($cfg['minieditor_parser_allow_ext_img']) {
            $this->mark_codes['~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~si'] = '<p><img class="user_image_link" src="$1" alt="" /></p>';
            $this->mark_codes['~\[img w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/img\]~si'] = '<p><img class="user_image_link" width="$1" src="$2" alt="" /></p>';
        }
        if ($cfg['minieditor_parser_allow_ext_url']) {
            $this->mark_codes['~\[url\]((?:ftps|https?)://.*?)\[/url\]~si'] = '<a rel="nofollow" target="_blank" href="$1">$1</a>';
            $this->mark_codes['~\[url=((?:ftps?|https?)://.*?)\](.*?)\[/url\]~si'] = '<a rel="nofollow" target="_blank" href="$1">$2</a>';
        }
    }

    function getEditor($conf = null) {
        global $tpl;

        $conf['editor_bar'] = $tpl->getTplFile('MiniEditor', 'MiniEditorBar');
        isset($conf['text']) ? $conf['text'] = stripcslashes($conf['text']) : null;
        return $tpl->getTplFile('MiniEditor', 'editor', $conf);
    }

    function showPreview() {
        global $db, $filter;
        $text = $db->escape($filter->postUtf8Txt('editor_text'));
        $text = stripcslashes($text);

        echo $this->parseText($text);
    }

    function parseText($text) {
        global $cfg;

        //Replace mark codes
        $text = preg_replace(array_keys($this->mark_codes), array_values($this->mark_codes), $text);
        //Replace new lines with br or enclose new lines with <p>
        $cfg['minieditor_nlbr'] ? $text = nl2br($text) : null;
        //delete br (examente no recuerdo, creo que doble br sin texto en medio)
        $text = preg_replace('/><br \/>(\s*)(<br \/>)?/si', '>', $text);
        // Replace STATIC_SRV_URL with the static server url
        $text = preg_replace('/{STATIC_SRV_URL}/si', $this->srv_url, $text);
        // Replace [S] (image relate tag with the plataform version path "Desktop" or "Mobile"
        $text = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $this->img_platform . DIRECTORY_SEPARATOR, $text);
        // Replace keywords !TEST with the link assoc
        if ($cfg['minieditor_keylinks']) {
            $this->parseKeyLinks($text);
        }
        return $text;
    }

    private function parseKeyLinks(&$text) {
        global $db;

        $query = $db->select('links', 'extra, link', ['plugin' => 'MiniEditor']);
        if ($db->numRows($query) < 1) {
            return false;
        }
        $keylinks = $db->fetchAll($query);

        foreach ($keylinks as $keylink) {
            $keylink_search = '!' . $keylink['extra'];
            $search[] = $keylink_search;
            if ($keylink['link'][0] == '/') {
                $keylink_replace = ' <a href="' . $keylink['link'] . '">' . $keylink['extra'] . '</a> ';
            } else {
                $keylink_replace = ' <a rel="nofollow" href="' . $keylink['link'] . '" target=_blank>' . $keylink['extra'] . '</a> ';
            }
            $replace[] = $keylink_replace;
        }
        $text = str_ireplace($search, $replace, $text);
    }

}
