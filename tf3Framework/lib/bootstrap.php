<?php
namespace tf3Framework;

use tf3Framework\lib\View;
use tf3Framework\lib\Mysql;
use tf3Framework\lib\Controller;
use tf3Framework\lib\User;

require_once INCL.'/autoload.php';
require_once INCL.'/constants.php';

session_start();
if (!defined('DROOT')) {
    exit('hu?');
}
require_once INCL.'/functions.php';
require_once INCL.'/tffplugin.php';
$template = new View();
$SQL = new Mysql();
$SQL->connect($tmm['database']['server'], $tmm['database']['user'], $tmm['database']['pw']) or die('Datenbank nicht erreichbar');
$SQL->changedb($tmm['database']['database']);
$SQL->cachetime = 1;
$USER = new User;

if (isset($_REQUEST['ajax'])) {
    $template->assign('ajax', true);
}
$template->assign('PAGEROOT', WEB);
$template->assign('TPL_FRONT', TPL_FRONT);
$template->assign('SID', '?'.SID);
$template->assign('sessionname', session_name());
$template->assign('sessionid', session_id());
$template->assign('MP3_ROOT', MP3ROOT);
$template->assign('USER', $USER);
$template->assign('EDITABLE', $USER->get_level() == 1 ? true : false);
if (isset($tmm['features']['use_ajax']) && $tmm['features']['use_ajax'] == 1) {
    $template->assign('ajax_navi', 1);
}
footer_quickload();
