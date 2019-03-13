<?php
namespace tf3Framework;

class request
{
    private $get;
    private $post;
    private $session;
    private $cookie;
    private $type;
    public function __construct($get, $post, $session, $cookie)
    {
        $this->get = $get;
        $this->post = $post;
        $this->session = $session;
        $this->cookie = $cookie;
    }
    private function sanitize($val)
    {
        global $SQL;
        $type = gettype($this->type);
        switch ($type) {
            case 'integer': {
                    // Auf Integer überprüfen
                    $ret = (int) $val;

                    return (int) $ret;
                    break;
                }
            case 'double': {
                    // Auf FLOAT überprüfen
                    $ret = (float) $val;

                    return $ret;
                    break;
                }
            case 'string': {
                    // User hat nen String übergeben
                    // User müssen kein HTML übergeben, echt nicht (Javascript sowieso nicht)
                    $ret = strip_tags($val);
                    // Evtl. Slashes escapen - hilft gegen Injections
                    $ret = $SQL->escape($ret);
                    $ret = trim($ret);
                    // Steht nun überhaupt noch was drin? - Falls nö, kick it.
                    $ret = strlen($ret) == 0 ? false : $ret;

                    return (string) $ret;
                    break;
                }
            default: {
                    break;
                }
        }

        return false;
    }

    public function getGet()
    {
        if (!isset($this->get[$key])) {
            return false;
        }
        $this->type = $type;
        $val = $this->get[$key];

        return $this->sanitize($val);
    }
    public function setGet($val)
    {
        $this->get = $val;
    }
    public function getPost($key, $type)
    {
        if (!isset($this->post[$key])) {
            return false;
        }
        $this->type = $type;
        $val = $this->post[$key];

        return $this->sanitize($val);
    }
    public function setPost($val)
    {
        $this->post = $val;
    }
    public function getSession()
    {
        return $this->session;
    }
    public function setSession($val)
    {
        $this->session = $val;
    }
    public function getCookie()
    {
        return $this->cookie;
    }
    public function setCookie($val)
    {
        $this->cookie = $val;
    }
}

$Request = new Request($_GET, $_POST, $_SESSION, $_COOKIE);
