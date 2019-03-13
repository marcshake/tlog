<?php
use tf3Framework\lib\Controller;/**
 * Class adminblog.
 */

class adminblog
{
    /**
     * @var int
     */
    public $limit = 255;
    public $checked;
    public function __construct()
    {
    }    /**
     * @param $id
     */
    public function doChange($id)
    {
        global $SQL;
        $id = (int) $id;
        $tt = time();
        $tmp = $SQL->fetch("SELECT vis from tff_blog_posts where p_id={$id} limit 1");
        $upd = 0;
        if ($tmp[0]['vis'] == 0) {
            $upd = 1;
        }
        $SQL->query("UPDATE tff_blog_posts set vis={$upd}, times={$tt} where p_id={$id} limit 1");
        $page = (int) $_SESSION['view_page'];
        Controller::redirect('admin/blog/list/'.$page);
    }
    /**
     * Mass Delete all selected posts and relations.
     *
     * @global type $SQL
     *
     * @param type $params
     */
    public function massdelete($params = false)
    {
        global $SQL;
        $in = false;
        if (isset($_POST['delete'])) {
            foreach ($_POST['delete'] as $id) {
                $in[] = (int) $id;
            }
            $list = implode(',', $in);
            $SQL->query('DELETE FROM tff_blog_posts where p_id in ('.$list.')');
            $SQL->query('DELETE FROM tff_blog_relations where blog_id in('.$list.')');
        }
        Controller::redirect('admin/blog/list');
    }    /**
     * @param bool|false $params
     */
    public function doBlog($params = false)
    {
        global $template, $SQL;
        $SQL->cachetime = 0;
        if (isset($params[0])) {
            switch ($params[0]) {
                case 'massdelete': {
                        $this->massdelete($params);
                        break;
                    }
                case 'change_status': {
                        $this->doChange($params[1]);
                        break;
                    }
                case 'edit': {
                        if (!isset($params[1])) {
                            break;
                        }
                        $this->blogedit($params[1]);
                        break;
                    }
                case 'new': {
                        $this->doNewblog();
                        break;
                    }
                case'list': {
                        $this->doList($params);
                        break;
                    }
                case'deleteblog': {
                        $this->doDeleteBlog($params[1]);
                        break;
                    }
                case'saveblog': {
                        $this->doSaveblog($params);
                        break;
                    }
                case'newblog': {
                        $this->doNewBlog();
                        break;
                    }
                case 'cleanup': {
                        $this->cleanup();
                        break;
                    }
            }
        }
    }
    public function cleanup()
    {
        global $SQL;
        $SQL->query('DELETE from tff_blog_categories where cat_id not in (SELECT cat_id from tff_blog_relations)');
        controller::redirect('admin');
    }    /**
     * @param $params
     */
    public function doList($params)
    {
        global $SQL;
        global $template;
        $page = isset($params[1]) ? (int) $params[1] * $this->limit : 0;
        $q = _request('q', '');
        $_SESSION['view_page'] = floor($page / $this->limit);
        $tmp = $SQL->fetch('SELECT p_id from tff_blog_posts');
        $max = count($tmp);
        $page_next = ($page / $this->limit + 1 <= $max / $this->limit) ? $page / $this->limit + 1 : floor($max / $this->limit);
        $page_prev = ($page / $this->limit - 1 >= 0) ? $page / $this->limit - 1 : 0;
        $additional = '';
        if ($q!='') {
            $template->assign('q', $q);
            $additional = 'where MATCH (b.title,b.contents) against ("'.$q.'")';
        }
        $posts = $SQL->fetch('
      SELECT b.vis, b.p_id, b.title,b.comments_allowed, c.handle, u.user_name author
      FROM tff_blog_posts b
      LEFT JOIN tff_blog_relations r on r.blog_id=b.p_id
        LEFT JOIN tff_blog_categories c on c.cat_id = r.cat_id
        left join tff_users u on u.user_id = b.author_id
        '.$additional.'
       group by b.p_id
      order by vis,times desc');
        foreach ($posts as $row => $data) {
            $out[$row] = $data;
            $out[$row]['preview'] = false;
            if (!empty($data['handle'])) {
                $out[$row]['preview'] = urlencode($data['handle']).'/'.urlencode($data['title']);
            }
        }
        $posts = $out;
        $template->assign('page_next', $page_next);
        $template->assign('page_prev', $page_prev);
        $template->assign('bloglist', $posts);
    }
    public function doNewblog()
    {
        global $template, $SQL, $USER;
        $template->assign('CK', 1);
        $template->assign('ADMIN_ACTION', 'admin/blog/newblog');
        $template->assign('action', 'editor');
        $list_tags = $SQL->fetch('SELECT handle from tff_blog_categories order by handle');
        if ($list_tags) {
            $template->assign('tags', $list_tags);
        }
        if (isset($_REQUEST['edit_title'])) {
            $title = $SQL->escape($_REQUEST['edit_title']);
            $contents = $SQL->escape($_REQUEST['edit_content']);
            $headimg = $SQL->escape($_REQUEST['ogimage']);
            $author = $USER->get_id();
            $time = time();
            $zen = _request('zen', 0);
            if ($zen) {
                $contents = $this->parseZen($contents);
            }
            $save = 'INSERT INTO tff_blog_posts (title,contents,author_id,times,headimg)
					values ("' .$title.'","'.$contents.'",'.$author.','.$time.', "'.$headimg.'")';
            $SQL->query($save);
            $new_id = $SQL->last_id();
            $this->handletags($new_id);
            Controller::redirect('admin/blog/list');
        }
    }
    private function parseZen($contents)
    {
        $contents = trim($contents);
        $contents = str_replace('\r', '', $contents);
        $tmp = explode('\n\n', $contents);
        $contents = '';
        foreach ($tmp as $paragraph) {
            $contents .= '<p>'.$paragraph.'</p>';
        }
        // Single-Lines durch BR austauschen
        $contents = str_replace('\n', '<br>', $contents);
        return $contents;
    }    /**
     * @param $mod
     */
    public function blogedit($mod)
    {
        global $template, $SQL;
        $template->assign('CK', 1);
        $template->assign('action', 'editor');
        $entry = $SQL->fetch('SELECT * FROM tff_blog_posts where p_id='.$mod);
        $tags = $this->get_tags($mod);
        $list_tags = $SQL->fetch('SELECT cat_id,handle from tff_blog_categories order by handle');
        $template->assign('checked', $this->checked);
        $template->assign('tags', $list_tags);
        $template->assign('row', $entry[0]);
        $template->assign('edit_tags', $tags);
        $template->assign('edit_content', $entry[0]['contents']);
        $template->assign('edit_title', $entry[0]['title']);
        $template->assign('edit_ogimage', $entry[0]['headimg']);
        $template->assign('ADMIN_ACTION', 'admin/blog/saveblog');
        $template->assign('DELETE_ACTION', 'admin/blog/deleteblog');
        $_SESSION['editid'] = $mod;
    }    /**
     * @param $id
     *
     * @return bool|string
     */
    public function get_tags($id)
    {
        global $SQL;
        $this->checked = [];
        $qqq = "SELECT r.cat_id, d.handle
                from tff_blog_relations r
                left join tff_blog_categories d on d.cat_id=r.cat_id
                where blog_id=$id";
        $tmp = $SQL->fetch($qqq);
        $out = '';
        if (!$SQL->numrows()) {
            return false;
        }
        foreach ($tmp as $row => $value) {
            $this->checked[$value['cat_id']] = $value['cat_id'];
            $out .= $value['handle'].',';
        }
        $out = trim($out);
        return $out;
    }    /**
     * @param $edit
     */
    public function doDeleteblog($edit)
    {
        global $SQL;
        $SQL->query('DELETE from tff_blog_posts where p_id='.$edit);
        $SQL->query('DELETE FROM tff_blog_relations where blog_id='.$edit);
        // Zuletzt geänderter Eintrag löschen
        $page = isset($_SESSION['view_page']) ? (int) $_SESSION['view_page'] : 0;
        Controller::redirect('admin/blog/list/'.$page);
    }
    public function doSaveblog()
    {
        global $SQL;
        $_title = $SQL->escape($_REQUEST['edit_title']);
        $_entry = $SQL->escape($_REQUEST['edit_content']);
        $_headimg = $SQL->escape($_REQUEST['ogimage']);
        $pw = _request('password', '');
        $edit = $_SESSION['editid'];
        $zen = _request('zen', 0);
        if ($zen) {
            $_entry = $this->parseZen($_entry);
        } else {
            $this->handletags($edit);
        }
        $use_pw = $pw == true ? ", password='{$pw}' " : ', password=NULL';
        $update = 'UPDATE tff_blog_posts
			SET contents="' .$_entry.'",
			title="' .$_title.'",
			headimg="' .$_headimg.'"'.$use_pw.'
			where p_id=' .$edit.' limit 1';
        $SQL->query($update);
        unset($_SESSION['editid']);
        $page = isset($_SESSION['view_page']) ? (int) $_SESSION['view_page'] : 0;
        Controller::redirect('admin/blog/list/'.$page);
    }    /**
     * @param $post_id
     */
    public function handletags($post_id)
    {
        global $SQL;
        $tags = _request('tags', '');
        if (stristr($tags, ',')) {
            $tagarray = explode(',', $tags);
        } else {
            $tagarray[0] = $tags;
        }
        $SQL->query("DELETE FROM tff_blog_relations where blog_id=$post_id");
        foreach ($tagarray as $row => $tag) {
            $tmp = false;
            $tag = trim($tag);
            $ins = 0;
            if (strlen($tag) > 0) {
                $tmp = $SQL->fetch("SELECT cat_id from tff_blog_categories where handle='$tag'");
                if ($SQL->numrows() == 1) {
                    $ins = $tmp[0]['cat_id'];
                } else {
                    $SQL->query("insert into tff_blog_categories (handle) values ('$tag');");
                    $ins = $SQL->last_id();
                }
                if ($ins != 0) {
                    $SQL->query("insert into tff_blog_relations (blog_id, cat_id) values ($post_id,$ins) ");
                }
            }
        }
    }
}
$admin_blog = new adminblog();
