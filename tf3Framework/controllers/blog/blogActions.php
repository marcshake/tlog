<?php

use tf3Framework\lib\Controller;

/**
 * Class blog.
 */
class blog
{

    /**
     * @var
     */
    public $postings;

    /**
     * @var
     */
    public $categories;

    /**
     * @var
     */
    public $showPost;

    /**
     * @var
     */
    public $showCat;

    /**
     * @var
     */
    public $comments;

    /**
     * @var int
     */
    public $limit = 10;

    /**
     * @var
     */
    public $menustuff;

    /**
     * @var int
     */
    public $viewpage = 0;

    /**
     * @var bool
     */
    public $keywords = false;

    private function readtime($string)
    {
        $string = strip_tags($string);
        $words = explode(' ', $string);
        $time = count($words) / 150;
        $out = sprintf('%02d:%02d', (int) $time, fmod($time, 1) * 60);

        return $out;
    }

    public function doDefault()
    {
        $p = _request('p', 0);
        if ($p) {
            $this->findById($p);
        }
        $this->get_postings();
        $this->create_menustructure();
        $this->set_template();
    }

    public function doVoter()
    {
        global $SQL;
        $id = _request('bid', 0);
        $already_voted = isset($_SESSION['votes']) ? in_array($id, $_SESSION['votes']) : false;
        if ($id and ! $already_voted) {
            // User hat diesen Post noch nicht gevotet
            $time = time();
            $SQL->query("INSERT INTO tff_votes (vote,stmp) values({$id},{$time})");
            // Zu den bereits gezählten Votes dazu.
            $_SESSION['votes'][] = $id;
        }
        echo $this->numvotes($id);
        exit();
    }

    /**
     * @param $id
     *
     * @return bool|int
     */
    public function numvotes($id)
    {
        global $SQL;
        $SQL->set_cache(false);
        if ($id == 0) {
            return false;
        }
        $out = $SQL->fetch("SELECT count(*) anz from tff_votes where vote={$id}");
        if ($out) {
            return $out[0]['anz'];
        }

        return 0;
    }

    public function doArchive()
    {
        global $template;
        global $SQL;
        $data = $SQL->fetch('SELECT b.title, b.times, c.handle FROM tff_blog_posts b
  JOIN tff_blog_relations r on r.blog_id=b.p_id
  JOIN tff_blog_categories c on c.cat_id = r.cat_id
  WHERE b.vis = 1 order by b.times desc');
        if (!$data) {
            Controller::redirect('/');
        }
        foreach ($data as $row => $value) {
            $y = date('Y', $value['times']);
            $m = date('m', $value['times']);
            $d = date('d', $value['times']);
            $h = date('h', $value['times']);
            $out[$y][$m][$d][$h]['url'] = urlencode($value['handle']) . '/' . urlencode($value['title']) . '/';
            $out[$y][$m][$d][$h]['title'] = $value['title'];
        }
        $template->assign('archive', $out);
    }

    public function __construct()
    {
        $this->last_comments();
    }

    /**
     * @param $entry
     */
    public function doShow($entry)
    {
        // We do not want to read people this category
        if (isset($entry[0]) and $entry[0] == 'qload') {
            Controller::redirect('blog');
        }
        $params = ($entry);
        $this->showPost = isset($params[1]) ? _sanitize($params[1], '') : false;
        $this->showCat = isset($params[0]) ? _sanitize($params[0], '') : false;
        $category = $this->get_cat_id();
        $entry = $this->get_post_id($category);
        $this->get_postings($category, $entry);
        $this->create_menustructure();
        $this->get_comments($entry);
        if ($this->showPost) {
            $this->similar_postings($category);
        }
        if (_request('pdf', 0)) {
            $this->make_pdf();
        }
        $this->set_template();
    }

    private function make_pdf()
    {
        include DROOT . '/vendor/tecnickcom/tcpdf/tcpdf.php';
        $pdf = new TCPDF();
        $pdf->SetCreator('TFF3');
        $pdf->SetAuthor($this->postings[0]['author']);
        $pdf->SetTitle($this->postings[0]['title']);
        $pdf->setPrintHeader(true);
        $pdf->addpage();
        $pdf->setFont('Helvetica', '', 10);
        $html = $this->postings[0]['contents'];
        $html .= '<hr>' . PROJECT_ADMIN . ' ' . PROJECT_TITLE;
        if (class_exists('tidy')) {
            $tidy = new tidy();
            $html = $tidy->repairString($html, [], 'utf8');
        }
        $pdf->writeHTMLCell('', '', '', '', $html, 0, 0); // Schreibt das umgebaute Element rein.
        $pdf->Output();
    }

    /**
     * @param $catid
     *
     * @return bool
     */
    private function similar_postings($catid)
    {
        global $SQL, $template;
        // Try to get Posts from the same category
        $sel = "SELECT blog_id from tff_blog_relations where cat_id=$catid";
        $tmp = $SQL->fetch($sel);
        if (!$tmp) {
            return false;
        }
        $findlist = '';
        foreach ($tmp as $row => $val) {
            $findlist .= $val['blog_id'] . ',';
        }
        $findlist = substr($findlist, 0, -1);
        $posts = $SQL->fetch("SELECT title,headimg from tff_blog_posts where p_id in($findlist) and vis=1");
        if (!$posts) {
            return false;
        }
        $out = [];
        foreach ($posts as $row => $value) {
            $out[$row] = $value;
            $out[$row]['category'] = urldecode($this->showCat);
        }
        $template->assign('similar_posts', $out);
    }

    /**
     * @param $entry
     *
     * @return bool
     */
    public function doTrackback($entry)
    {
        // Fire up Trackback-Event
        return false;
    }

    private function create_menustructure()
    {
        // Setup some nice Menustuff
        global $SQL;
        $cats = $SQL->fetch("SELECT c.handle FROM tff_blog_categories c
            left join tff_blog_relations r on r.cat_id=c.cat_id
            where c.handle!='qload'
            group by(c.handle)
            having count(*) > 1
            
            ");

        $this->menustuff = $cats;
    }

    /**
     * @return bool
     */
    private function get_one_year_ago()
    {
        global $SQL;
        $lastyear = time() - 31556926;

        return false;
    }

    public function doRss()
    {
        global $template;
        $this->limit = 100;
        $this->get_postings(false, false, true, false);
        // Overwrite Template-Name
        $template->assign('PAGEROOT', WEB);
        $this->set_template();
        header('content-type:text/xml');
        $template->display('rss.php');
        exit();
    }

    /**
     * @param $eintrag
     *
     * @return bool
     */
    private function get_comments($eintrag)
    {
        global $SQL, $template;

        if (!$eintrag) {
            return false;
        }

        $q = 'SELECT *
                                  FROM tff_comments
                                  WHERE blogid = ' . $eintrag . ' ORDER BY stamp asc';
        $tmp = $SQL->fetch($q);
        if (count($tmp)) {
            foreach ($tmp as $row => $value) {
                $out[$row] = $value;
                $out[$row]['datum'] = date('d.m.Y', $value['stamp']);
                //                $out[$row]['comment'] = str_replace('\r\n', '<br/>', $out[$row]['comment']);
                $out[$row]['comment'] = htmlspecialchars_decode($out[$row]['comment']);
                if (empty($out[$row]['url'])) {
                    unset($out[$row]['url']);
                } else {
                    $value['url'] = htmlspecialchars_decode($value['url']);
                    if (!stristr($value['url'], 'http')) {
                        $out[$row]['url'] = 'http://' . $value['url'];
                    } else {
                        $out[$row]['url'] = $value['url'];
                    }
                }
            }
            $this->comments = $out;
        } else {
            $this->comments = false;
        }
        $template->assign('show_comments', true);
    }

    /**
     * @return bool|void
     */
    private function get_cat_id()
    {
        global $SQL, $template;

        if (!$this->showCat) {
            return false;
        }

        // Alle Blogs auslesen, die zu einer Kategorie gehören
        $cattitle = $SQL->escape(urldecode($this->showCat));
        if ($cattitle == '') {
            return;
        }
        $q = 'SELECT cat_id
                                  FROM ' . _CATS . '
                                  WHERE handle ="' . $cattitle . '"';
        $tmp = $SQL->fetch($q);
        if (count($tmp)) {
            return $tmp[0]['cat_id'];
        } else {
            Controller::pagenotfound_handler('Category not found');
        }
    }

    /**
     * @param $category
     *
     * @return bool
     */
    private function get_post_id($category)
    {
        global $SQL;
        if (!$category) {
            Controller::pagenotfound_handler('Posting not found');
        }
        if (!$this->showPost) {
            return false;
        }

        $title = $SQL->escape(urldecode($this->showPost));
        $q = 'SELECT p_id
                                  FROM ' . _BLOG . ', ' . _RELS . '
                                  WHERE title="' . $title . '"
                                  AND ' . _RELS . '.blog_id = p_id
                                  AND ' . _RELS . '.cat_id =' . $category;
        $tmp = $SQL->fetch($q);
        if (count($tmp)) {
            return $tmp[0]['p_id'];
        } else {
            Controller::pagenotfound_handler('Posting not found');
        }
    }

    /**
     * @param $param
     */
    public function doPage($param)
    {
        $args = $param;
        $page = _sanitize($param[0], 0);
        $cat = false;

        if (isset($param[1])) {
            $cat = _sanitize($param[1], 0);
        }
        $this->get_postings($cat, false, false, $page);
        $this->create_menustructure();
        $this->set_template($page, $cat);
    }

    /**
     * @param bool|false $category
     * @param bool|false $entry
     * @param bool|false $rss
     * @param bool|false $page
     */
    public function get_postings($category = false, $entry = false, $rss = false, $page = false)
    {
        global $SQL, $template;

        $page = (int) $this->page($page);
        // Build SQL-Query
        $q = [];
        $additional = ' WHERE BL.vis=1 ';
        if ($category) {
            $q[] = 'AND CA.cat_id = ' . $category;
            $_SESSION['view_category'] = (int) $category;
        }
        if ($entry) {
            $q[] = 'p_id=' . $entry;
            $this->create_keywords($entry);
        } else {
            $template->assign('pagination', true);
            $template->assign('PAGETITLE', PROJECT_TITLE . ' blog');
            $out2 = get_startpage();
            $template->assign('morenews', $out2);
        }

        if (count($q)) {
            $additional .= implode(' AND ', $q);
        }

        $sel = 'SELECT p_id, title, BL.password, vis,comments_allowed, contents, BL.headimg, AU.user_name, times,  CA.handle category
                                  FROM ' . _BLOG . ' AS BL
                                  JOIN ' . _AUTHOR . ' AU on BL.author_id = AU.user_id
                                  JOIN ' . _RELS . ' RE ON RE.blog_id = BL.p_id
                                  JOIN ' . _CATS . ' CA ON RE.cat_id = CA.cat_id
                                  ' . $additional . '
                                  GROUP BY BL.p_id
                                  ORDER BY times desc
                                  LIMIT ' . $page . ', ' . $this->limit;
        
        $tmp = $SQL->fetch($sel);
        $anz = count($tmp);
        foreach ($tmp as $r => $v) {
            $tmp[$r]['contents'] = str_replace('[{$PAGEROOT}]', WEB, $v['contents']);
            $tmp[$r]['readtime'] = $this->readtime($v['contents']);
            if ($anz == 1 && $entry) {
                $tmp[$r]['contents'] = str_replace('###more###', '<a name="more"> </a>', $tmp[$r]['contents']);
                $tmp[$r]['votes'] = $this->numvotes($entry);
            } else {
                $start = strpos($v['contents'], '<p>');
                $end = strpos($v['contents'], '</p>', $start);
                $paragraph = substr($v['contents'], $start, $end - $start + 4);
                $tmp[$r]['contents'] = $paragraph;
            }
            if ($v['password']!='') {
                $tmp[$r]['contents'] = $this->protectPost($v['p_id'], $tmp[$r]['contents']);
            }
        }
        if (count($tmp)) {
            $this->postings = $tmp;
            $this->get_categories($tmp);
        }

        // We have RSS
        if ($rss) {
            //  header('content-type:application/atom+xml');
        }
    }

    
    private function protectPost($id, $string)
    {
        if (isset($_SESSION['free'][$id])) {
            $out = $string;
        } else {
            $out = '<form action="'.WEB.'blog/setfree/'.$id.'" method="post">';
            $out .= '<p>Passwortgeschützter Beitrag :<input type="text" name="showPW"><input type="submit" value="Anzeigen" /></form>';
        }
        return $out;
    }
    
    public function doSetfree($params)
    {
        global $SQL;
        $id = (int) $params[0];
        $pw = _request('showPW', '');
        $correct = $SQL->fetch("SELECT p_id,title from tff_blog_posts where p_id={$id} and password='{$pw}'");
        if ($correct) {
            $_SESSION['free'][$id] = true;
        }
        $this->findById($id);
    }
    
    private function findById($id)
    {
        global $SQL;
        $title = $SQL->fetch('SELECT title from tff_blog_posts where p_id='.$id);
        if (!$title) {
            Controller::redirect('blog/');
        }
        $cid = $SQL->fetch("SELECT cat_id from tff_blog_relations where blog_id=".$id);
        if (!$cid) {
            Controller::redirect('blog/');
        }
        $handle = $SQL->fetch("SELECT handle from tff_blog_categories where cat_id=".$cid[0]['cat_id']);
        Controller::redirect('blog/show/'.urlencode($handle[0]['handle']).'/'.urlencode($title[0]['title']));
    }
    /**
     * @param $id
     */
    private function create_keywords($id)
    {
        // Lade Content und erstelle Keywords daraus
        global $SQL, $template;
        $t = (int) $id;
        $words = $SQL->fetch('SELECT contents FROM ' . _BLOG . ' WHERE p_id=' . $id);
        $tmp = strip_tags($words[0]['contents']);
        $tmp = strtolower($tmp);
        $tmp = str_replace("\n", '', $tmp);
        $tmp = str_replace("\t", '', $tmp);
        $tmp = str_replace("\r", '', $tmp);
        $tmp = str_replace(',', '', $tmp);
        $tmp = str_replace('.', '', $tmp);
        $tmp = str_replace(':', '', $tmp);
        $tmp = str_replace('"', '', $tmp);
        $tmp = str_replace('(', '', $tmp);
        $tmp = str_replace(')', '', $tmp);

        $common = [
            'einen',
            'eins',
            'einem',
            'alles',
            'ein',
            'ich',
            'du',
            'marcel',
            'und',
            'google',
            'trancefish',
            'ist',
            'auch',
            'der',
            'die',
            'das',
            'michael',
            'sein',
            'dich',
            'dein',
            'habe',
            'eine',
            'ein',
            'sind',
            'dass',
            '',
            'für',
            '',
        ];

        $list = explode(' ', $tmp);
        $keyword = [];
        if (is_array($list)) {
            foreach ($list as $word) {
                if (strlen($word) > 5 and ! in_array($word, $common)) {
                    $keyword[$word] = $word;
                }
            }
        }

        if (count($keyword) >= 4) {
            $x = 0;
            $y = 50;    // Maximalwert der Keywords
            $wortliste = '';
            foreach ($keyword as $wort) {
                if ($x <= $y) {
                    if (strlen($wort) > 4) {
                        $wortliste .= $wort . ', ';
                    }
                }
                ++$x;
            }

            $template->assign('KEYWORDS', $wortliste);
        }
    }

    /**
     * @param $tmp
     *
     * @return bool
     */
    private function get_categories($tmp)
    {
        global $SQL;
        // Find categories
        foreach ($tmp as $row => $value) {
            $blog_id[] = $value['p_id'];
        }
        if (!is_array($blog_id)) {
            return false;
        }
        $entries = implode(' OR ' . _RELS . '.blog_id = ', $blog_id);
        $sel = 'SELECT ' . _RELS . '.blog_id, CA.handle
                                  FROM ' . _CATS . ' CA
                                  JOIN ' . _RELS . ' on ' . _RELS . '.cat_id = CA.cat_id
                                  WHERE ' . _RELS . '.blog_id = ' . $entries . ' ORDER by rel_id';
        $cats = $SQL->fetch($sel);

        $x = 0;
        foreach ($cats as $row => $value) {
            $out[$value['blog_id']][$x]['title'] = $value['handle'];
            $out[$value['blog_id']][$x]['url'] = urlencode($value['handle']);
            ++$x;
        }
        unset($cats);
        $this->categories = isset($out) ? $out : false;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function get_cat_title($id)
    {
        global $SQL;
        $tmp = $SQL->fetch("SELECT handle from tff_blog_categories where cat_id=$id");

        return $tmp[0]['handle'];
    }

    /**
     * @param int        $page
     * @param bool|false $cat
     */
    private function set_template($page = 0, $cat = false)
    {
        global $template;
        $headimg = false;
        $add = ' Blog - ';

        if ($page != 0) {
            $cattitle = ($cat != false) ? $this->get_cat_title($cat) : ' Seite ';
            $add = " Blog - $cattitle / $page ";
        }
        if (count($this->postings) > 1) {
            $title = PROJECT_TITLE . $add;
            $time = time();
            $desc = '';
            foreach ($this->postings as $row => $data) {
                $desc .= $data['title'] . ' ';
            }
            $template->assign('DESCRIPTION', $desc);
        } else {
            $title = $this->postings[0]['title'];
            $time = $this->postings[0]['times'];
            $headimg = (isset($this->postings[0]['headimg']) && strlen($this->postings[0]['headimg']) > 0) ? $this->postings[0]['headimg'] : false;
            $content = $this->postings[0]['contents'];
            $cache=preg_replace('~<iframe[^>]*\K(?=src)~i', 'data-', $content);
            #            $this->postings[0]['contents'] = $cache;
            $DESCRIPTION = (strip_tags(str_replace("\r", ' ', $this->postings[0]['contents'])));
            $DESCRIPTION = str_replace("\n", '', $DESCRIPTION);
            $DESCRIPTION = substr($DESCRIPTION, 0, 512);
            $template->assign('DESCRIPTION', trim($DESCRIPTION));
            if (!empty($headimg)) {
                $template->assign('OGIMAGE', $headimg);
            }
        }
        if (count($this->postings) == 0) {
            Controller::pagenotfound_handler('Oops: I think something went completely out of control here. I guess, I made a fucking mistake or you tried to reach a page that does not exist.');
        }
        $this->listpages($cat);

        $this->clean_postings();

        if ($this->comments) {
            $template->assign('comments', $this->comments);
        }
        //print_r($this->postings);exit();
        $template->assign('blogposts', $this->postings);
        $template->assign('menudata', $this->postings);
        $template->assign('moremenus', $this->menustuff);
        $template->assign('title', $title);
        $template->assign('PAGETITLE', $title);
        $template->assign('SUBTITLE', (urldecode($this->showCat)));

        $template->assign('HEADER_IMG', $headimg);
        $template->assign('pubDate', $this->date3339($time));
        $template->assign('hideall', true);
    }

    private function clean_postings()
    {
        foreach ($this->postings as $row => $value) {
            $this->postings[$row]['url'] = WEB . 'blog/show/' . urlencode($value['category']) . '/' . urlencode($value['title']) . '/';
            $this->postings[$row]['categories'] = $this->categories[$value['p_id']];
            $this->postings[$row]['updated'] = $this->date3339($value['times']);
            $this->postings[$row]['description'] = $value['contents']; //strip_tags($value['contents'], '<p><br><img><a><ul><li><dd><dt><dl><del><ins><blockquote><em><b>');
            $this->postings[$row]['postdate'] = date('d.m.Y - H:i', $value['times']);
        }
    }

    /**
     * @return bool
     */
    private function generate_pdf()
    {
        /*    if(count($this->postings) == 0)
          {
          Controller::pagenotfound_handler('Illegal Posting called');
          }
          $this->clean_postings();
          include(DCMS_BASE_PATH.'lib/fpdf16/fpdf.php'); */
        return false;
    }

    /**
     * @param int $timestamp
     *
     * @return bool|string
     */
    public function date3339($timestamp = 0)
    {
        if (!$timestamp) {
            $timestamp = time();
        }
        $date = date('D, d M Y H:i:s O', $timestamp);

        return $date;

        $matches = [];
        if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
            $date .= $matches[1] . $matches[2] . ':' . $matches[3];
        } else {
            $date .= 'Z';
        }

        return $date;
    }

    /**
     * @param $blog_id
     *
     * @return bool
     */
    private function trackback($blog_id)
    {
        return false;
    }

    public function doSuchen()
    {
        $search = isset($_POST['q']) ? $_POST['q'] : false;
        if ($search and strlen($search) >= 4) {
            header('location:' . WEB . 'blog/search/' . $search);
        } else {
            Controller::pagenotfound_handler('Suchbegriff nicht gefunden');
        }
        exit();
    }

    /**
     * @param $search
     */
    public function doSearch($search)
    {
        global $SQL, $template, $tpl;
        if ($search[0]) {
            $keyword = $SQL->escape(strip_tags($search[0]));
            $query = '
                                    SELECT *,
                                      MATCH(contents) AGAINST("' . $keyword . '") AS score
                                      FROM ' . _BLOG . '
                                    WHERE MATCH(contents) AGAINST("' . $keyword . '") AND vis=1
                                    ORDER BY score DESC
                                  ';
            $res = $SQL->fetch($query);
            $contents = false;

            $contents['title'] = $keyword;
            if (count($res)) {
                $rels = $SQL->fetch('SELECT * FROM ' . _RELS);
                $cats = $SQL->fetch('SELECT * FROM ' . _CATS);
                foreach ($res as $row => $value) {
                    $entry[$value['p_id']] = $value;
                }
                foreach ($rels as $row => $value) {
                    $rel[$value['blog_id']] = $value['cat_id'];
                }
                foreach ($cats as $row => $value) {
                    $cat[$value['cat_id']] = $value['handle'];
                }
                // Array nochmal umbauen
                $page = '';
                foreach ($entry as $id => $value) {
                    $cat_id = $rel[$id];
                    $handle = $cat[$cat_id];
                    $url = WEB . 'blog/show/' . urlencode($handle) . '/' . $value['title'] . '/';
                    $contents = substr(strip_tags($value['contents']), 0, 1024);
                    $contents = str_ireplace($keyword, '<span style="color:#ff0;background:#000">' . $keyword . '</span>', $contents);
                    $score = $value['score'];
                    $page .= '<dl><dt><a href="' . $url . '">' . $value['title'] . '</a></dt>';
                    $page .= '<dd>' . $contents . '<br/><br/>' . $score . '</dd>';
                    $page .= '</dl>';
                }
                unset($contents);
                $contents['title'] = $keyword;
                $contents['contents'] = $page;
            } else {
                $contents['title'] = $keyword;
                $contents['content'] = '<p>Ihre Suche nach ' . $keyword . ' ergab keine Treffer</p>';
            }
            $template->assign('content', $contents);
            $template->assign('SUBTITLE', 'Suchergebnisse');
            $template->display('page.tpl');
            exit();
        }
    }

    /**
     * @param bool|false $page
     *
     * @return int
     */
    private function page($page = false)
    {
        global $template;
        $x = (int) $page - 1;
        $x = $x <= 0 ? $x = 0 : $x;
        $next = $x + 1;
        $prev = ($x - 1 <= 0) ? 0 : $x - 2;
        $this->viewpage = $next;
        $template->assign('prepage', $next);
        $template->assign('nextpage', $next + 1);

        return $x * $this->limit;
    }

    /**
     * @param bool|false $param
     */
    private function listpages($param = false)
    {
        global $template, $SQL;
        $in_category = '';

        $cat = $this->get_cat_id();
        if (!$cat) {
            $cat = $param;
        }
        if ($cat) {
            $qq = 'SELECT blog_id from tff_blog_relations where cat_id = ' . $cat . ' group by blog_id';
            $tmp = $SQL->fetch($qq);
            $data = '';
            foreach ($tmp as $row => $v) {
                $data .= $v['blog_id'] . ',';
            }
            $in_category = ' and p_id in(' . substr($data, 0, -1) . ')';
        }
        $qq2 = 'SELECT COUNT(*) ee FROM ' . _BLOG . ' WHERE vis=1' . $in_category;
        $entries = $SQL->fetch($qq2);
        $pages = ceil($entries[0]['ee'] / $this->limit);
        $out = '';
        for ($x = 1; $x <= $pages; ++$x) {
            $active = ($this->viewpage == $x) ? ' button-primary ' : '';
            $out .= '<a href="' . WEB . 'blog/page/' . $x . '/' . $cat . '" class="button' . $active . '">' . $x . '</a> &nbsp; ';
        }
        $template->assign('pagelist', $out);
    }

    /**
     * @return bool
     */
    private function last_comments()
    {
        global $SQL, $template;
        $comments = $SQL->fetch('SELECT p.title, c.user,c.mail,c.stamp,c.blogid,bc.handle from tff_comments c
                LEFT JOIN tff_blog_posts p on p.p_id=c.blogid
                LEFT JOIN tff_blog_relations br on br.blog_id = c.blogid
                LEFT JOIN tff_blog_categories bc on br.cat_id=bc.cat_id
                order by stamp desc LIMIT 10
                ');
        if (!$comments) {
            return false;
        }
        foreach ($comments as $row => $value) {
            $out[$row]['url'] = WEB . 'blog/show/' . urlencode($value['handle']) . '/' . urlencode($value['title']) . '/';
            $out[$row]['writer'] = $value['user'];
            $out[$row]['datum'] = date('d.m.Y', $value['stamp']);
            $out[$row]['title'] = $value['title'];
        }
        $template->assign('last_comments', $out);

        return true;
    }
}
