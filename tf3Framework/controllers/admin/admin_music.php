<?php

class adminmusic
{
    public function doMusic($params)
    {
        global $template;
        $template->assign('MUSIC_EDIT', 'admin/music/edit');
        $this->list_albums();
        if (isset($params[0])) {
            switch ($params[0]) {
                case 'edit': {
                        $this->edit($params[1]);
                        break;
                    }
                case'delete': {
                        $this->delete($params[1]);
                        break;
                    }
            }
        }
    }

    public function edit($album)
    {
        global $SQL;
        global $template;
        $template->assign('MUSIC_DELETE_ACTION', 'admin/music/delete');
        $songs = $SQL->fetch('SELECT * from tff_songs where alb_id='.(int) $album);
        $template->assign('tracks', $songs);
        $_SESSION['album'] = $album;
    }

    public function list_albums()
    {
        global $SQL, $template;
        $albs = $SQL->fetch('SELECT * from tff_albums');
        $template->assign('albs', $albs);
    }

    public function delete($id)
    {
        global $SQL;
        $SQL->query('DELETE from tff_songs where song_id='.(int) $id);
        Controller::redirect('admin/music/edit/'.$_SESSION['album']);
    }
}
$admin_music = new adminmusic();
