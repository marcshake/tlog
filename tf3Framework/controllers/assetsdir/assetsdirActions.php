<?php

/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 20.10.2017
 * Time: 16:41.
 */
class assetsdir
{
    public function doDefault($params)
    {
        global $template;
        $iPath = $template->getTemplateDir();
        $file = $params['action'];
        $path = $iPath.$file;
        session_cache_limiter('none');
        $seconds_to_cache = 60 * 60 * 24 * 30; // 30 days
        header('Pragma: cache');
        header('Cache-Control: max-age='.$seconds_to_cache);
        $this->set_eTagHeaders($path, filemtime($path));
        $mime = mime_content_type($path);
        $ext = strtolower(array_pop(explode('.', $path)));
        if ($ext == 'css') {
            $mime = 'text/css';
        }
        header('Content-Type:'.$mime);
        echo file_get_contents($path);
    }

    public function set_eTagHeaders($file, $timestamp)
    {
        $gmt_mTime = gmdate('r', $timestamp);

        //header('Cache-Control: public');
        header('ETag: "'.md5($timestamp.$file).'"');
        header('Last-Modified: '.$gmt_mTime);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mTime || str_replace(
                '"',
                    '',
                stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])
            ) == md5($timestamp.$file)
            ) {
                header('HTTP/1.1 304 Not Modified');
                exit();
            }
        }
    }
}
