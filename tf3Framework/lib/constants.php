<?php
namespace tf3Framework;

if (!defined('DROOT')) {
    exit('hu?');
}
define('DODEBUG', false);

$inifile = file_exists(DROOT.'/tf3Framework/config/config.ini.dev.php') ? DROOT.'/tf3Framework/config/config.ini.dev.php' : DROOT.'/tf3Framework/config/config.ini.php';

$tmm = parse_ini_file($inifile, 1);

/* Prefix Database */
define('DB', $tmm['database']['prefix']);
define('PROJECT_TITLE', $tmm['http']['default_title']);
/* Main Global */
$TPL = ['TITLE' => PROJECT_TITLE];

/* verzeichnisse */
define('WEB', $tmm['http']['webroot']);

define('CLASSDIR', DROOT.'/tf3Framework/controllers/');
define('TPL_DIR', DROOT.DIRECTORY_SEPARATOR.'tf3Framework/templates/'.$tmm['view']['template'].'/');
define('TPL_FRONT', WEB.$tmm['view']['template']);
define('SMARTY_DIR', DROOT.'/ext/smarty/');
define('DS', DIRECTORY_SEPARATOR);
define('MP3ROOT', $tmm['http']['mp3root']);

/* Errors */
define('ERR_FILE_NOT_FOUND', 'Datei nicht gefunden');
define('ERR_METHOD_NOT_FOUND', 'Datei echt nicht gefunden');
define('ERR_UNKNOWN_PAGE', 'Unbekannte Seite');

define('ERR_NOBOXCHOSEN', 'You did not check a box');
define('ERR_INVALID_SESSION', 'Your session was lost');
define('ERR_OKAY', 'No errors found');
define('ERR_WRONGBOX', 'You checked the wrong box');
define('ERR_NONAME', 'You forgot your name');
define(
    'ERR_NOMAIL',
'Your mail-adress will not be visible. But you need to tell it to me.'
);
define('ERR_NOMESSAGE', 'Message is empty.');
define('ERR_INVALID_MAIL', 'Your mailadress is invalid');
define('ERR_NO_BLOG_CHOSEN', 'Somehow you forgot to view a blog');
define('ERR_UNKNOWN_CATEGORY', 'This category does not exist');

define('PROJECT_ADMIN', $tmm['security']['admin']);
define('ADMIN_MAIL', $tmm['security']['admin']);

define('SALT', $tmm['security']['salt']);
define('DB_CACHE', true);
define('TFF_MINIFY', true);

/* Tabellen für Blog */
define('_BLOG', DB.'blog_posts');
define('_COMMENTS', DB.'blog_comments');
define('_CATS', DB.'blog_categories');
define('_RELS', DB.'blog_relations');
/*
 * Responsible Persons
 */
define('_AUTHOR', DB.'users');

/* Tabellen für CMS */
define('_CMS_CAT', DB.'categories');
define('_CMS_POSTS', DB.'cmspages');

/* Setup some basics */
//setting basic configuration parameters
ini_set('session.name', 'bk');
ini_set('session.use_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
ini_set('magic_quotes_runtime', 0);
error_reporting(E_ALL);
if (DODEBUG == 0) {
    error_reporting(E_ERROR);
}
