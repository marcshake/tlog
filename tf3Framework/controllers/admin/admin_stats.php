<?php

class adminstats
{
    public function __construct()
    {
        $this->get_votes();
    }

    public function get_votes()
    {
        global $SQL, $template;
        $votes = $SQL->fetch('SELECT max(stmp) lasttime, count(*) anz,vote,stmp from tff_votes group by vote order by anz desc');
        if (!$votes) {
            return false;
        }
        foreach ($votes as $row => $data) {
            $tmp = $SQL->fetch("SELECT title from tff_blog_posts where p_id={$data['vote']}");
            $out[$data['vote']]['title'] = $tmp[0]['title'];
            $out[$data['vote']]['likes'] = $data['anz'];
            $out[$data['vote']]['time'] = date('d.m.y', $data['lasttime']);
        }
        $template->assign('votes', $out);
    }
}
$adminstats = new adminstats();
