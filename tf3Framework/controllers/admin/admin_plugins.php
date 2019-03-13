<?php

class adminplugins
{
    private $plugins;

    // Get list of Available Plugins
    public function __construct()
    {
        global $template;
        $plugindir = DROOT.'/plugins';
        $x = 0;
        $out = false;
        $files = array_diff(scandir($plugindir), array('..', '.'));
        foreach ($files as $dir) {
            // Load the Code
            if (file_exists($plugindir.'/'.$dir.'/code/plugin.php')) {
                include $plugindir.'/'.$dir.'/code/plugin.php';
                $this->plugins[$dir] = new $dir();
                $out[$dir]['name'] = $this->plugins[$dir]->name;
                $out[$dir]['description'] = $this->plugins[$dir]->description;
            }
        }
        $template->assign('plugins', $out);
    }

    public function doPlugins($params)
    {
        if (isset($params[0]) and isset($this->plugins[$params[0]])) {
            $this->plugins[$params[0]]->backend($params);
        }
    }
}

$plugs = new adminplugins();
$plugs->doPlugins($params);
