<?php
namespace tf3Framework;

class tffplugin
{
    public $name = 'Plugin';
    public $description = 'Beschreibung';
    public $author = 'Marcel Schindler';

    public function __construct()
    {
    }

    public function backend($params = false)
    {
        global $USER;
        if (!$USER->logged()) {
            Controller::redirect();
        }
    }

    public function frontend($params = false)
    {
    }
}
