<?php

/* Set Table Names */

class page
{
    public $category_id;
    public $page_id;
    public $cat_title = '';
    public $cat_decription = '';
    public $page_found = false;

    public function doDefault()
    {
        global $SQL, $template;
        $data = $SQL->fetch('select * from '._CMS_POSTS.' where handle="homepage"');
        $data[0]['contents'] = str_replace(
            '[{$PAGEROOT}]',
            WEB,
            $data[0]['contents']
        );
        $template->assign('PAGETITLE', $data[0]['title']);
        $template->assign('contents', $data[0]);
        $template->assign('KEYWORDS', $data[0]['keywords']);
        $template->assign('DESCRIPTION', $data[0]['description']);
        $template->assign('TFF_HOME', 1);
    }

    public function doCms($entry)
    {
        // Open CMS-Page

        global $SQL;
        $params[0] = !isset($entry[0]) ? 0 : $entry[0];
        $params[1] = !isset($entry[1]) ? 'index' : $entry[1];
        $cat = _sanitize($params[0], '');
        $pag = _sanitize($params[1], '');
        $this->getCatID($cat, $pag);
        if ($params[1] == 'index') {
            if (!$this->page_found) {
                $this->create_index();
            }
        } else {
            // We try to load the contentn here
            $this->get_contents($params[1]);
        }
    }

    private function get_contents($title)
    {
        global $SQL, $template;
        $da = urldecode($title);
        $da = _sanitize($da, '');
        $query = 'SELECT * FROM '._CMS_POSTS.' where title="'.$da.'" AND cat_id='.$this->category_id.' LIMIT 1';
        $tmp = $SQL->fetch($query);
        $headimg = false;
        if (count($tmp)) {
            if ($tmp[0]['redirect'] != '') {
                Controller::redirect($tmp[0]['redirect']);
            }
            $contents = $tmp[0];
            $contents['contents'] = str_replace(
                '[{$PAGEROOT}]',
                WEB,
                $contents['contents']
            );
            $contents['url'] = urlencode(substr(WEB, 0, -1).$_SERVER['REQUEST_URI']);
            $headimg = $contents['headimg'];
            $template->assign('contents', $contents);
            $template->assign('KEYWORDS', $contents['keywords']);
            $template->assign('DESCRIPTION', $tmp[0]['description']);

            $template->assign('PAGETITLE', $contents['title']);
            $template->assign('SUBTITLE', $this->cat_title);
            if (!empty($headimg)) {
                $template->assign('OGIMAGE', $headimg);
            }

            $template->assign('hideall', true);
        } else {
            Controller::pagenotfound_handler(ERR_UNKNOWN_PAGE);
        }
    }

    private function create_index()
    {
        // Do something
        global $template, $SQL;
        $topics = $SQL->fetch('SELECT title from '._CMS_POSTS.' WHERE cat_id='.$this->category_id);
        $desc = '';
        foreach ($topics as $row => $value) {
            $desc .= $value['title'].' ';
            $topics[$row]['url'] = urlencode($value['title']);
        }
        $do_index['title'] = $this->cat_title;
        $do_index['PAGETITLE'] = $this->cat_title;
        $do_index['describe'] = $this->cat_decription;
        $do_index['topics'] = $topics;
        $do_index['title_url'] = urlencode($this->cat_title);
        $template->assign('do_index', $do_index);
        $template->assign('PAGETITLE', $do_index['PAGETITLE']);
        $template->assign('DESCRIPTION', $desc);
    }

    private function getCatID($string = 0, $pag = 0)
    {
        global $SQL;
        $string = urldecode($string);
        $string = _sanitize($string, '');
        $query = 'SELECT * FROM '._CMS_CAT.' WHERE handle="'.$string.'" LIMIT 1';
        $tmp = $SQL->fetch($query);
        if (count($tmp) == 1) {
            $this->category_id = (int) $tmp[0]['cat_id'];
            $this->cat_title = $tmp[0]['handle'];
            $this->cat_decription = $tmp[0]['category_description'];
        } else {
            // User might have entered a direct Handle
            $this->category_id = 0;
            $this->page_found = true;
            $this->get_contents($string);
        }
    }
}
