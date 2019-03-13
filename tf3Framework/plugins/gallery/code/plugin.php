<?php

class gallery extends tffplugin
{
    public $name = 'Bildergalerie';
    public $description = 'einfaches Galerieskript';
    public $author = 'Marcel Schindler';
    private $gallerydir;
    private $list_of_galleries;
    public $allowed;
    public $view;

    private function delete($params)
    {
        $galID = isset($params[3]) ? (int) $params[3] : false;
        $img = isset($params[4]) ? (int) $params[4] : false;
        if ($galID && $img) {
            $path = $this->list_of_galleries[$galID];
            $images = $this->get_images($galID);

            $file = ($this->gallerydir.'/'.$path.'/'.$images[$img]);
            $meta = $this->gallerydir.'/'.$path.'/'.$img.'.meta';
            if (is_file($meta)) {
                unlink($meta);
            }
            if (is_file($file)) {
                unlink($file);
                Controller::redirect('admin/plugins/gallery/backend/view/'.$galID);
            }
        }
        Controller::redirect('admin/plugins/gallery/backend/');
    }
    /**
     * Deletes the whole Gallery.
     *
     * @param type $params
     */
    private function deletegal($params)
    {
        $int = isset($params[3]) ? (int) $params[3] : false;
        if ($int) {
            $pathname = $this->gallerydir.'/'.$this->list_of_galleries[$int];
            rmdir($pathname);
            Controller::redirect('admin/plugins/gallery/backend');
        }
    }

    /**
     * @global type $template
     *
     * @param type $params
     */
    public function backend($params)
    {
        global $template;
        $this->defaults();
        $action = isset($params[2]) ? $params[2] : '';

        switch ($action) {
            case 'edit': {
                $this->editImage($params);
                break;
            }
            case 'editmeta': {
                $this->editmeta();
                break;
            }
            case 'deletegal': {
                $this->deletegal($params);
                break;
            }
            case 'delete': {
                    $this->delete($params);
                    break;
                }
            case 'view': {
                    $this->view($params[3]);
                    break;
                }
            case 'upload': {
                    $this->upload();
                    break;
                }
            case'new': {
                    $this->newgallery();
                    break;
                }
            default: {

                }
        }
        $template->assign('pluginview', dirname(__DIR__).'/backend/view.php');
        $template->assign('galleries', $this->list_of_galleries);
    }

    private function editmeta()
    {
        global $SQL;
        $titel = _request('bildtitel', '');
        $edit_content = htmlentities($_REQUEST['edit_content']);
        $galleryID = _request('galleryID', 0);
        $imageId = _request('imageID', 0);
        $bildname = _request('bildname', '');
        $textfile = $bildname.'.meta';
        $path = $this->gallerydir.'/'.$this->list_of_galleries[$galleryID];
        $meta = $path.'/'.$textfile;
        $out = new \stdclass();
        $out->contents = $edit_content;
        $out->title = $titel;
        $json = json_encode($out);
        $fp = fopen($meta, 'w+');
        fputs($fp, $json);
        fclose($fp);
        Controller::redirect('admin/plugins/gallery/backend/edit/'.$galleryID.'/'.$imageId);
    }

    private function editImage($p)
    {
        global $template;
        $galleryID = $p[3];
        $imageId = $p[4];
        $imagelist = $this->get_images($galleryID);
        $image = $imagelist[$imageId];
        $meta = $this->getImageMETA($galleryID, $image);
        $details['SERVER'] = $this->gallerydir.'/'.$this->list_of_galleries[$galleryID].'/'.$image;
        $details['URL'] = WEB.'gallery/'.$this->list_of_galleries[$galleryID].'/'.$image;
        $details['bildname'] = $image;
        $details['imageID'] = $imageId;
        $details['galleryID'] = $galleryID;
        $details['contents'] = $meta->contents;
        $details['title'] = $meta->title;
        $template->assign('details', $details);
    }

    private function getImageMETA($galleryID, $imageId)
    {
        $textfile = $imageId.'.meta';
        $path = $this->gallerydir.'/'.$this->list_of_galleries[$galleryID];
        $meta = $path.'/'.$textfile;
        if (file_exists($meta)) {
            $tmp = file_get_contents($meta);
            $result = json_decode($tmp);
            $result->contents = html_entity_decode($result->contents);
        } else {
            $result->title = 'Bildtitel';
            $result->contents = 'Inhaltsseite';
        }

        return $result;
    }

    private function newgallery()
    {
        $newgal = _request('newGal', '');
        if ($newgal) {
            $pathname = $this->gallerydir.'/'.$newgal;
            if (!file_exists($pathname)) {
                mkdir($pathname, 777);
            }
        }
        Controller::redirect('admin/plugins/gallery/backend/');
    }

    /**
     * @global type $template
     *
     * @param type $gallery
     */
    public function frontend($gallery)
    {
        global $template;
        $this->defaults();
        $id = array_search($gallery, $this->list_of_galleries);

        $images = $this->get_images($id);
        foreach ($images as $img => $bild) {
            $meta[$bild] = $this->getImageMETA($id, $bild);
        }
        $template->assign('path', WEB.'gallery/'.$this->list_of_galleries[$id]);
        $template->assign('images', $images);
        $template->assign('meta', $meta);
        $old = $template->getTemplateDir();
        $template->setTemplateDir(dirname(__DIR__).'/frontend/');
        $template->display('view.php');
        $template->setTemplateDir($old);
    }

    private function defaults()
    {
        $this->gallerydir = DROOT.'/public/gallery';
        if (!file_exists($this->gallerydir)) {
            mkdir($this->gallerydir, 777, true);
        }
        $this->get_galleries();
    }

    public function __construct()
    {
        parent::__construct();
        $this->allowed = ['jpg', 'jpeg', 'gif', 'png'];
    }

    /**
     * @param type $id
     *
     * @return type
     */
    public function get_images($id)
    {
        $images = array_diff(scandir($this->gallerydir.'/'.$this->list_of_galleries[$id]), array('..', '.'));
        if (is_array($images)) {
            foreach ($images as $image) {
                if (!stristr($image, '.meta')) {
                    $out[] = $image;
                }
            }
        }

        return $out;
    }

    /**
     * @global type $template
     *
     * @param type $id
     */
    private function view($id)
    {
        global $template;
        $images = $this->get_images($id);
        $template->assign('path', $this->list_of_galleries[$id]);
        $template->assign('images', $images);
        $template->assign('galleryid', $id);
        $template->assign('addImages', true);
    }

    /**
     * @global type $USER
     *
     * @return bool
     */
    private function upload()
    {
        global $USER;
        $chosen_gallery = _request('galleryid', 0);
        if (isset($_FILES['newfile'])) {
            $t = count($_FILES['newfile']);
        } else {
            return false;
        }
        for ($x = 0; $x < $t; ++$x) {
            if (!isset($_FILES['newfile']['name'][$x])) {
                Controller::redirect('admin/plugins/gallery/backend/view/'.$chosen_gallery);
                exit;
            }
            $filename = $_FILES['newfile']['name'][$x];
            $tmp = pathinfo($filename);
            if (!in_array(strtolower($tmp['extension']), $this->allowed)) {
                Controller::exit_app('Ungültiger Dateityp');
            }
            $tmpname = $_FILES['newfile']['tmp_name'][$x];
            $out = md5($tmp['basename']).'.'.$tmp['extension'];
            $destination = $this->gallerydir.DIRECTORY_SEPARATOR.$this->list_of_galleries[$chosen_gallery].DIRECTORY_SEPARATOR.$out;
            if ($tmp['extension'] == 'jpg') {
                $img = imagecreatefromjpeg($tmpname);
                // Optimierte Version speichern
                imagejpeg($img, $destination, 75);
            }
            if ($tmp['extension'] == 'jpeg') {
                $img = imagecreatefromjpeg($tmpname);
                // Optimierte Version speichern
                imagejpeg($img, $destination, 75);
            }

            if ($tmp['extension'] == 'gif') {
                $img = imagecreatefromgif($tmpname);
                // Optimierte Version speichern
                imagegif($img, $destination);
            }

            if ($tmp['extension'] == 'png') {
                $img = imagecreatefrompng($tmpname);
                // Optimierte Version speichern
                imagepng($img, $destination, 4);
            } else {
                move_uploaded_file($tmpname, $destination);
            }
        }
        Controller::redirect('admin/plugins/gallery/');
    }

    public function get_galleries()
    {
        $this->list_of_galleries = array_diff(scandir($this->gallerydir), array('..', '.'));
    }
}
