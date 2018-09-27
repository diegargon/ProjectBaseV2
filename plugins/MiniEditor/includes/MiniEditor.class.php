<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class Editor {

    private $mark_codes = [
        '~\[p\](.*?)\[/p\]~si' => '<p>$1</p>',
        '~\[b\](.*?)\[/b\]~si' => '<span class="bold">$1</span>',
        '~\[i\](.*?)\[/i\]~si' => '<span class="italic">$1</span>',
        '~\[u\](.*?)\[/u\]~si' => '<span class="underline">$1</span>',
        '~\[pre\](.*?)\[/pre\]~si' => '<pre>$1</pre>',
        '~\[size=((?:[1-9][0-9]?[0-9]?))\](.*?)\[/size\]~si' => '<span style="font-size:$1px;">$2</span>',
        '~\[color=((?:[a-zA-Z]|#[a-fA-F0-9]{3,6})+)\](.*?)\[/color\]~si' => '<span style="color:$1;">$2</span>',
        '~\[localimg\](.*?)\[\/localimg\]~si' => '<p><img class="user_image_link" src="{STATIC_SRV_URL}$1" alt="$1" /></p>',
        '~\[localimg w=((?:[1-9][0-9]?[0-9]?))\](.*?)\[\/localimg\]~si' => '<p><img class="user_image_link" width="$1" src="{STATIC_SRV_URL}$2" alt="$2" /></p>',
        '~\[list\](.*?)\\[\\/list\\]~si' => '<ol>$1</ol>',
        '~\[\*\](.*)\[\/\*\]~i' => '<li>$1</li>',
        '~\[style=((?:[a-zA-Z-_:;])+)\]~si' => '<div style="$1">',
        '~\[/style\]~si' => '</div>',
        '~\[h2\](.*?)\[/h2\]~si' => '<h2>$1</h2>',
        '~\[h3\](.*?)\[/h3\]~si' => '<h3>$1</h3>',
        '~\[h4\](.*?)\[/h4\]~si' => '<h4>$1</h4>',
        '~\[div_class=((?:[a-zA-Z-_\s])+)\](.*?)\[/div_class\]~si' => '<div class="$1">$2</div>',
        '~\[blockquote\](.*?)\[/blockquote\]~si' => '<blockquote>$1</blockquote>',
        '~\[code\](.*?)\[/code\]~si' => '<code>$1</code>',
        '~\[br\]~si' => '<br/>',
        '~\[youtube\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>',
        '~\[youtube w=((?:[1-9][0-9]?[0-9]?)) h=((?:[1-9][0-9]?[0-9]?))\]https:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]~si' => '<div><iframe width="$1" height="$2" src="https:\/\/www.youtube.com\/embed\/$3" frameborder="0" allowfullscreen></iframe></div>',
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
        return $tpl->getTplFile("MiniEditor", "editor", $conf);
    }

    function preview() {
        global $db, $filter;
        $text = $db->escape_strip($filter->post_UTF8_txt('editor_text'));
        $text = stripcslashes($text);

        echo $this->parse($text);
    }

    function parse($text) {
        return $this->_parse($text);
    }

    private function _parse($text) {
        $text = preg_replace(array_keys($this->mark_codes), array_values($this->mark_codes), $text);
        $text = nl2br($text);
        $text = preg_replace('/><br \/>(\s*)(<br \/>)?/si', '>', $text);
        $text = preg_replace('/{STATIC_SRV_URL}/si', $this->srv_url, $text);
        $text = preg_replace('/\[S\]/si', DIRECTORY_SEPARATOR . $this->img_platform . DIRECTORY_SEPARATOR, $text);
        return $text;
    }

}
