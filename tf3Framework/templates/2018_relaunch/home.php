<?php include 'header.php'?>

<div class="container home">
    <div class="row">
        <div class="four columns">
        <h3>beliebteste Beiträge</h3>
            <?php foreach ($this->view['posts'] as $row => $value): ?>

            <figure class="photo">
                <a title="<?= $value['title'] ?>" href="<?php echo $this->view['PAGEROOT'] ?>blog/show/<?php echo urlencode($value['category']) ?>/<?php echo urlencode($value['title']) ?>/">

                    <img src="/assetsdir/ajax-loader.gif" data-src="<?php echo $this->view['PAGEROOT'] ?>thumb/Img/300/200/<?php echo $value['headimg'] ?>"
                        class="u-full-width imgShad" alt="<?= $value['title'] ?>" />
                </a>

                <figcaption>
                    <?=$value['contents_short']?>
                </figcaption>
            </figure>
            <?php endforeach; ?>
        </div>
        <div class="eight columns">
            <?= $this->view['contents'] ?>

        </div>
    </div>
</div>


<?php include 'footer.php' ?>