<?php

class sitemap
{
    public function doDefault()
    {
        global $SQL;
        $urlset = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $tmp = $SQL->fetch('SELECT handle from tff_blog_categories bc
                    left join tff_blog_relations br on br.cat_id = bc.cat_id
                    left join tff_blog_posts b on br.blog_id = b.p_id
                    where b.vis = 1 group by br.cat_id');
        foreach ($tmp as $row => $value) {
            $entry = urlencode($value['handle']);
            $path = WEB.'blog/show/'.$entry;
            $urlset .= '<url><loc>'.$path.'</loc></url>';
        }
        $tmp2 = $SQL->fetch('SELECT p.title, c.handle FROM `tff_blog_posts` p
                                        left join tff_blog_relations br on br.blog_id = p.p_id
                                        left join tff_blog_categories c on c.cat_id=br.cat_id
                                        where p.vis=1
                                        GROUP by p.p_id;');
        foreach ($tmp2 as $row => $value) {
            $handle = urlencode($value['handle']);
            $title = urlencode($value['title']);
            $path = WEB.'blog/show/'.$handle.'/'.$title;
            $urlset .= "<url><loc>$path</loc></url>";
        }
        $urlset .= '</urlset>';
        header('Content-Type: application/xml');
        echo $urlset;
        exit();
    }
}
