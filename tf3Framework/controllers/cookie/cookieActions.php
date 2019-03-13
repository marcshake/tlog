<?php

class cookie
{
    public function doDefault()
    {
        return true;
    }

    public function doDismiss()
    {
        $_SESSION['COOKIE_DISMISS'] = true;
        Controller::Redirect();
    }
}
