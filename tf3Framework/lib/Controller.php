<?php
namespace tf3Framework\lib;

if (!defined('DROOT')) {
    exit('hu?');
}

/**
 * TFF Framework. Simple as possible Framework with not many features but
 * easy to maintain.
 *
 * @author Marcel Schindler <info@trancefish.de>
 */
class Controller
{
    /**
     * my funny controller.
     */
    const VERSION = 1;

    /**
     * @var
     */
    private static $mainPath;
    public static $route = false;

    /**
     * Disable Constructor.
     *
     * @return false
     */
    public function __construct()
    {
        return false;
    }

    /**
     * Handle Redirects.
     */
    public static function redirect($string = false)
    {
        header('location:'.WEB.$string);
        exit();
    }

    public static function splitstring()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?'); // Get to GET-Params
        $requestURI = explode('/', $path);
        $scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
        for ($i = 0; $i < sizeof($scriptName); ++$i) {
            if ($requestURI[$i] == $scriptName[$i]) {
                unset($requestURI[$i]);
            }
        }
        $command = array_values($requestURI);

        // QueryString auseinanderdröseln
        $qs = @parse_url($_SERVER['REQUEST_URI']) or self::pagenotfound_handler('WTF?');

        if (isset($qs['query'])) {
            $get = explode('&', $qs['query']);
            foreach ($get as $r => $v) {
                list($key, $val) = isset($v[0]) ? (explode('=', $v)) : false;
                {
                    $_REQUEST[$key] = $val;
                }
            }
        }
        /* Set Actions */
        $page = isset($command[0]) ? strlen(trim($command[0])) > 0 ? $command[0] : 'home' : 'home';
        $action = isset($command[1]) ? strlen(trim($command[1])) > 0 ? $command[1] : 'default' : 'default';
        unset($command[0]);
        unset($command[1]);
        $params = array_values($command); // Additional-Params
        self::$mainPath = ['page' => $page, 'action' => $action, 'params' => $params];
    }

    /**
     * @param bool|false $string
     */
    public static function pagenotfound_handler($string = false)
    {
        self::exit_app($string);
    }

    /**
     * @param $msg
     */
    public static function exit_app($msg)
    {
        global $template;
        header('HTTP/1.0 404 Not Found');
        $template->assign('errormessage', $msg);
        $template->display('404.php');

        exit(false);
    }

    public static function dispatch()
    {
        global $template;
        self::splitstring();
        $data = self::$mainPath;
        $class = isset($data['page']) ? $data['page'] : '';
        $method = $data['action'];
        $params = $data['params'];
        self::defaultModules();
        $fname = (strtolower($class).'/'.strtolower($class).'Actions.php');
        if (strpos($class, '.') or strpos($class, '/') or strpos($class, '\\')) {
            self::exit_app(ERR_FILE_NOT_FOUND);
        }
        if (!file_exists(CLASSDIR.($fname))) {
            $fname = 'cms/cmsActions.php';
            $class = 'cms';
            $tmp = $data['page'];
            $action = $data['action'] != 'default' ? $data['action'] : 'index';
            $data['action'] = $data['page'];
            $data['params'][0] = $action;
        }
        include CLASSDIR.$fname;
        $launch = new $class();
        if ($class == 'cms' || $class == 'assetsdir') {
            $params = $data;
            $method = 'Default';
        }
        $actionMethod = 'do'.ucfirst($method);
        $rest_url = null;
        if (is_array($params)) {
            $rest_url = implode('/', $params);
        }
        self::$route = $class;
        $template->setRoute(self::$route);
        $template->assign('MY_LOCATION', WEB.$class.'/'.$method.'/'.$rest_url);
        $template->assign('I_AM_AT', WEB.$class.'/'.$method.'/'.$rest_url);
        if (method_exists($launch, $actionMethod)) {
            $launch->$actionMethod($params);
        } else {
            self::exit_app('Seite nicht gefunden');
        }
        if (file_exists(TPL_DIR.$class.'.php')) {
            #            header('X-Frame-Options: DENY');
            $template->display($class.'.php');
        }
    }

    /**
     * Dummy-Function do load other scripts.
     *
     * @return false
     */
    public static function defaultModules()
    {
        self::check_database();

        return false;
    }

    /**
     * @return bool
     */
    public static function check_database()
    {
        global $SQL;
        $stamp = filemtime(__FILE__);
        $db_version = $SQL->fetch('SELECT profile_data from tff_additional_data where uid='.self::VERSION);
        if (!$db_version) {
            $SQL->query('INSERT INTO tff_additional_data (uid,profile_data) VALUES ('.self::VERSION.','.$stamp.')');

            return true;
        }
        if ($db_version < $stamp) {
        }

        return false;
    }

    /**
     * @return mixed
     */
    public static function getMainPath()
    {
        return self::$mainPath;
    }

    /**
     * @param mixed $mainPath
     */
    public static function setMainPath($mainPath)
    {
        self::$mainPath = $mainPath;
    }

    public static function loadMenus()
    {
        global $SQL;
        $items = '';
        $tmp = $SQL->fetch("SELECT contents from tff_cmspages where cat_id=0 and handle='mainMenu'");
        if ($tmp) {
            $items = $tmp[0]['contents'];
        }
        return $items;
    }
}
