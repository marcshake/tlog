<?php

class music
{
    public function doDefault()
    {
        global $template;
        $template->assign('PAGETITLE', 'MP3 kostenlos');
        $this->createlist();
        if (_request('media', 0)) {
            $this->songs();
        }
    }

    public function songs()
    {
        global $SQL, $template;
        $album = _request('media', 0);
        $title = $SQL->fetch('SELECT alb_title from tff_albums where alb_id='.$album);
        $template->assign('PAGETITLE', 'Musik aus dem Album '.$title[0]['alb_title']);
        $tmp = $SQL->fetch("SELECT * FROM tff_songs where alb_id=$album order by filename");
        foreach ($tmp as $r => $v) {
            $out[$r] = $v;
            $out[$r]['action'] = false;
            if (strtolower(substr($v['filename'], -3)) == 'mp3' || strtolower(substr($v['filename'], -3)) == 'ogg') {
                $out[$r]['action'] = 'audio/'.substr($v['filename'], -3);
            }
        }
        $template->assign('tracks', $out);
    }

    public function createlist()
    {
        global $SQL, $template;
        $albums = $SQL->fetch('SELECT * FROM tff_albums order by alb_title');
        $template->assign('albums', $albums);
    }
}
