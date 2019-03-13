<?php

class adminbackup
{
    public function doBackup($p)
    {
        switch ($p[0]) {
            case 'create': {
                    $this->doCreate();
                }
        }

        return false;
    }

    public function doCreate()
    {
    }
}
$admin_backup = new adminbackup();
