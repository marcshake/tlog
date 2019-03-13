<?php

class captcha
{
    public function doDefault()
    {
        return false;
    }

    public function doImage()
    {
        $s = $this->randomizer();
        $img = imagecreate(100, 30);
        $bg = imagecolorallocate($img, 0, 0, 0);
        $fg = imagecolorallocate($img, 255, 255, 255);

        imagestring($img, 2, 5, 10, $s, $fg);
        header('Content-Type:image/jpg');
        imagejpeg($img);
        exit();
    }

    public function randomizer()
    {
        $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $l = strlen($string);
        $t = 0;
        $capval = '';
        while ($t <= 6) {
            $x = rand(0, $l);
            $capval .= $string{$x};
            ++$t;
        }
        $_SESSION['cap'] = $capval;

        return $capval;
    }
}
