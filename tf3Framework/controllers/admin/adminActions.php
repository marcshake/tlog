<?php
use tf3Framework\lib\Controller;

// Modified some blog stuff

class admin
{
    public $view;

    public function __construct()
    {
        global $USER, $template, $SQL;
        $template->setTemplateDir(DROOT.'/tf3Framework/templates/admin3/');

        $lvl = $USER->get_level();
        if ($lvl != 1) {
            $template->display('login.php');
            exit();
        }

        $SQL->set_cache(false);
        $this->update();
        $this->view = $template;    // Weiterverwendung des Views
        $anz = $SQL->fetch('SELECT count(*) anz FROM tff_blog_posts');
        $comm = $SQL->fetch('SELECT count(*) anz FROM tff_comments');
        $cnt = $SQL->fetch('SELECT count(*) anz FROM tff_cmspages');
        $arr = array('numBlogs' => $anz[0]['anz'],
            'numComments' => $comm[0]['anz'],
            'numPages' => $cnt[0]['anz'], );
        $template->assign('USER', $USER);
        $template->assign('stats', $arr);
    }

    private function update()
    {
        global $SQL;
        $tmp = $SQL->fetch('show columns from tff_cmspages');
        $description_there = false;
        $lvl = false;
        foreach ($tmp as $row => $value) {
            if ($value['Field'] == 'description') {
                $description_there = true;
            }
            if ($value['Field'] == 'lvl') {
                $lvl = true;
            }
        }
        if ($description_there === false) {
            $SQL->query('ALTER TABLE `tff_cmspages` ADD `description` VARCHAR(255) NOT NULL AFTER `keywords`');
        }
        if ($lvl == false) {
            $SQL->query('ALTER TABLE `tff_cmspages` ADD `lvl` INT NULL AFTER `teaser`');
        }
    }

    public function doPlugins($params)
    {
        include 'admin_plugins.php';
        $this->view->display('plugins.php');
    }

    public function doFilejson()
    {
        include 'admin_filer.php';
        $admin_filer->loadJson();
    }

    public function doStats($params = false)
    {
        include 'admin_stats.php';
        $this->view->display('stats.php');
    }

    public function doVertrag($params = false)
    {
        include 'admin_vertrag.php';
        $admin_vertrag->doVertrag($params);
        $this->view->display('vertrag.php');
    }

    public function doZen($params = false)
    {
        global $SQL;
        if (isset($params[0])) {
            $id = (int) $params[0];
            $_SESSION['editid'] = $id;
            $entry = $SQL->fetch('SELECT * FROM tff_blog_posts where p_id='.$id);
            $entry[0]['contents'] = strip_tags($entry[0]['contents']);
            $this->view->assign('entry', $entry[0]);
        }
        $this->view->display('zen.php');
    }

    public function doBlog($params = false)
    {
        // Inkludiere Externe Klasse, um das hier zu managen
        include 'admin_blog.php';
        $admin_blog->doBlog($params);
        $this->view->display('blog.php');
    }

    public function doTemplates($params = false)
    {
        include 'admin_templates.php';
        $admin_templates->doTemplates($params);
        $this->view->display('templates.php');
    }

    public function doFiler($params = false)
    {
        include 'admin_filer.php';
        $admin_filer->doFiler($params);
        $this->view->display('filer.php');
    }

    public function doTidy($params = false)
    {
        include 'admin_tidy.php';
        $admin_tidy->doTidy();
        $this->view->display('tidy.php');
    }

    public function doCms($params = false)
    {
        // Inkludiere CMS-Modul
        include 'admin_cms.php';
        $admin_cms->doCMS($params);
        $this->view->display('content_pages.php');
    }

    public function doPurge()
    {
        // This removes everything from the Cache-Directory
        global $SQL;
        $SQL->query('DELETE from tff_blog_relations where blog_id not in (SELECT p_id from tff_blog_posts)');
        $SQL->query('DELETE FROM tff_blog_categories WHERE cat_id NOT IN (SELECT cat_id FROM tff_blog_relations)');
        $SQL->query('DELETE from tff_comments where blogid not in (select p_id from tff_blog_posts)');
        controller::redirect('admin');
        exit();
    }

    public function doComments($params = false)
    {
        include 'admin_comments.php';
        $admin_comments->doComments($params);
        $this->view->display('comments.php');
    }

    public function doUsers($params = false)
    {
        include 'admin_users.php';
        $admin_users->doUsers($params);
        $this->view->display('users.php');
    }

    public function doMusic($params = false)
    {
        include 'admin_music.php';
        $admin_music->doMusic($params);
    }

    public function doBackup($params = false)
    {
        include 'admin_backup.php';
        $this->view->display('backup.php');
        $admin_backup->doBackup($params);
    }

    public function doDefault()
    {
        global $USER, $template, $SQL;
        $this->view = $template;    // Weiterverwendung des Views
        $this->view->assign('welcome', true);
        $this->view->assign('version', date('y.m.h', filemtime(__FILE__)));
        $this->view->assign('postings', count($SQL->fetch('SELECT p_id FROM tff_blog_posts')));
        $this->view->assign('pages', count($SQL->fetch('SELECT p_id FROM tff_cmspages')));
        $this->view->assign('users', count($SQL->fetch('SELECT user_id FROM tff_users')));

        $this->view->display('admin.php');

        return;
    }
}
