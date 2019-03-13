<?php
class ajax
{
    public function doLoad($file)
    {
        global $template;
        $path = basename($file[0]).'.php';
        if (isset($file[0]) and file_exists(TPL_DIR.$path)) {
            $template->display($path);
        }
    }
}
