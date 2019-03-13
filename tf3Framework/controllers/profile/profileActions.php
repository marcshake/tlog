<?php

use tf3Framework\lib\Controller;

class profile
{
    public function doDefault()
    {
        global $template;
        if (isset($_SESSION['check_email'])) {
            unset($_SESSION['check_email']);
            $template->assign(
                    'check_mail',
                    'style="border:1px solid red;color:red;background:white"'
            );
        }
        if (isset($_SESSION['check_pw'])) {
            unset($_SESSION['check_pw']);
            $template->assign(
                    'check_pw',
                    'style="border:1px solid red;color:red;background:white"'
            );
        }

        return true;
    }

    public function doLogin($params)
    {
        global $USER;
        $USER->login();
        $redirect = 'admin';
        $go = _request('go', '');
        if (isset($params[0]) and ! empty($params[0])) {
            $redirect = $params[0];
        }
        if ($USER->get_level() == 1) {
            Controller::redirect($redirect);
        }
        Controller::redirect($go);
    }

    public function doFailure()
    {
        global $template;
        $template->assign('Fail', true);
    }

    public function doRegister()
    {
        return false;
    }

    public function notify_user()
    {
        // Dummyfunktion, um Benutzer zu benachrichtigen
    }

    public function email($string)
    {
        return _mailchecker($string);
    }

    public function doSuccess()
    {
        global $template;
        $template->assign('success', 1);
    }

    private function exists($name)
    {
        global $SQL;
        $tmp = $SQL->fetch("SELECT user_name from tff_users where user_name='{$name}'");

        return $tmp;
    }

    public function doLogout($params = false)
    {
        global $USER;
        $redirect = 'admin';
        if (isset($params[0]) and ! empty($params[0])) {
            $redirect = $params[0];
        }
        $USER->logout();
        controller::redirect($redirect);
    }
}
