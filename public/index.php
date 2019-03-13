<?php
namespace tf3Framework;
use tf3Framework\lib\Controller;
#exit();

ini_set('default_charset', 'UTF-8');
setlocale(LC_TIME, 'de_DE');
define('DROOT', dirname(dirname(__FILE__))); // Where am I?
define('INCL', DROOT.'/tf3Framework/lib/'); // Include Directory
if (!file_exists(DROOT.'/cache')) {
    mkdir(DROOT.'/cache') == false ? exit('Please create a cache-folder') : '';
}

include INCL.'bootstrap.php';
$dispatch = Controller::dispatch();
