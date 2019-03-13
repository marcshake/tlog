<?php
namespace tf3Framework\lib;

/**
 * Steuert letztendlich die Darstellungsschicht. Keine Template-Engine in dem Sinne mehr
 * notwendig. Ist egal, ob man eine META-Sprache lernen muss oder ob man hier
 * direkt PHP schreibt.
 *
 * @author Marcel Schindler <info@trancefish.de>
 */
class View
{
    public static $route;
    /**
     * @var
     */
    public $view;
    /**
     * @var string
     */
    public $path;

    public function setRoute($route)
    {
        self::$route = $route;
    }

    public function __construct()
    {
        $this->path = TPL_DIR;
    }

    /**
     * @param $new
     */
    public function setTemplateDir($new)
    {
        $this->path = $new;
    }

    /**
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->path;
    }

    /**
     * @param $file
     */
    public function fetch($file)
    {
        $this->display($file);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function display($file)
    {
        include $this->path.$file;

        return true;
    }

    /**
     * @param $var
     * @param $value
     *
     * @return bool
     */
    public function assign($var, $value)
    {
        if (!empty($var) and !empty($value)) {
            $this->view[$var] = $value;

            return true;
        } else {
            $this->view[$var] = false;

            return false;
        }
    }
}
