<?php

/**
 * Class thumb.
 */
class thumb
{
    /**
     * @var string
     */
    public $file = '';
    /**
     * @var string
     */
    public $cachedir;
    /**
     * @var int
     */
    private $old;

    /**
     * thumb constructor.
     */
    public function __construct()
    {
        $d = date('d');
        $this->cachedir = DROOT.'/cache/'.$d.'/';
        if (!file_exists($this->cachedir)) {
            mkdir($this->cachedir);
        }
        $this->old = 24 * 60 * 60;
//        $this->garbage_collection();
    }

    /**
     * Lösche alte Thumbnails aus dem Cache.
     */
    private function garbage_collection()
    {
        $dd = opendir($this->cachedir);
        $stamp = time() - $this->old;
        while (false !== ($file = readdir($dd))) {
            if (!is_dir($this->cachedir.$file) and stristr($file, '.thumb.jpg')) {
                $action = (filemtime($this->cachedir.$file) <= $stamp) ? unlink($this->cachedir.$file)
                    : false;
            }
        }
    }

    /**
     * @return bool
     */
    public function doDefault()
    {
        return false;
    }

    /**
     * @param $url
     * @param $w
     * @param $p
     *
     * @return bool
     */
    public function fetch_image($url, $w, $p)
    {
        if (!stristr($url, WEB)) {
            controller::exit_app('Image not found');

            return false;
        }

        $tmp_img = $this->cachedir.md5($url.$w.$p).'.thumb.jpg';

        if (file_exists($tmp_img)) {
            $this->file = $tmp_img;

            return true;
        }
        // Gecachtes Thumbnail existiert nicht, also zieh es
        $ch = curl_init($url);
        $saveto = $tmp_img;
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $raw = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($saveto, 'x');
        fwrite($fp, $raw);
        fclose($fp);
        $this->file = $saveto;
        chmod($saveto, 0777);

        return false;
    }

    /**
     * @param $file
     * @param $timestamp
     */
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

    /**
     * @param int $params
     */
    public function doImg($params = 0)
    {
        $x = count($params);

        $widths = $params[0];
        $heights = $params[1];
        unset($params[0]);
        unset($params[1]);
        $images = implode('/', $params);
        $cached_result = false;
        $this->fetch_image($images, $widths, $heights);
        if (!$cached_result) {
            $extension = substr($images, -3, strlen($images));
            switch ($extension) {
                case 'gif':
                    $image = imagecreatefromgif($this->file);
                    break;
                case 'png':
                    $image = imagecreatefrompng($this->file);
                    break;
                default:
                    $image = imagecreatefromjpeg($this->file);
                    break;
            }
            
            $thumb_width = (int) $widths;
            $thumb_height = (int) $heights;
            $width = imagesx($image);
            $height = imagesy($image);
            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            if ($original_aspect >= $thumb_aspect) {
                // If image is wider than thumbnail (in aspect ratio sense)
                $new_height = $thumb_height;
                $new_width = $width / ($height / $thumb_height);
            } else {
                // If the thumbnail is wider than the image
                $new_width = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }
            $thumb = $extension != 'gif' ? imagecreatetruecolor(
                $thumb_width,
                $thumb_height
            ) : imagecreate($thumb_width, $thumb_height);
            // Resize and crop
            imagecopyresampled(
                $thumb,
                $image,
                0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0,
                0,
                $new_width,
                $new_height,
                $width,
                $height
            );
            switch ($extension) {
                case 'gif':
                    imagegif($thumb, $this->file);
                    break;
                case 'png':
                    imagejpeg($thumb, $this->file, 80);
                    break;
                default:
                    imagejpeg($thumb, $this->file, 80);
                    break;
            }
        }
        $extension = substr($images, -3, strlen($images));
        $shell = 'convert -strip -interlace Plane -quality 85% '.escapeshellarg($this->file).' '.escapeshellarg($this->file);
        exec($shell);
        switch ($extension) {
            case 'jpg':
                header('Content-Type:image/jpeg');

                break;
            case 'gif':
                header('Content-Type:image/gif');

                break;
            case 'png':

                header('Content-Type: image/png');
                
                break;
        }
        session_cache_limiter('none');
        $seconds_to_cache = 60 * 60 * 24 * 30; // 30 days
        header('Pragma: cache');
        header('Cache-Control: max-age='.$seconds_to_cache);
        $this->set_eTagHeaders($this->file, filemtime($this->file));
        echo file_get_contents($this->file);
        exit();
    }
}
