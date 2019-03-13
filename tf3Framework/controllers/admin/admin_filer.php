<?php
use tf3Framework\lib\Controller;

error_reporting(E_ALL);
/**
 * Class adminfiler.
 */
class adminfiler
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $whitelist;

    /**
     * @param $params
     */
    public function doFiler($params)
    {
        $action = isset($params[0]) ? $params[0] : 'browse';
        switch ($action) {
            case 'preview':
                $this->preview($params);
                break;

            case 'usage':
                $this->show_usage($params);
                break;
            case 'browse':
                $this->browse();
                break;
            case 'upload':
                $this->handle_upload();
                break;
            case 'delete':
                $this->delete($params);
                break;

            default:
                $this->browse();
                break;
        }
    }

    public function preview($params)
    {
        global $template;
        $image = _request('image', '');
        $id = _request('imageID', 0);
        $path = str_replace(WEB, '', $image);
        $file_path = DROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$path;
        if (file_exists($file_path)) {
            $imagedata = getimagesize($file_path);
            $bild['filename'] = $image;
            $bild['size'] = filesize($file_path);
            $bild['date'] = filemtime($file_path);
            $bild['id'] = $id;
            $data = ['imagedata' => $imagedata,
                'filedata' => $bild,
            ];
        } else {
            exit('Datei nicht gefunden');
        }

        $template->assign('preview', $data);
    }

    /**
     * @param $params
     */
    public function show_usage($params)
    {
        $id = (int) $params[1];
        $files = $this->browse();
        $thefile = ($files[$id]);
        $out = $thefile['usage'];
        if (is_array($out)) {
            $html = '<ul>';
            foreach ($out as $row => $data) {
                $html .= '<li>'.$data['title'].'</li>';
            }
            $html .= '</ul>';
            echo $html;
            exit();
        }
        echo '';
        exit();
    }

    /**
     * @param $params
     */
    public function delete($params)
    {
        $id = (int) $params[1];
        $files = $this->browse();
        $thefile = ($files[$id]);
        unlink(DROOT.'/public/web/_media/'.basename($thefile['filename_info']));
        Controller::redirect('admin/filer');
    }

    public function __construct()
    {
        $this->path = DROOT.'/public/web/_media';
        if (!file_exists(DROOT.'/public/web') || !file_exists(DROOT.'/public/web/_media')) {
            mkdir(DROOT.'/public/web', 1);
            mkdir(DROOT.'/public/web/_media', 1);
        }
        $this->whitelist = ['mp3', 'jpg', 'jpeg', 'gif', 'png', 'pdf', 'zip',
            'ogg', 'xm', 'it', 'mod', 'rar', 'mp4', 'rar', 'ods', 'odt', ];
    }

    /**
     * Prüfung, in welchen Datensätzen eine Datei hinterlegt ist.
     *
     * @param type $filename
     */
    private function usage_index($filename)
    {
        global $SQL;
        $cachedir = DROOT.'/cache/';
        $index = md5($filename);
        $indexfile = $cachedir.$index.'.idx';
        if (file_exists($indexfile)) {
            // Lese direkt die Index-Datei aus und gib ein Array zurück
            $tmp = file_get_contents($indexfile);
            $out = unserialize($tmp);

            return $out;
        }
        $posts = $SQL->fetch("SELECT p_id,title FROM tff_blog_posts where headimg='$filename' OR contents like '%{$filename}%'");
        if (is_array($posts)) {
            $fp = fopen($indexfile, 'w+');
            fputs($fp, serialize($posts));
            fclose($fp);

            return $posts;
        }

        return false;
    }


    private function make_thumb($src, $dest, $desired_width)
    {
        $src = $this->path.'/'.$src;
        $dest = $this->path.'/thumb_'.$dest;
        if (file_exists($dest)) {
            return true;
        }

        /* read the source image */
        $x = pathinfo($this->path.DS.$src);
        $x['extension'] = strtolower($x['extension']);
        switch ($x['extension']) {
            case 'jpeg':
            case 'jpg':
            $source_image = imagecreatefromjpeg($src);
            $dest.='.jpg';

            break;
            case 'gif':
                $source_image = imagecreatefromgif($src);
                $dest.='.jpg';
                break;
                case 'png':
                    $source_image = imagecreatefrompng($src);
                    $dest.='.jpg';
                    break;
    

        }

        #$source_image = imagecreatefromjpeg($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        
        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));
        
        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        
        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        
        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
    }

    /**
     * @return bool
     */
    public function browse()
    {
        global $template;
        $scanned_directory = array_diff(scandir($this->path), array('..', '.'));
        $x = 0;
        $files = false;
        rsort($scanned_directory);
        foreach ($scanned_directory as $row => $data) {
            $tmp = pathinfo($data);
            if ($tmp['extension']=='jpg') {
                $files[$x]['name'] = $data;
                $files[$x]['thumbnail'] = $data;
            }
            
            ++$x;
        }
        if (!$scanned_directory) {
            return false;
        }
        $template->assign('files', $files);
        $template->assign('whitelist', $this->whitelist);

        return $files;
    }

    public function cleanName($fileName)
    {
        return preg_replace("/[^a-z0-9\.]/", '', strtolower($fileName));
    }

    public function handle_upload()
    {
        global $USER;
        if (isset($_FILES['newfile'])) {
            $t = count($_FILES['newfile']);
        } else {
            return false;
        }
        for ($x = 0; $x < $t; ++$x) {
            //            $filename = $_FILES['newfile']['name'][$x];
            if (!isset($_FILES['newfile']['name'][$x])) {
                Controller::redirect('admin/filer');
                exit;
            }
            $filename = $_FILES['newfile']['name'][$x];
            $tmp = pathinfo($filename);
            if (!in_array(strtolower($tmp['extension']), $this->whitelist)) {
                Controller::exit_app('Ungültiger Dateityp');
            }
            $tmpname = $_FILES['newfile']['tmp_name'][$x];
            $basename = $this->cleanName($tmp['basename']);
            $out = $_SESSION['uid'].'_'.time().'_'.$basename;
            $destination = $this->path.DIRECTORY_SEPARATOR.$out;
            if ($tmp['extension'] == 'jpg') {
                $img = imagecreatefromjpeg($tmpname);
                // Optimierte Version speichern
                imagejpeg($img, $destination, 90);
            } else {
                move_uploaded_file($tmpname, $destination);
            }
        }
        Controller::redirect('admin/filer');
    }

    public function loadJson()
    {
        $data = $this->browse();
        showjson($data);
    }

    /**
     * @param $file
     *
     * @return bool|string
     */
    public function filetype($file)
    {
        $x = pathinfo($this->path.DS.$file);
        $x['extension'] = strtolower($x['extension']);
        if ($x['extension'] == 'jpg' or $x['extension'] == 'jpeg' or $x['extension'] == 'gif' or $x['extension'] == 'png'
        ) {
            return WEB.'web/_media/'.$file;
        } else {
            return WEB.'web/_media/'.$file;
        }
    }
}

$admin_filer = new adminfiler();
