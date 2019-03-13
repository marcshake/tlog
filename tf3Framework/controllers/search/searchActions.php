<?php

class search
{
    public static function doDefault()
    {
        global $SQL, $template;

        $suchbegriff = _request('q', '');

        if (strlen($suchbegriff) < 1) {
            return false;
        }

        // Suche auslösen, blog
        $blogposts = $SQL->fetch('SELECT c.handle, b.p_id, b.title, match(b.title,b.contents) againsT("'.$suchbegriff.'") AS score
            from ' ._BLOG.' b

            left join tff_blog_relations br on b.p_id=br.blog_id
            left join tff_blog_categories c on c.cat_id = br.cat_id
            where match(title,contents) against("' .$suchbegriff.'") and vis=1');

        // Suche auslösen, content
        $postings = $SQL->fetch('SELECT c.handle,p.handle filename,p.title, match(p.title,p.contents) againsT("'.$suchbegriff.'") AS score
            from ' ._CMS_POSTS.' p
                            left join tff_categories c on c.cat_id=p.cat_id

               where match(title,contents) against("' .$suchbegriff.'") and visible=1 and p.cat_id!=0');

        $result = [];
        if ($blogposts) {
            foreach ($blogposts as $row => $value) {
                $score = $value['score'];
                $result[$score]['title'] = $value['title'];
                $result[$score]['url'] = WEB.'blog/show/'.urlencode($value['handle']).'/'.urlencode($value['title']);
            }
        }
        if ($postings) {
            foreach ($postings as $row => $value) {
                $score = $value['score'];
                $result[$score]['title'] = $value['title'];
                $result[$score]['url'] = WEB.''.urlencode($value['handle']).'/'.urlencode($value['filename']);
            }
        }
        ksort($result);
        $template->assign('result', $result);
        $template->assign('suchbegriff', $suchbegriff);

        return $result;
    }
}
