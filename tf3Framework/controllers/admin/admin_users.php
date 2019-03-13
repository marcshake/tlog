<?php

class admin_users
{
    public function __construct()
    {
        global $SQL, $template;
        $template->assign('userlist', $SQL->fetch('SELECT * FROM tff_users'));
    }

    public function doUsers($params = false)
    {
        switch ($params[0]) {
            case 'edit':
                $this->editor($params[1]);
                break;
        }

        return true;
    }

    public function editor($id)
    {
        global $SQL;
        global $template;
        $id = (int) $id;
        $template->assign('useredit', $SQL->fetch("SELECT * from tff_users where user_id=$id "));
    }
}
$admin_users = new admin_users();
