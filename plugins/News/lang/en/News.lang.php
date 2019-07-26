<?php

/**
 *  News - News Language file english
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/* ADMIN */
$LNG['L_NEWS_NONEWS_MOD'] = 'No news for moderation';
$LNG['L_NEWS_WARN_NOLANG'] = 'Warning: No version of this page in your language';
$LNG['L_NEWS_NOT_EXIST'] = 'News not exists';
$LNG['L_CREATE_NEWS'] = 'Create news';
$LNG['L_NEWS_TITLE'] = 'Title <span class="text_small"> (Max/Min ' . $cfg['news_title_max_length'] . '/' . $cfg['news_title_min_length'] . ' characters)</span>';
$LNG['L_NEWS_LEAD'] = 'Lead <span class="text_small"> (Max/Min ' . $cfg['news_lead_max_length'] . '/' . $cfg['news_lead_min_length'] . ' characters)</span>';
$LNG['L_NEWS_TEXT'] = 'News text <span class="text_small"> (Max/Min ' . $cfg['news_text_max_length'] . '/' . $cfg['news_text_min_length'] . ' characters)</span>';
$LNG['L_NEWS_AUTHOR'] = 'Author';
$LNG['L_NEWS_ANONYMOUS'] = 'Anonymous';
$LNG['L_NEWS_LANG'] = 'Language';
$LNG['L_NEWS_OTHER_OPTIONS'] = 'Other options';
$LNG['L_NEWS_ERROR_INCORRECT_AUTHOR'] = 'Username incorrect';
$LNG['L_NEWS_INTERNAL_ERROR'] = 'Internal error, please try again';
$LNG['L_NEWS_TITLE_ERROR'] = 'There are some error in the title, check characters or provide a title if empty';
$LNG['L_NEWS_TITLE_MINMAX_ERROR'] = 'The title must have between  ' . $cfg['news_title_max_length'] . ' and ' . $cfg['news_title_min_length'] . ' characteres';
$LNG['L_NEWS_LEAD_ERROR'] = 'There are some error in the lead, check characters or provide a title if empty';
$LNG['L_NEWS_LEAD_MINMAX_ERROR'] = 'The lead must have between ' . $cfg['news_lead_max_length'] . ' and ' . $cfg['news_lead_min_length'] . ' characteres';
$LNG['L_NEWS_TEXT_ERROR'] = 'Empty text or characters not allowed';
$LNG['L_NEWS_TEXT_MINMAX_ERROR'] = 'The news text must have between ' . $cfg['news_text_max_length'] . ' and ' . $cfg['news_text_min_length'] . ' characteres';
$LNG['L_NEWS_CATEGORY'] = 'Category';
$LNG['L_NEWS_ADMIN'] = 'Administrator';
$LNG['L_NEWS_ALL_NOADMIN'] = 'All except admistration';
$LNG['L_NEWS_SUBMIT'] = 'Can send news';
$LNG['L_NEWS_COMMENT'] = 'Can comment news';
$LNG['L_NEWS_PAYMENT'] = 'Pay group';
$LNG['L_NEWS_READ'] = 'Can\'t read news';
$LNG['L_NEWS_SUBMITED_SUCCESSFUL'] = 'News succesful submited';
$LNG['L_NEWS_FEATURED'] = 'Feature';
$LNG['L_NEWS_MODERATION'] = 'Moderation';
$LNG['L_NEWS_MODERATION_DESC'] = 'Here you moderate the news sended to your web';
$LNG['L_NEWS_ERROR_WAITINGMOD'] = 'News its wating moderation';
$LNG['L_NEWS_DELETE'] = 'Delete';
$LNG['L_NEWS_EDIT'] = 'Edit';
$LNG['L_NEWS_APPROVED'] = 'Approve';
$LNG['L_NEWS_EDIT'] = 'Edit';
$LNG['L_NEWS_DISABLE'] = 'Disable';
$LNG['L_NEWS_CONFIRM_DEL'] = ' Delete news, are you sure?';
$LNG['L_NEWS_EDIT_NEWS'] = 'News edit';
$LNG['L_NEWS_UPDATE_SUCCESSFUL'] = 'News update succesful';
$LNG['L_NEWS_NO_EDIT_PERMISS'] = 'Can\'t edit, no rights';
$LNG['L_NEWS_FRONTPAGE'] = 'Frontpage';
$LNG['L_NEWS_CATEGORY_DESC'] = 'Submit or modify here categories for the news';
$LNG['L_NEWS_CREATE'] = 'Create';
$LNG['L_NEWS_MODIFY'] = 'Modify';
$LNG['L_NEWS_MODIFIED_CATS'] = 'Modify categories';
$LNG['L_NEWS_CREATE_CAT'] = 'Create categories';
$LNG['L_NEWS_CATEGORIES'] = 'Categories';
$LNG['L_NEWS_INFRONTPAGE'] = 'In Frontpage';
$LNG['L_NEWS_INFRONTPAGE_DESC'] = 'Here you can see and change the news in your frontpage';
$LNG['L_NEWS_BACKPAGE'] = 'Backpage';
$LNG['L_NEWS_SOURCE'] = 'Source';
$LNG['L_NEWS_RELATED'] = 'Related';
$LNG['L_NEWS_NEWLANG'] = 'Translate';
$LNG['L_NEWS_TRANSLATOR'] = 'Translator';
$LNG['L_NEWS_TRANSLATE_SUCCESSFUL'] = 'News translated and submit successful';
$LNG['L_NEWS_TRANSLATE_BY'] = 'Translate by ';
$LNG['L_NEWS_E_RELATED'] = 'Incorrect/Not working related link, fix it or leave blank';
$LNG['L_NEWS_E_SOURCE'] = 'Incorrect/Not working  source link, fix it or leave blank';
$LNG['L_NEWS_E_ALREADY_TRANSLATE_ALL'] = 'News already translate to all active languages.';
$LNG['L_NEWS_NEW_PAGE'] = 'New page';
$LNG['L_NEWS_CREATE_NEW_PAGE'] = 'Create new page';
$LNG['L_NEWS_DELETE_NOEXISTS'] = 'News deleted or inexistent';
$LNG['L_NEWS_SECTION'] = 'section'; //Need mod htaccess if change
$LNG['L_NEWS_E_SEC_NOEXISTS'] = 'Section not exists';
$LNG['L_NEWS_FATHER'] = 'Father';
$LNG['L_NEWS_ORDER'] = 'Weight';
$LNG['L_E_NOEDITACCESS'] = 'You haven\'t access to edit';

/* NEW */

$LNG['L_NEWS_ALREADY_EXIST'] = 'News alredy exist';
$LNG['L_E_NOVIEWACCESS'] = 'You haven permissions to view this news';
$LNG['L_NEWS_NOCATS'] = 'No categories configured, you can\'t send any news';
$LNG['L_NEWS_CHILDS'] = 'Include childs';
$LNG['L_NEWS_TITLES'] = 'Titles';
$LNG['L_NEWS_TITLESLEAD'] = 'Titles / lead';
$LNG['L_NEWS_FULLNEWS'] = 'Full news';
$LNG['L_NEWS_DISPLAY_TYPE'] = 'Show:';
$LNG['L_NEWS_LIMITS'] = 'Limit';
$LNG['L_NEWS_BLOCK_TITLE'] = 'Block title';
$LNG['L_NEWS_E_SEC_NOEXISTS'] = 'Section not exist';
$LNG['L_SUBMIT_NEWS'] = 'Send';
$LNG['L_NEWS_SEC_EMPTY'] = 'Seccion vacia';
$LNG['L_NEWS_SEC_EMPTY_TITLE'] = 'Oops';
$LNG['L_NEWS_DELETE_PAGE'] = 'Delete page';
$LNG['L_NEWS_NOMULTILANG_SUPPORT'] = 'No multilang support';
$LNG['L_NEWS_SHOW_LANG'] = 'Show news in';
$LNG['L_NEWS_CREATE'] = 'Create';
$LNG['L_NEWS_UPDATE'] = 'Update';
$LNG['L_NEWS_ASDRAFT'] = 'Save as a draft';
$LNG['L_NEWS_E_VIEW_DRAFT'] = 'You can\'t view this draft';
$LNG['L_NEWS_WARNING_DRAFT'] = 'Warning: this news as a draft';
$LNG['L_NEWS_E_CANT_ACCESS'] = 'No access';
$LNG['L_NEWS_E_NODRAFTS'] = 'You havent drafts';
$LNG['L_NEWS_DRAFTS'] = 'Drafts';
$LNG['L_NEWS_NPAGE'] = 'Num Page';
$LNG['L_NEWS_NEWS_LINK'] = 'Link to news';

/* ACL */
$LNG['L_PERM_R_NEWS_FULL_ACCESS'] = 'Full admin access to read';
$LNG['L_PERM_W_NEWS_FULL_ACCESS'] = 'Full admin access to read/write';
$LNG['L_PERM_R_VIEW_NEWS'] = 'View news';
$LNG['L_PERM_R_CREATE_NEWS'] = 'Create news';
$LNG['L_PERM_W_ADD_SOURCE'] = 'Add source to news';
$LNG['L_PERM_W_ADD_RELATED'] = 'Add relate to news';
$LNG['L_PERM_W_FEATURE'] = 'Mark as feature';
$LNG['L_PERM_W_EDIT'] = 'Edit news';
$LNG['L_PERM_W_TRANSLATE'] = 'Translate news';
$LNG['L_PERM_W_OWN_TRANSLATE'] = 'Translate own news';
$LNG['L_PERM_W_CHANGE_AUTHOR'] = 'Change author';
$LNG['L_PERM_W_DELETE'] = 'Delete news';
$LNG['L_PERM_W_MODERATION'] = 'Aprobe news';
$LNG['L_PERM_W_FRONTPAGE'] = 'Mark as frontpage';
$LNG['L_PERM_W_EDIT_OWN'] = 'Edit own pages';
$LNG['L_PERM_W_DELETE_OWN'] = 'Delete own news';
$LNG['L_PERM_W_ADD_PAGES'] = 'Add pages to own news';
