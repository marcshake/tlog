<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="<?= assets('admin.css') ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .centerbox {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            overflow: auto;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 2px 2px 10px #000;
            cursor: pointer;
            z-index: 10;
        }
    </style>

</head>
<body>

<form action="<?php echo $this->view['PAGEROOT'] ?>profile/login" method="post" class="centerbox">
    <div class="row">
        <div class="four columns">
            <img src="<?= assets('tlog.svg') ?>" alt="logo" width="128"/>
        </div>
        <div class="eight columns">
            <input type="hidden" name="tok" value="<?= $this->view['USER']->crsf() ?>"/>
            <input type="text" name="uname" placeholder="<?= i18n('Benutzername') ?>"/><br/>
            <input type="password" name="upass" placeholder="<?= i18n('Passwort') ?>"/>
            <input type="submit" value="Login" name="<?= i18n('Einloggen') ?>"/>
        </div>
    </div>
</form>

</body>
</html>