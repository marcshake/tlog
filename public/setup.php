<?php
define('DROOT', dirname(dirname(__FILE__)));
use tf3Framework\lib\Mysql;

if(!file_exists(DROOT.'/vendor/autoload.php')) 
{
    exit('Please start Composer first');
}
session_Start();
#error_reporting(E_NONE);

$step = isset($_REQUEST['step']) ? (int) $_REQUEST['step'] : 0;
$menuitems = ['Willkommen','Systemcheck','MySQL-Server','Datenbank wählen','SQL-Import','Benutzeranlage','Konfiguration downloaden','Abschlusscheck','Fertig'];
$setup = new setup();

$setup->handleRequest();
class setup
{
    private $path;
    private $errors;
    private $requirements;
    private $errormessage;
    private $SQL;
    private $databases;
    private $hash;
    public function __construct()
    {
        $this->requirements = [];
        $this->path= DROOT;
        $this->errors = 0;
        $this->databases = false;
        include $this->path.'/tf3Framework/lib/autoload.php';
    }
    public function getErrormessages()
    {
        return $this->errormessage;
    }
    private function checkSQL()
    {
        $this->requirements['MySQLi'] = true;
        if (!function_exists('mysqli_connect')) {
            $this->requirements['MySQLi'] = false;
            $this->errormessage[] = 'Du musst die MySQLi-Erweiterungen in PHP benutzen. Bitte wende dich an deinen Provider.';
            return 1;
        }
        return 0;
    }
    private function phpV()
    {
        $this->requirements['PHP-Version'] = true;
        if (version_compare(phpversion(), '7', '<')) {
            $this->requirements['PHP-Version'] = false;
            $this->errormessage[] = 'Es wird PHP7 vorausgesetzt.';

            return 1;
        }
        return 0;
    }
    public function systemCheck()
    {
        $this->errors = 0;
        $this->errors += $this->checkBower();
        $this->errors += $this->checkSQL();
        $this->errors += $this->phpV();
        //$this->errors += $this->checkComposer();
        return $this->requirements;
    }

    private function checkComposer() {
        if(!file_exists(DROOT.'/vendor/autoload.php')) {
            $this->errormessage[] = 'Bitte führe Composer install aus';
            return 0;
            
        }
        return 1;
    }

    public function getDatabases()
    {
        return $this->databases;
    }

    public function getServer()
    {
        return isset($_SESSION['server'])?$_SESSION['server']:false;
    }
    public function getUser()
    {
        return isset($_SESSION['user'])?$_SESSION['user']:false;
    }
    private function saveDSN()
    {
        $SERVER = [];
        $SERVER['user'] = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
        $SERVER['server'] = filter_input(INPUT_POST, 'server', FILTER_SANITIZE_STRING);
        $SERVER['pass'] = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
        $_SESSION['user'] = $SERVER['user'];
        $_SESSION['server'] = $SERVER['server'];
        $this->SQL = new Mysql();
        $connection = $this->SQL->connect($SERVER['server'], $SERVER['user'], $SERVER['pass']);
        if ($connection==false) {
            $this->errors=1;
            $this->errormessage[] = 'Konnte keine Verbindung aufbauen, prüfe noch einmal deine Zugangsdaten';
            return false;
        }
        $_SESSION['pass'] = $SERVER['pass'];
        $this->show_databases();
        return true;
    }

    public function handleRequest()
    {
        $goTest = isset($_REQUEST['goTest']);
        $goImport = isset($_REQUEST['goImport']);
        $goInstall = isset($_REQUEST['goInstall']);
        $goCreate = isset($_REQUEST['createUser']);
            // I know this is dirty.
            if ((int)$_REQUEST['step']>=3) {
                $this->SQL = new Mysql;
                $this->SQL->connect($_SESSION['server'], $_SESSION['user'], $_SESSION['pass']);
                $this->show_databases();
            }
        if ($goTest) {
            $this->saveDSN();
        }

        if ($goInstall) {
            $database = filter_input(INPUT_POST, 'database', FILTER_SANITIZE_STRING);
            $this->SQL->changedb($database);
            $this->loadSQL();
            $_SESSION['database'] = $database;
        }
        if($goCreate) {
            $this->createUser();
        }
    }

    private function setSalt() {
    // It's not useful anymore, cause we create password-hashes anyway but nevertheless...
    if(isset($_SESSION['salt'])) {
        $this->hash = $_SESSION['salt'];
           return true;
        }
        $random = '';
        $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($string);
        for($x=0;$x<=6;$x++) {
            $int = rand(0,$len);
            $random.=$string[$int];
        }
        $_SESSION['salt'] = $random;
        $this->hash = $random;
        return $random;

    }

    private function createUser() {
        $this->setSalt();
        $user = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
        $pass = md5($this->hash.filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
        $this->SQL->connect($_SESSION['server'], $_SESSION['user'], $_SESSION['pass']);
        $this->SQL->changedb($_SESSION['database']);
        $this->SQL->query("INSERT INTO tff_users(`user_name`,`password`,`lvl`) values ('{$user}','{$pass}',1)");
        
    }

    private function loadSQL()
    {
        $lines = file($this->path.'/tf3Framework/SQL/structure.sql');
        $tmp = '';
        foreach ($lines as $line) {
            if (substr($line, 0, 2)=='--' || $line=='') {
                continue;
            }
            $tmp .= $line;

            if (substr(trim($line), -1, 1)==';') {
                $this->SQL->query($tmp);
                $tmp = '';
            }
        }
    }

    private function show_databases()
    {
        $tmp = $this->SQL->fetch("SHOW databases");
        if ($tmp) {
            $this->databases = $tmp;
        }
    }

    public function showIni() {
        $ini = parse_ini_file($this->path.'/tf3Framework/config/config.ini.dist',true);
        $ini['database']['server'] = $_SESSION['server'];
        $ini['database']['user'] = $_SESSION['user'];
        $ini['database']['pw'] = $_SESSION['pass'];
        $ini['database']['database'] = $_SESSION['database'];
        $ini['http']['webroot'] = '//'.$_SERVER['HTTP_HOST'].'/';
        $ini['security']['salt'] = $_SESSION['salt'];
        $out = '';
        foreach($ini as $section => $keys) {
            $out .= '['.$section.']'.PHP_EOL;
            foreach($keys as $k => $V) {
                $out .=$k.' = '.$V.PHP_EOL;
            }

        }
        return $out;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    private function checkBower()
    {
        $this->requirements['Bowerkomponenten'] = true;
        if (!file_exists($this->path.'/public/js/bower_components/')) {
            $this->requirements['Bowerkomponenten'] = false;
            $this->errormessage[] = 'Bitte installiere noch die Bower-Komponenten. Geh auf <a href="https://bower.io/">Bower.io</a> und mache dann ein <kbd>bower install</kbd> im Ordner public/js';

            return 1;
        }
        return 0;
    }
    public function loadCSS()
    {
        $css = file_get_contents(DROOT.'/tf3Framework/templates/setup/styles.css');
        return '<style>'.$css.'</style>';
    }
}

    include DROOT.'/tf3Framework/templates/setup/setup.php';
