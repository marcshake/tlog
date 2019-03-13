<?php include 'head.php' ?>
<div class="row">
    <div class="eight columns">
    <?php if ($this->view['files']) : ?>
        <?php foreach ($this->view['files'] as $r => $v): ?>
            <figure class="u-pull-left thumb">
                <img data-url="<?=path('web/_media/').$v['thumbnail']?>" class="thumbclick" data-src="<?=WEB?>Thumb/img/300/200/<?=path('web/_media/').$v['thumbnail']?>" alt="<?=$v['thumbnail']?>">
            </figure>
        <?php endforeach; ?>
    <?php else: ?>
    die Medienbibliothek ist leer
    <?php endif; ?>
    </div>
    <div class="four columns">
        <form action="<?php echo $this->view['PAGEROOT']; ?>admin/filer/upload"
                method="post" enctype="multipart/form-data" class="white_bg">
                    <?= i18n('Starte Upload') ?>
                <input type="file" name="newfile[]" multiple="true" class="button"/>
                <input type="submit" value="Beginne Upload" name="upload" class="button"/>
        </form>
        <input type="text" id="url" class="u-full-width">
    </div>
</div>
<?php include 'foot.php' ?>