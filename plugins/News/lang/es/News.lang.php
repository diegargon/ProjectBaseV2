<?php

/**
 *  News - News language file - Spanish
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */
!defined('IN_WEB') ? exit : true;

/* ADMIN */
$LNG['L_NEWS_MODERATION'] = 'Moderación';
$LNG['L_NEWS_NONEWS_MOD'] = 'No hay noticias aguardando moderación';
$LNG['L_NEWS_CATEGORIES'] = 'Categorias';
$LNG['L_NEWS_CREATE_CAT'] = 'Crear categoria';
$LNG['L_NEWS_DELETE'] = 'Borrar';
$LNG['L_NEWS_APPROVED'] = 'Aprobar';
$LNG['L_NEWS_FATHER'] = 'Padre';
$LNG['L_NEWS_ORDER'] = 'Peso';
$LNG['L_NEWS_CREATE'] = 'Crear';
$LNG['L_NEWS_MODIFY_CATS'] = 'Modificar categorias';
$LNG['L_SUBMIT_NEWS'] = 'Enviar';
$LNG['L_NEWS_AUTHOR'] = 'Autor';
$LNG['L_NEWS_ANONYMOUS'] = 'Anonimo';
$LNG['L_NEWS_TITLE'] = 'Titulo';
$LNG['L_NEWS_LEAD'] = 'Entradilla';
$LNG['L_NEWS_TEXT'] = 'Texto de la noticia';
$LNG['L_NEWS_SOURCE'] = 'Fuente';
$LNG['L_NEWS_RELATED'] = 'Relacionadas';
$LNG['L_NEWS_LANG'] = 'Idioma';
$LNG['L_NEWS_OTHER_OPTIONS'] = 'Otras opciones';
$LNG['L_NEWS_CATEGORY'] = 'Categoria';
$LNG['L_NEWS_ERROR_INCORRECT_AUTHOR'] = 'Nombre de usuario incorrecto';
$LNG['L_NEWS_INTERNAL_ERROR'] = 'Error interno, por favor vuelva a intentarlo';
$LNG['L_NEWS_TITLE_ERROR'] = 'Hay algun error en el titulo, compruebe que los caracteres sean correctos o que el campo no este vacio';
$LNG['L_NEWS_TITLE_MINMAX_ERROR'] = 'El titulo tiene que estar entre maximo de ' . $cfg['news_title_max_length'] . ' y un minimo de ' . $cfg['news_title_min_length'] . ' caracteres';
$LNG['L_NEWS_LEAD_ERROR'] = 'Hay algun error en la entradilla, compruebe que los caracteres sean correctos o que el campo no este vacio';
$LNG['L_NEWS_LEAD_MINMAX_ERROR'] = 'La entradilla tiene que estar entre maximo de ' . $cfg['news_lead_max_length'] . ' y un minimo de ' . $cfg['news_lead_min_length'] . ' caracteres';
$LNG['L_NEWS_TEXT_ERROR'] = 'Hay algun error en el texto de la noticia, compruebe que los caracteres sean correctos o que el campo no este vacio';
$LNG['L_NEWS_TEXT_MINMAX_ERROR'] = 'El texto de la noticia tiene que estar entre maximo de ' . $cfg['news_text_max_length'] . ' y un minimo de ' . $cfg['news_text_min_length'] . ' caracteres';
$LNG['L_NEWS_SUBMITED_SUCCESSFUL'] = 'Noticia enviada con exito';
$LNG['L_NEWS_FEATURED'] = 'Destacadas';
$LNG['L_NEWS_FRONTPAGE'] = 'Portada';
$LNG['L_NEWS_EDIT'] = 'Editar';
$LNG['L_NEWS_NEWLANG'] = 'Traducir';
$LNG['L_NEWS_CONFIRM_DEL'] = '¿Estas seguro que quieres eliminar?';
$LNG['L_NEWS_NEW_PAGE'] = 'Pagina nueva';
$LNG['L_NEWS_ERROR_WAITINGMOD'] = 'La noticia esta en espera de moderación';
$LNG['L_NEWS_E_RELATED'] = 'Enlace relacionado incorrecto o no funciona, corrijalo o dejelo en blanco';
$LNG['L_NEWS_E_SOURCE'] = 'Enlace fuente incorrecto o no funciona, corrijalo o dejelo en blanco';
$LNG['L_NEWS_E_ALREADY_TRANSLATE_ALL'] = 'La noticia ya esta traducida a todos los idiomas activos.';
$LNG['L_NEWS_NOT_EXIST'] = 'Noticia borrada o no existe';
$LNG['L_NEWS_EDIT_NEWS'] = 'Editar noticia';
$LNG['L_NEWS_UPDATE_SUCCESSFUL'] = 'Noticia actualizada con exito';
$LNG['L_NEWS_TRANSLATOR'] = 'Traductor';
$LNG['L_NEWS_TRANSLATE_SUCCESSFUL'] = 'Noticia traducida enviada con exito';
$LNG['L_NEWS_TRANSLATE_BY'] = 'Traducido por ';
$LNG['L_NEWS_CREATE_NEW_PAGE'] = 'Crear pagina nueva';
$LNG['L_NEWS_NOLANG'] = 'No existe en ese idioma';
$LNG['L_NEWS_DELETE_NOEXISTS'] = 'Noticia borrada o no existe';
$LNG['L_E_NOEDITACCESS'] = 'No tienes acceso de edicion';

/* NEW */

$LNG['L_NEWS_ALREADY_EXIST'] = 'Noticia ya existe';
$LNG['L_E_NOVIEWACCESS'] = 'No tienes permiso para ver esta noticia';
$LNG['L_NEWS_NOCATS'] = 'No existen categorias para enviar la noticia';
$LNG['L_NEWS_CHILDS'] = 'Incluir hijos';
$LNG['L_NEWS_TITLES'] = 'Titulos';
$LNG['L_NEWS_TITLESLEAD'] = 'Titulos / Entradilla';
$LNG['L_NEWS_FULLNEWS'] = 'Noticia completa';
$LNG['L_NEWS_DISPLAY_TYPE'] = 'Mostrar:';
$LNG['L_NEWS_LIMITS'] = 'Limite';
$LNG['L_NEWS_BLOCK_TITLE'] = 'Titulo del bloque';
$LNG['L_NEWS_E_SEC_NOEXISTS'] = 'Seccion no existe';
$LNG['L_NEWS_SEC_EMPTY'] = 'Seccion vacia';
$LNG['L_NEWS_SEC_EMPTY_TITLE'] = 'Oops';
$LNG['L_NEWS_DELETE_PAGE'] = 'Borrar pagina';
$LNG['L_NEWS_NOMULTILANG_SUPPORT'] = 'No hay soporte multilang';
$LNG['L_NEWS_SHOW_LANG'] = 'Mostrar noticias en';
$LNG['L_NEWS_CREATE'] = 'Creada';
$LNG['L_NEWS_UPDATE'] = 'Actualizada';
$LNG['L_NEWS_ASDRAFT'] = 'Guardar como borrador';
$LNG['L_NEWS_E_VIEW_DRAFT'] = 'No puede ver este borrador';
$LNG['L_NEWS_WARNING_DRAFT'] = 'Atención: esta noticia esta como borrador';
$LNG['L_NEWS_E_CANT_ACCESS'] = 'Sin accesso';
$LNG['L_NEWS_E_NODRAFTS'] = 'No tiene borradores';
$LNG['L_NEWS_DRAFTS'] = 'Borradores';
$LNG['L_NEWS_NPAGE'] = 'Numero de pagina';
$LNG['L_NEWS_NEWS_LINK'] = 'Enlace a noticia';

/* ACL */
$LNG['L_PERM_R_NEWS_FULL_ACCESS'] = 'Acceso administrador total de lectura';
$LNG['L_PERM_W_NEWS_FULL_ACCESS'] = 'Acceso administrador total lectura/escritura';
$LNG['L_PERM_R_VIEW_NEWS'] = 'ver noticias';
$LNG['L_PERM_R_CREATE_NEWS'] = 'crear noticias';
$LNG['L_PERM_W_ADD_SOURCE'] = 'añadir fuente';
$LNG['L_PERM_W_ADD_RELATED'] = 'añadir relacionada';
$LNG['L_PERM_W_FEATURE'] = 'marcar como destacada';
$LNG['L_PERM_W_EDIT'] = 'editar la noticia';
$LNG['L_PERM_W_TRANSLATE'] = 'traducir la noticia';
$LNG['L_PERM_W_OWN_TRANSLATE'] = 'Traducir su propia noticia';
$LNG['L_PERM_W_CHANGE_AUTHOR'] = 'cambiar autor';
$LNG['L_PERM_W_DELETE'] = 'borrar noticias';
$LNG['L_PERM_W_MODERATION'] = 'Aprobar noticias';
$LNG['L_PERM_W_FRONTPAGE'] = 'marcar como frontpage';
$LNG['L_PERM_W_EDIT_OWN'] = 'Editar paginas propias';
$LNG['L_PERM_W_DELETE_OWN'] = 'borrar noticias propias';
$LNG['L_PERM_W_ADD_PAGES'] = 'Añadir paginas a sus noticias';
