<!DOCTYPE html>
<html lang="en">
<head>
<?php assets('roboto-v18-latin-regular.eot'); ?>
        <?php assets('roboto-v18-latin-regular.svg'); ?>
        <?php assets('roboto-v18-latin-regular.ttf'); ?>
        <?php assets('roboto-v18-latin-regular.woff'); ?>
        <?php assets('roboto-v18-latin-regular.woff2'); ?>
        <link rel="stylesheet" type="text/css" href="<?= assets('admin.css') ?>"/>
        <meta charset="UTF-8">
        <style type="text/css">
        body {
        	height: 100%;
        	margin-top: 2rem;
        	background-image: url(<?=assets('zen.jpg')?>);
        	background-repeat: no-repeat;
        	background-size: cover;
        }
        .zen {
        	min-height: 80vh;
            font-family: Big Caslon, Book Antiqua, Palatino Linotype, Georgia, serif;
        	background-color: rgba(255, 255, 255, .5);
        	color: #000;
            box-shadow:5px 5px 20px #000;
        }
        .stats {
            padding:1rem .5rem;
        	background-color: rgba(255, 255, 255, .5);
            box-shadow:5px 5px 20px #000;
            border-radius:5px;
        }
        .stats span {
            color:purple;
        }
        .edititle {
            box-shadow:5px 5px 20px #000;
            font-family: Big Caslon, Book Antiqua, Palatino Linotype, Georgia, serif;
        	background-color: rgba(255, 255, 255, .5)!important;
        	color: #000;
        }
        </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZEN</title>
</head>
<body>
<div class="container">
<div class="row">
<div class="three columns">
<div class="stats">
<a href="<?=WEB?>admin/">
ZEN verlassen
</a>

<h2>Statistiken</h2>
Wörter: <span id="cwords">0</span><br/>
Absätze: <span id="cpara">0</span>
</div>
</div>
<div class="two columns">
&nbsp;
</div>

<div class="seven columns">
<?php
$action = 'newblog';
$title = '';
$contents = '';
if (isset($this->view['entry'])) {
    $action = 'saveblog';
    $title = $this->view['entry']['title'];
    $contents = $this->view['entry']['contents'];
}
?>
<form action="<?= $this->view['PAGEROOT'] ?>admin/blog/<?=$action?>"
              enctype="multipart/form-data" method="post">
              <input type="hidden" name="zen" value="1"/>
              
            <input class="u-full-width edititle" name="edit_title" placeholder="<?= i18n('Seitentitel') ?>" type="text" value="<?=$title?>">
            <textarea class="zen u-full-width" cols="80" name="edit_content" placeholder="<?= i18n('Text') ?>" rows="50" onkeyup="countwords()" id="cblock"><?=$contents?></textarea>
            <input class="button button-primary" name="go" type="submit" value="<?= i18n('Speichern') ?>">
        </form>
        </div></div>
</div>
<script type="text/javascript">
var countwords = function() {
    var words = 0;
    var ta = document.getElementById('cblock');
    var cout = document.getElementById('cwords');
    var cabs = document.getElementById('cpara');
    var str = ta.value;
    var anzahl = str.split(' ').length;
    var abs = str.split('\n\n').length;
    cout.innerHTML = anzahl;
    cabs.innerHTML = abs;

};
</script>

</body>
</html>