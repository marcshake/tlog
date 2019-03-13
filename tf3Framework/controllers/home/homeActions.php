<?php

class home
{
    public $limit = 50;
    public $monate = [
        1 => 'Januar',
        2 => 'Februar',
        3 => 'März',
        4 => 'April',
        5 => 'Mai',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'August',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember',
    ];
    public $cdn_active;

    public function __construct()
    {
        $this->cdn_active = false;

        return false;
    }

    public function doDefault()
    {
        global $SQL;
        global $template;
        $template->assign('PAGETITLE', PROJECT_TITLE);

        $home = $SQL->fetch("SELECT * from tff_cmspages where cat_id=0 and handle='index'");
        if ($home) {
            // Statische Startseite
            $template->assign('contents', $home[0]['contents']);
            $template->assign('teaser', $home[0]['teaser']);
            $template->assign('PAGETITLE', $home[0]['title']);
            $template->assign('OGIMAGE', $home[0]['headimg']);
        }

        $desc = '';
        $additional = ' WHERE BL.vis=1 ';

        // Most commented
        $cmd = $SQL->fetch("SELECT count(*) postcount ,blogid from tff_comments group by blogid order by postcount desc");
        $in = '';
        if ($cmd) {
            foreach ($cmd as $val) {
                $cc[] = $val['blogid'];
            }
            $in = ' and p_id in('. implode(',', $cc).')';
        }
        $in = '';

        $sel = 'SELECT p_id, title, vis, contents, BL.headimg, AU.user_name, times, CA.handle category
      FROM ' ._BLOG.' AS BL
      JOIN ' ._AUTHOR.' AU on BL.author_id = AU.user_id
      JOIN ' ._RELS.' RE ON RE.blog_id = BL.p_id
      JOIN ' ._CATS.' CA ON RE.cat_id = CA.cat_id
      ' .$additional.$in.'
      GROUP BY BL.p_id
      ORDER BY times desc LIMIT 6';
        $posts = $SQL->fetch($sel);
        foreach ($posts as $row => $val) {
            $out[$row] = $val;
            $out[$row]['w'] = 200;
            $out[$row]['h'] = 100;
            $desc .= $val['title'].' ';
            $out[$row]['contents_long'] = $this->clear($val['contents'], 1000);
            $out[$row]['contents'] = $this->clear($val['contents'], 320);
            $out[$row]['contents_short'] = $this->shorten($val['contents']);
        }
        $template->assign('posts', $out);
        $template->assign('DESCRIPTION', $home[0]['description']);
        $template->assign('KEYWORDS', $home[0]['keywords']);
    }

    private function shorten($string)
    {
        $v['contents'] = $string;
        $start = strpos($v['contents'], '<p>');
        $end = strpos($v['contents'], '</p>', $start);
        $paragraph = substr($v['contents'], $start, $end - $start + 4);
//        $tmp[$r]['contents'] = $paragraph;
        return $paragraph;
    }

    public function clear($string, $length = 210)
    {
        $tmp = strip_tags($string);
        $out = substr($tmp, 0, $length);

        return $out;
    }

    public function doPreview($params)
    {
        global $SQL;
        $id = _sanitize($params[0], 0);
        if ($id) {
            $tmp = $SQL->fetch("SELECT * from tff_blog_posts where p_id={$id} limit 1");
            echo json_encode($tmp[0]['contents']);
            exit();
        } else {
            exit();
        }
    }
}
