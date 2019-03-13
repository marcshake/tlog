
<?php $count = 0; ?>
<div class="gallery">
    <?php foreach ($this->view['images'] as $c => $img): ?>
        <?php if ($count == 0): ?>
            <div class="row">

        <?php endif; ?>
        <div class="three columns">
            <a>
            <span>
            <?=$this->view['meta'][$c]->title?>
            <?=$this->view['meta'][$c]->contents?>
            
            </span>
                <img class="lazy u-full-width" src="<?= WEB ?>thumb/img/300/416/<?= $this->view['path'] ?>/<?= $img ?>">
                
            </a>
        </div>
                <?php $count++?>
                <?php if ($count == 4) {
    $count = 0;
} ?>
        <?php if ($count == 0): ?>
            </div>
           <?php endif ?>
    <?php endforeach; ?>

</div>