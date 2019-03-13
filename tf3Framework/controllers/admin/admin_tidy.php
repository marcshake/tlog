<?php

class adminTidy
{
    public function doTidy()
    {
        global $template;
        $config = array(
            'indent' => true,
            'output-xml' => true,
            'input-xml' => true,
            'wrap' => '80', );
        $input = $_REQUEST['tidy'];
        $tidy = new tidy();
        $output = $tidy->repairString($input, $config, 'utf8');
        $template->assign('tidy', $output);
    }
}
$admin_tidy = new adminTidy();
