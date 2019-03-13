<form action="<?php echo $this->view['PAGEROOT']; ?><?php echo $this->view['ADMIN_ACTION'] ?>" method="post"
      enctype="multipart/form-data">
      <div class="row">
<div class="nine columns">

    <input type="text" name="edit_title" value="<?php echo $this->view['edit_title'] ?>" class="cms u-full-width"
           placeholder="Seitentitel"/>


    <textarea class="ckeditor megaheight" name="edit_content" id="editor" rows="50"
              cols="80"><?php echo htmlentities($this->view['edit_content']) ?></textarea>
    </div>
    <div class="three columns">
    <input placeholder="Tags" type="text" name="tags" value="<?php echo $this->view['edit_tags'] ?>" class="u-full-width" autocomplete="false" id="tags"/>

        <div class="categories">
        <?php if ($this->view['tags']) : ?>

            <?php foreach ($this->view['tags'] as $r => $row) : ?>
                <label>
                    <input <?=in_array($row['cat_id'], $this->view['checked']) ? 'checked="checked"' : '' ?> class="useTag" type="checkbox" name="use_tag" value="<?php echo $row['handle'] ?>"/><?php echo $row['handle'] ?>
                </label>
            <?php endforeach; ?>
        <?php endif; ?>

        </div>
        <div>
            <label for="ogimage">Artikelbild</label>

            <?php $this->view['edit_ogimage'] = empty($this->view['edit_ogimage']) ? assets('blank.png') : $this->view['edit_ogimage']; ?>
            <img class="u-full-width" src="<?= $this->view['edit_ogimage'] ?>" title="Vorschau" id="previewImage"/>


            <input id="ogimage" type="text" name="ogimage" value="<?php echo $this->view['edit_ogimage'] ?>" class="u-full-width"
                   autocomplete="false" placeholder="Titelbild"/>
            <a href="#" id="openbrowser" class="button" title="Dateimanager öffnen und Bild auswählen">
                Artikelbild 
            </a>
            </div>
            <div>
            <a href="<?php echo $this->view['PAGEROOT']; ?>admin/blog/change_status/<?php echo $this->view['row']['p_id'] ?>"
               class="button" title="<?= $this->view['row']['vis'] == 1 ? 'Sichtbar' : 'Unsichtbar'; ?>">
               <?= $this->view['row']['vis'] == 1 ? 'Sichtbar' : 'Unsichtbar'; ?>
            </a>

            <a class="button confirmation" href="<?php echo $this->view['PAGEROOT']; ?>admin/blog/deleteblog/<?php echo $this->view['row']['p_id'] ?>">
                <i class="fa fa-trash"> </i>
            </a>
            <input type="text" placeholder="password" name="password" value="<?= isset($row['password']) ? $row['password'] : null ?>" />
            </div>
            <div>
            <input type="submit" name="save" value="Speichern" class="button button-primary u-full-width"/>
            </div>
        </div>
    </div>

</form>

