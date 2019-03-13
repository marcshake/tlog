<?php include 'header.php'?>
<div class="container">
<div class="nine columns">
<?php if ($this->view['albums']) : ?>
    <div class="row">
        <?php $active = _request('media', 0); ?>
        <?php foreach ($this->view['albums'] as $row => $data) : ?>
            <a class="u-pull-left" href="?media=<?php echo $data['alb_id'] ?>">
                <img src="//www.trancefish.de/web/images/album_artwork/<?= $data['artwork'] ?>"/><br/>
                <?= $data['alb_title'] ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ($this->view['tracks']): ?>
    <div class="row">
        <b><?= i18n('Tracks auf diesem Album') ?></b>
    </div>
    <?php foreach ($this->view['tracks'] as $row => $value) : ?>
        <div class="row">
            <div class="five columns">
                <a href="//www.trancefish.de/web/music/<?= $value['filename'] ?>">
                    <?= $value['filename'] ?></a>

            </div>
            <div class="seven columns">
                <?php if ($value['action'] == true): ?>
                    <audio class="u-full-width" controls preload="none">
                        <source src="//www.trancefish.de/web/music/<?= $value['filename'] ?>"
                                type="<?= $value['action'] ?>">
                    </audio>

                <?php endif; ?>


            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
<div class="three columns">
<?php include 'menu.php' ?>
</div>
</div>

<?php include 'footer.php'?>
