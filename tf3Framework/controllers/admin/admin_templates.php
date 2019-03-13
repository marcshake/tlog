<?php

class admintemplates
{
    private $templates;

    public function __construct()
    {
        $this->templates = $this->tpldir();
    }

    public function doTemplates($p)
    {
        global $template;
        $code = file_get_contents(DROOT.DS.'templates/refreshment5/header.php');
        $code = htmlspecialchars($code);

        $template->assign('templates', $this->templates);
        $template->assign('CODE', $code);
        $chosen = basename(TPL_DIR);
    }

    private function tpldir()
    {
        $dir = DROOT.DS.'templates';
        $scanned_directory = array_diff(
            scandir($dir),
            array('..', '.', 'admin_2', 'ns_admin', 'skeleton', 'resources')
        );

        return $scanned_directory;
    }
}
$admin_templates = new admintemplates();
