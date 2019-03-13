<?php
use tf3Framework\lib\Controller;

?>
    <?php if (!$this->view['ajax']) :?>
    <!DOCTYPE html>
    <html lang="de">

    <head>
        <meta charset="UTF-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <meta name="robots" content="index,follow">
        <link rel="alternate" type="application/rss+xml" title="RSS Feed for trancefish.de" href="<?= $this->view['PAGEROOT'] ?>blog/rss"
        />
        <!-- Integration von Social-Media //-->
        <link rel="icon" href="<?php echo $this->view['PAGEROOT'] ?>favicon.png" type="image/png" />
        <title>
            <?php echo $this->view['PAGETITLE'] ?> - trancefish.de
        </title>
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@trancefish_de" />
        <meta name="twitter:creator" content="@trancefish_de" />
        <meta name="twitter:title" content="<?php echo $this->view['PAGETITLE'] ?>">
        <?php if (isset($this->view['OGIMAGE'])) : ?>
        <meta property="og:image" content="<?= $this->view['OGIMAGE'] ?>">
        <?php endif; ?>
        <?php if (isset($this->view['DESCRIPTION'])) : ?>
        <meta name="description" content="<?php echo $this->view['DESCRIPTION'] ?>" />
        <meta name="og:description" content="<?php echo $this->view['DESCRIPTION'] ?>" />
        <?php else: ?>
        <?php endif; ?>
        <?php if (isset($this->view['KEYWORDS'])) : ?>
        <meta name="keywords" content="<?php echo $this->view['KEYWORDS'] ?>" />
        <?php endif; ?>
        <link rel="stylesheet" href="/assetsdir/styles.css" />
    </head>

    <body>
        <nav>

            <div class="container">
                <div class="row">
                    <div class="one column">
                        <a href="<?=WEB?>" id="logo">
                            <img src="<?=WEB?>assetsdir/logo.svg" alt="Startseite" />
                        </a>
                    </div>
                    <div class="eleven columns">

                        <form action="<?= $this->view['PAGEROOT'] ?>search" method="get">
                            <input type="text" name="q" placeholder="Suche..." class="u-full-width">
                            <?=Controller::loadMenus();?>

                        </form>

                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper" id="mainC">
            <?php endif;?>
