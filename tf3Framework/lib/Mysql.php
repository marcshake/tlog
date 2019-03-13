<?php
namespace tf3Framework\lib;

if (!defined('DROOT')) {
    exit('hu?');
}

/**
 * Class DCMS_DB.
 */
class Mysql
{
    /**
     * @var bool
     */
    public $result = false;

    /**
     * @var string
     */
    public $cachefile = '';

    /**
     * @var array
     */
    public $cfcontent = [];

    /**
     * @var bool
     */
    public $q = false;

    /**
     * @var bool
     */
    public $cid = false;

    /**
     * @var bool
     */
    public $id = false;

    /**
     * @var bool
     */
    public $cachedir = false;

    /**
     * @var int
     */
    public $rows = 0;

    /**
     * @var int
     */
    public $cachetime = 120;

    /**
     * @var string
     */
    public $last_query = '';

    /**
     * @var bool
     */
    public $is_cached = DB_CACHE;

    /**
     * @var
     */
    public $mysql;

    /**
     * @param $svr
     * @param $usr
     * @param $pw
     *
     * @return bool
     */
    public function connect($svr, $usr, $pw)
    {
        $this->mysql = new \mysqli($svr, $usr, $pw);
        $err = $this->mysql->connect_errno;
        if ($err) {
            return false;
        }

        $cache = (DROOT.'/cache/');
        if (!file_exists($cache)) {
            mkdir($cache);
        }
        $d = date('d');
        if (!file_exists($cache.$d)) {
            mkdir($cache.$d);
        }
        $this->cachedir = $cache.$d.'/';

        return true;
    }

    /**
     * @param $path
     */
    public function setcachedir($path)
    {
        $this->cachedir = $path;
    }

    /**
     * @param bool|true $on
     */
    public function set_cache($on = true)
    {
        $this->is_cached = $on;
    }

    public function killcache_db()
    {
        $stamp = time() - 30;
        $this->query('delete from tff_sqlcache where stamp <='.$stamp);
    }

    /**
     * @param $query
     */
    public function query($query)
    {
        $this->last_query = $query;
        $this->mysql->query("set names 'utf8mb4'");
        $this->result = $this->mysql->query($query) or die($this->mysql->error.' '.$query);
        $this->rows = isset($this->result->num_rows) ? $this->result->num_rows : 0;
        $this->killcache();
    }

    public function killcache()
    {
        if ($this->is_cached == false) {
            return;
        }
        $stamp = time() - 7200;
        $dd = opendir($this->cachedir);
        while (false !== ($file = readdir($dd))) {
            if (!is_dir($this->cachedir.$file) and stristr($file, '.sqc')) {
                if (filemtime($this->cachedir.$file) <= $stamp) {
                    unlink($this->cachedir.$file);
                }
            }
        }
    }

    /**
     * @param           $query
     * @param bool|true $cache
     */
    public function fetch_db($query, $cache = true)
    {
    }

    /**
     * @return int
     */
    public function numrows()
    {
        return $this->rows;
    }

    /**
     * @param $database
     */
    public function changedb($database)
    {
        $this->mysql->select_db($database);
    }

    /**
     * @param $str
     */
    public function error($str)
    {
        echo $this->last_query;
        die($str);
    }

    public function field_names()
    {
    }

    /**
     * @return mixed
     */
    public function last_id()
    {
        $tmp = $this->fetch('SELECT last_insert_id() as id');

        return $tmp[0]['id'];
    }

    /**
     * @param           $query
     * @param bool|true $cache
     *
     * @return bool
     */
    public function fetch($query)
    {
        $cache = true;
        if ($this->is_cached == false) {
            $cache = false;
        }
        
        $q = $this->cachedir.md5(__DIR__.$query).'.sqc';
        $stamp = time() - 30;
        if ($cache and file_exists($q) and filemtime($q) > $stamp) {
            $ret = [];
            include $q;
            $this->rows = count($ret);

            return $ret;
        } else {
            $this->query($query);
            $ret = $this->dataset();
            if (sizeof($ret) > 0 and $cache) {
                $fp = fopen($q, 'w');
                $wr = '<?php '."\n".'/*qc:on*/'."\n".'$ret = '.(var_export($ret, true)).' ?>';
                fputs($fp, $wr);
                fclose($fp);
            }
        }

        return $ret;
    }

    /**
     * @return bool
     */
    public function dataset()
    {
        if ($this->result) {
            $x = 0;
            while ($data = $this->result->fetch_array(MYSQLI_ASSOC)) {
                $out[$x] = $data;
                ++$x;
            }

            return $out;
        }

        return false;
    }

    public function close()
    {
        $this->mysql->close();
    }

    /**
     * @return int
     */
    public function freeresult()
    {
        return 1;
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function escape($string)
    {
        return $this->mysql->real_escape_string($string);
    }
}
