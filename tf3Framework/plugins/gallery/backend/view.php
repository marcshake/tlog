<?php $gimgClass = 'galleryStyle'.time(); ?>

<style type="text/css">
    .<?= $gimgClass ?> {
        position:relative;
        width:128px;
        height:196px;
        float:left;
    }
    .<?= $gimgClass ?> a {
        clear:both;
    }

</style>

<h2>Liste der Galerien</h2>
<div class="row">
    <div class="eight columns">
        <?php foreach ($this->view['galleries'] as $c => $gal): ?>
            <a class="button" href="<?= WEB ?>admin/plugins/gallery/backend/view/<?= $c ?>">
                <?= $gal ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="four columns">
        <form method="post" action="<?= WEB ?>admin/plugins/gallery/backend/new">
            <div class="row">
                <div class="two columns">
                    Neue Galerie:
                </div>
                <div class="ten columns">
                    <input type="text" name="newGal" class="u-full-width" placeholder="Neue Galerie"/>
                </div>
            </div>
            <input type="submit" value="anlegen" name="anlegen"/>
        </form>

    </div>
</div>
<?php if ($this->view['details']): ?>
<form action="<?=WEB?>admin/plugins/gallery/backend/editmeta/" method="post">
<input type="hidden" name="galleryID" value="<?=$this->view['details']['galleryID']?>" />
<input type="hidden" name="imageID" value="<?=$this->view['details']['imageID']?>" />
<input type="hidden" name="bildname" value="<?$this->view['details']['bildname']?>"/>
    <div class="row">
        <div class="four columns">
            <img src="<?=$this->view['details']['URL']?>" class="u-full-width"/>
        </div>
        <div class="eight columns">
        <input type="text" class="u-full-width" placeholder="Bildtitel" name="bildtitel" value="<?=$this->view['details']['title']?>" />
        <br/>
        <textarea class="ckeditor megaheight" name="edit_content" id="editor" rows="50"
              cols="80"><?php echo htmlentities($this->view['details']['contents']) ?></textarea>
              <input type="submit" name="Save" class="button u-full-width" /> 
        </div>
    </div>
</form>
<?php endif; ?>

<?php if ($this->view['images']): ?>
    <h3>Bilder</h3>
    <?php foreach ($this->view['images'] as $c => $img): ?>
        <div class="<?= $gimgClass ?>">
            <img src="<?= WEB ?>thumb/img/128/128/<?= WEB ?>gallery/<?= $this->view['path'] ?>/<?= $img ?>"><br/>
            <a href="<?= WEB ?>admin/plugins/gallery/backend/delete/<?= $this->view['galleryid'] ?>/<?= $c ?>" onclick="return doconfirm('Löschen?');">
                <i class="fa fa-trash"></i>
            </a>
            <a href="<?= WEB ?>admin/plugins/gallery/backend/edit/<?= $this->view['galleryid'] ?>/<?= $c ?>">
                <i class="fa fa-pencil"></i>
            </a>

        </div>
    <?php endforeach; ?>
<?php else:?>
    <?php if ($this->view['galleryid']):?>
    Ganze Galerie löschen?
                        <a href="<?= WEB ?>admin/plugins/gallery/backend/deletegal/<?= $this->view['galleryid'] ?>" onclick="return doconfirm('Löschen?');">
                <i class="fa fa-trash"></i>
            </a>

        <?php endif; ?>
    
<?php endif; ?>
    
<?php if ($this->view['addImages']): ?>
    <form action="<?= WEB ?>admin/plugins/gallery/backend/upload" enctype="multipart/form-data" method="post">
        <input type="hidden" name="galleryid" value="<?= $this->view['galleryid'] ?>"/>
        <input type="file" name="newfile[]" multiple="multiple">
        <input type="submit" value="Hochladen"/>
    </form>

<?php endif; ?>

