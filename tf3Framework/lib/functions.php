<?php
#namespace tf3Framework;
if (!defined('DROOT')) {
    exit('hu?');
}

function loadStyle($file)
{
    $path = TPL_DIR;
    $old = 'assetsdir/';
    $new = WEB.$old;
    $contents = file_get_contents($path.$file);
    $contents = str_replace($old, $new, $contents);

    return '<style type="text/css">'.$contents.'</style>';
}

function showjson($data)
{
    $out = json_encode($data);
    $len = strlen($out);
    header('Content-Type:application/javascript');
    header('Content-Length:'.$len);
    echo $out;
    exit();
}

function includePlugin($string)
{
    preg_match_all('/\[(.*?)\]/', $string, $matches);
    if (isset($matches[0])) {
        foreach ($matches[1] as $match) {
            if (!empty($match)) {
                list($overhead, $class, $params) = explode(':', $match);
                $search = '['.$overhead.':'.$class.':'.$params.']';
                if (file_exists(DROOT.'/plugins/'.$class.'/code/plugin.php')) {
                    require_once DROOT.'/plugins/'.$class.'/code/plugin.php';
                    $instance = new $class();
                    ob_start();
                    $instance->frontend($params);
                    $result = ob_get_contents();
                    ob_end_clean();
                    $string = str_replace($search, $result, $string);
                }
            }
        }
    }

    return $string;
}

/**
 * @param $string
 *
 * @return string
 */
function clear($string)
{
    $out = strip_tags($string);
    $ret = substr($out, 0, 100).'...';

    return $ret;
}

// Handle USER Input / forced = pflichtfeld
/**
 * @param            $content
 * @param bool|false $type
 * @param bool|false $forced
 *
 * @return bool|float|int|string
 */
function _request($content, $type = false, $forced = false)
{
    global $SQL, $Request;
    $type = gettype($type);
    if (!isset($_REQUEST[$content])) {
        // Variable existiert nicht, also ist sie FALSCH!
        if ($type == 'string') {
            return false;
        } else {
            return 0;
        }
    }
    if ($forced and empty($_REQUEST[$content])) {
        // Feld ist leer
        return false;
    }

    // Handle and Escape Requestvars
    switch ($type) {
        case 'integer': {
                // Auf Integer überprüfen
                $ret = (int) $_REQUEST[$content];

                return (int) $ret;
                break;
            }
        case 'double': {
                // Auf FLOAT überprüfen
                $ret = (float) $_REQUEST[$content];

                return (float) $ret;
                break;
            }
        case 'string': {
                // User hat nen String übergeben
                // User müssen kein HTML übergeben, echt nicht (Javascript sowieso nicht)
                $ret = strip_tags($_REQUEST[$content]);
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

/**
 * @param $string
 *
 * @return bool
 */
function _mailchecker($string)
{
    if (strpos($string, '@') and strpos($string, '.')) {
        return true;
    }

    return false;
}

/**
 * @param $var
 * @param $type
 *
 * @return bool|float|int|string
 */
function _sanitize($var, $type)
{
    $_REQUEST['params'] = $var;

    return _request('params', $type);
}

/**
 * @return bool
 */
function _medialister()
{
    global $SQL;
    $albums = $SQL->fetch('SELECT * FROM '.DB.'albums');
    foreach ($albums as $r => $v) {
        $albums[$r]['url'] = urlencode($v['alb_title']);
    }

    return $albums;
}

/**
 * @return bool
 */
function footer_quickload()
{
    global $template;
    global $SQL;
    // Statischen Footer laden
    $cat = $SQL->fetch('SELECT cat_id from tff_blog_categories where handle="qload"');
    // Posts rausholen
    if (!$cat) {
        return false;
    }
    $qq = "SELECT blog_id from tff_blog_relations where cat_id={$cat[0]['cat_id']}";
    $tmp = $SQL->fetch($qq);
    $find = '';
    foreach ($tmp as $r => $v) {
        $find .= "{$v['blog_id']},";
    }
    if ($find == '') {
        return true;
    }
    $find = substr($find, 0, -1);
    $qqq = "SELECT * from tff_blog_posts where p_id in ($find) and vis=0 order by times desc";
    $out = $SQL->fetch($qqq);

    // Navigationstagcloud laden
    $tt = $SQL->fetch('SELECT cat_id,count(blog_id) anz from tff_blog_relations  group by cat_id order by anz desc limit 20 ');
    $in = '';
    foreach ($tt as $r => $v) {
        $in .= "{$v['cat_id']},";
    }
    $in = substr($in, 0, -1);
    $findcats = 'SELECT handle from tff_blog_categories where handle!="qload" and cat_id in ('.$in.') order by handle';
    //    echo $findcats;
    $cats = $SQL->fetch($findcats);
    $template->assign('blcats', $cats);
    $template->assign('quickload', $out);
}

/**
 * @return mixed
 */
function get_startpage()
{
    global $SQL;
    $sel = 'SELECT  from_unixtime(times) datum,month(from_unixtime(times)) monat,p_id, title,contents,headimg, vis, AU.user_name, times,  CA.handle category
            FROM tff_blog_posts AS BL
            JOIN tff_users AU on BL.author_id = AU.user_id
            JOIN tff_blog_relations RE ON RE.blog_id = BL.p_id
            JOIN tff_blog_categories CA ON RE.cat_id = CA.cat_id
            where vis = 1
            and headimg is not null
            GROUP BY RE.blog_id
            ORDER BY times desc
            LIMIT 0,4';
    $tmp = $SQL->fetch($sel);
    $x = 0;
    foreach ($tmp as $row => $data) {
        $out2[$x] = $data;

        $out2[$x]['contents'] = subclear($data['contents'], 300);
        $out2[$x]['contentsf'] = $data['contents'];
        $out2[$x]['titleurl'] = urlencode($data['title']);
        $out2[$x]['categoryurl'] = urlencode($data['category']);
        $out2[$x]['headimg'] = $data['headimg'];
        $out2[$x]['datum'] = $data['datum'];
        ++$x;
    }

    return $out2;
}

/**
 * @param     $string
 * @param int $length
 *
 * @return string
 */
function subclear($string, $length = 210)
{
    $tmp = strip_tags($string);
    $out = substr($tmp, 0, $length);

    return $out;
}

/**
 * @param $dir
 *
 * @return array
 */
function dirToArray($dir)
{
    $result = [];

    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, ['.', '..'])) {
            if (is_dir($dir.DIRECTORY_SEPARATOR.$value)) {
                $result[$value] = dirToArray($dir.DIRECTORY_SEPARATOR.$value);
            } else {
                $result[] = $value;
            }
        }
    }

    return $result;
}

/**
 * @param            $search
 * @param bool|false $parameters
 * @param            ...$more
 *
 * @return string
 */
function i18n($search, $parameters = false, ...$more)
{
    $langfile = 'de';
    if (isset($_SESSION['language'])) {
        $langfile = $_SESSION['language'];
    }
    include DROOT.DIRECTORY_SEPARATOR.'tf3Framework/languages'.DIRECTORY_SEPARATOR.$langfile.'.php';
    if (isset($lang[$search])) {
        return sprintf($lang[$search], $parameters, $more);
    }
    logger('Message not found:'.$search);

    return sprintf($search, $parameters, $more);
}

/**
 * @param $string
 */
function logger($string)
{
    $log = DROOT.DIRECTORY_SEPARATOR.'tf3Framework/log/tff3-log.log';
    $fp = fopen($log, 'a');
    fputs($fp, "\r\n".time().$string);
    fclose($fp);
}

/**
 * @param $string
 *
 * @return mixed
 */
function cleanFileUrl($string)
{
    $out = str_replace('/', '-', $string);
    $out = str_replace('\\', '-', $out);

    return $out;
}

/**
 * @param $file
 *
 * @return string
 */
function assets($file)
{
    global $template;
    $source = $template->getTemplateDir().$file;
    $filetype = pathinfo($file, PATHINFO_EXTENSION);
    $hashed = cleanFileUrl($file).'.'.$filetype;
    $destination = DROOT.'/public/assets/'.$hashed;
    if (is_file($source) && !is_file($destination) or (filemtime($destination) < filemtime($source))) {
        copy($source, $destination);

        return WEB.'assets/'.$hashed;
    } else {
        return WEB.'assets/'.$hashed;
    }
}

/**
 * @param $array
 */
function json_response($array)
{
    header('Content-Type: application/json');
    echo json_encode($array);
    exit();
}


function path($string = false)
{
    if ($string === false) {
        return WEB;
    }
    $string = implode('/', array_map('urlencode', explode('/', $string)));
    return WEB.$string;
}
