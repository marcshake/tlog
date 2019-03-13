<?php

class admincomments
{
    public function doComments($params = false)
    {
        switch ($params [0]) {
            case 'delete': {
                    $this->delete($params[1]);
                }
            default: {
                    $this->commentbrowser($params);
                    break;
                }
        }
    }

    private function delete($id)
    {
        global $SQL;
        $idv = (int) $id;
        $SQL->query("DELETE FROM tff_comments where cid={$idv}");
        controller::redirect('admin/comments/');
    }

    private function commentbrowser($params = false)
    {
        global $SQL, $template;
        $loc = $SQL->fetch('SELECT * FROM tff_comments order by stamp DESC limit 100');
        if ($loc) {
            // Get Blog Postings
            $in = array();
            foreach ($loc as $row => $data) {
                $in[] = $data['blogid'];
                $comments[$data['blogid']][] = $data;
            }
            // Hol die entsprechenden Blogs
            $liste = implode(',', $in);
            $blogs = $SQL->fetch("SELECT p_id,title from tff_blog_posts where p_id in({$liste})");
            foreach ($blogs as $row => $value) {
                $out[$value['p_id']] = $value;
                $out[$value['p_id']]['comments'] = $comments[$value['p_id']];
            }
            $template->assign('comment_editor', $out);
        }
    }
}
$admin_comments = new admincomments();
