<!doctype html>
<html lang="de">

<head>
    <?php assets('roboto-v18-latin-regular.eot'); ?>
    <?php assets('roboto-v18-latin-regular.svg'); ?>
    <?php assets('roboto-v18-latin-regular.ttf'); ?>
    <?php assets('roboto-v18-latin-regular.woff'); ?>
    <?php assets('roboto-v18-latin-regular.woff2'); ?>
    <link rel="stylesheet" type="text/css" href="<?= assets('admin.css') ?>" />
    <script type="text/javascript" src="<?=path('js/tlog.back.js')?>"></script>
    <meta charset="UTF-8">
    <title>Administration</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript">
        var WEBROOT = '<?=WEB?>';
        var MEDIADIR = 'web/_media/';
    </script>

</head>

<body>
    <nav>
        <div class="container">
            <div class="row">
                <div class="two columns">
                    <a href="<?= WEB ?>admin">
                        <img src="<?= assets('tlogW.svg') ?>" alt="t-log" height="32" />
                    </a>
                </div>
                <div class="ten columns">
                    <div class="u-pull-right">
                        <a href="<?= WEB ?>profile/logout">
                            <?= $this->view['USER']->getName() ?> ausloggen
                        </a>
                        <a class="" href="<?= WEB ?>" target="_blank">
                            Zur Seite
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </nav>
    <div>
        <div class="row">
            <div class="two columns leftMenu sticked">
                <a class="first" href="<?= WEB ?>admin">
                    <i class="fa fa-home"></i>
                    Dashboard</a>
                <a class="first" href="<?= $this->view['PAGEROOT'] ?>admin/cms">
                    <i class="fa fa-pencil-alt"></i>
                    <?= i18n('Contentseiten') ?>
                </a>

                <a class="first" href="<?= $this->view['PAGEROOT'] ?>admin/blog/list">
                    <i class="fa fa-newspaper"></i>
                    <?= i18n('Blogbeiträge'); ?>
                </a>
                <a class="first" href="<?= WEB ?>admin/zen">
                    <i class="fa fa-code"></i> Zen-Modus
                </a>

                <a class="first" href="<?= WEB ?>admin/filer">
                    <i class="fa fa-image"></i> Mediendaten
                </a>
                <a class="first" href="<?= $this->view['PAGEROOT'] ?>admin/comments">
                    <i class="fa fa-comment"></i>
                    <em>
                        <?= $this->view['stats']['numComments'] ?>
                    </em>
                    <?= i18n('Kommentare') ?>
                </a>

                <hr>
                <a href="<?= WEB ?>admin/plugins" class="first">
                    Plugins
                </a>
                <hr>
                <a href="<?= WEB ?>profile/logout">
                    Logout
                </a>

                <div>
                    <hr>
                    <a href="https://www.trancefish.de/">
                        Powered by tlog</a>
                </div>
            </div>
            <div class="ten columns mainContents">