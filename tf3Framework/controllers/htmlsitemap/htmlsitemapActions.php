<?php

class htmlsitemap
{
    public function doDefault()
    {
        global $SQL, $template;
        $additional = ' where vis = 1 ';
        $sel = 'SELECT p_id, title, vis, AU.user_name, times, COUNT(COM.comment_id) anz, CA.handle category
      FROM ' ._BLOG.' AS BL
      LEFT JOIN ' ._COMMENTS.' COM on COM.blog_id = BL.p_id
      JOIN ' ._AUTHOR.' AU on BL.author_id = AU.user_id
      JOIN ' ._RELS.' RE ON RE.blog_id = BL.p_id
      JOIN ' ._CATS.' CA ON RE.cat_id = CA.cat_id
      ' .$additional.'
      GROUP BY BL.p_id
      ORDER BY times desc
      ';
        $tmp = $SQL->fetch($sel);
        $out = [];
        foreach ($tmp as $row => $data) {
            $out[$data['category']][] = $data;
        }
        unset($tmp);
        ksort($out);
        $template->assign('sitemap', $out);
    }
}
