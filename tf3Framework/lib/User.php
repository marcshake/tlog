<?php
namespace tf3Framework\lib;

if (!defined('DROOT')) {
    exit('hu?');
}

/**
 * Class user.
 */
class User
{
    /**
     * @var bool
     */
    public $username = false;
    /**
     * @var bool
     */
    public $logged;
    /**
     * @var int
     */
    public $level = 0;
    /**
     * @var
     */
    private $salt = SALT;

    public function __construct()
    {
        $this->username = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : false;
        $this->logged = isset($_SESSION['user_logged_in']);
    }

    /**
     * @return bool
     */
    public function logged()
    {
        $this->username = isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : false;
        $this->logged = isset($_SESSION['user_logged_in']);

        return $this->logged;
    }

    /**
     * @return string
     */
    public function crsf()
    {
        return isset($_SESSION['token']) ? $_SESSION['token'] : $this->create_token();
    }

    /**
     * @param $username
     * @param $password
     */
    public function register($username, $password)
    {
        global $SQL;
        $SQL->query('INSERT into tff_users (user_name,password,lvl,user_mail)'
            ." values ('{$username}','{$password}',2,'{$username}')");
    }

    public function logout()
    {
        session_destroy();
    }

    /**
     * @return bool
     */
    public function login()
    {
        global $SQL;
        $_SESSION['fp'] = isset($_SESSION['fp']) ? $_SESSION['fp'] : md5($this->salt.$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);

        $username = _request('uname', '');
        $password = _request('upass', '');
        $tok = _request('tok', '');
        if ($tok != $_SESSION['token']) {
            Controller::exit_app('Login Failed');
        }
        if ($username == '') {
            Controller::exit_app('Kein Benutzername angegeben');
        }
        if ($password == '') {
            Controller::exit_app('Kein Passwort angegeben');
        }
        $pw = md5($this->salt.$password);
        // New Login Method
        $userdata = $SQL->fetch('SELECT * FROM '.DB."users where user_name='".$username."'");
        if (password_verify($this->salt.$password, $userdata[0]['hash'])) {
            $this->create_session($userdata[0]);
            $_SESSION['csrf'] = uniqid('', true);

            return true;
        }
        $suche = 'SELECT * FROM '.DB.'users where user_name="'.$username.'" and password="'.$pw.'" LIMIT 1';
        $userdata = $SQL->fetch($suche, false);
        if (!($userdata)) {
            Controller::exit_app('Benutzername und/oder Passwort falsch. Vergessen?'.$this->show_reminderlink());
        } else {
            $this->update_login($password, $userdata[0]['user_id']);
            $this->create_session($userdata[0]);
        }
    }

    // Use new Login Method
    /**
     * @param $password
     * @param $id
     */
    public function update_login($password, $id)
    {
        global $SQL;
        $hash = password_hash($this->salt.$password, PASSWORD_DEFAULT);
        $SQL->query('UPDATE '.DB.'users set password=null, hash="'.$hash.'" where user_id='.$id);
    }

    /**
     * @return string
     */
    public function show_reminderlink()
    {
        $html = '<br/><a href="'.WEB.'profile/resend_pw">Passwort erneut zuschicken</a>';

        return $html;
    }

    /**
     * @param $userdata
     */
    public function create_session($userdata)
    {
        session_regenerate_id();    // User ist eingelogged, kriegt ne neue Session.
        $this->create_token();
        $_SESSION['user_logged_in'] = true;
        $_SESSION['hash'] = md5($this->salt.$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
        $_SESSION['USERNAME'] = $userdata['user_name'];
        $_SESSION['uid'] = $userdata['user_id'];
    }

    public function get_levels()
    {
        $levels = [
            0 => 'Jeder',
            1 => 'Administratoren',
            2 => 'Redaktion',
            3 => 'Mitglieder',
        ];

        return $levels;
    }

    /**
     * @return string
     */
    public function create_token()
    {
        if (isset($_SESSION['token'])) {
            return $_SESSION['token'];
        }
        $token = md5(uniqid(mt_rand(), true));
        $_SESSION['token'] = $token;

        return $token;
    }

    /**
     * @return bool
     */
    public function get_level()
    {
        global $SQL;
        if (!isset($_SESSION['uid'])) {
            return false;
        }
        $uid = $_SESSION['uid'];
        $memb = $SQL->escape($_SESSION['USERNAME']);
        $tmp = $SQL->fetch('SELECT lvl from tff_users where user_id='.$uid.' and user_name="'.$memb.'" ');
        if (!$tmp) {
            return false;
        }

        return $tmp[0]['lvl'];
    }

    public function getName()
    {
        return $_SESSION['USERNAME'];
    }

    public function get_data()
    {
    }

    /**
     * @return mixed
     */
    public function get_id()
    {
        $hash = md5($this->salt.$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
        if ($_SESSION['hash'] == $hash) {
            return $_SESSION['uid'];
        }
    }
}
