<?php include 'head.php'?>
<?php foreach ($this->view['pagelist'] as $row => $data): ?>

    <a data-id="<?= $data['category']['cat_id'] ?>" href="#"
       class="chooser button<?= isset($this->view['page_editor']['cat_id']) && ($this->view['page_editor']['cat_id'] == $data['category']['cat_id']) ? ' button-primary' : '' ?> "><?= $data['category']['handle'] ?>
    </a>
<?php endforeach; ?>
<a data-id="newfolder" class="button chooser" href="#">
    Neuer Ordner
</a>

<?php reset($this->view['pagelist']); ?>
<?php foreach ($this->view['pagelist'] as $row => $data): ?>

    <div data-cat="<?= $data['category']['cat_id'] ?>"
         class="contentlist"<?= $this->view['page_editor']['cat_id'] == $data['category']['cat_id'] ? 'style="display:block"' : '' ?>>
             <?php foreach ($data['inhalt'] as $r => $contents): ?>
            <div class="row <?=$r % 2 == 0 ? 'odd' : 'even'?>">
                <div class="nine columns">
                    <a href="<?= WEB ?>admin/cms/page/edit/<?= $contents['p_id'] ?>">
                        <?= $contents['handle'] ?>:

                        <?= $contents['title'] ?>
                    </a>
                </div>
                <div class="three columns">
                    <a href="#" class="button">
                        <i class="fa-eye<?= $contents['visible'] == 1 ? '' : '-slash'; ?> fa"></i>
                    </a>
                    <a href="<?= WEB ?>admin/cms/page/delete/<?= $contents['p_id'] ?>" class="button confirmation">
                        <i class="fa-trash fa"></i>
                    </a>
                    <a href="<?= $preview ?>" class="button">
                        <i class="fa fa-newspaper"> </i>
                    </a>

                </div>
            </div>
        <?php endforeach; ?>
        <div class="row">
            <a href="<?= WEB ?>admin/cms/page/new/<?= $contents['cat_id'] ?>" class="button button-primary">
                Neue Seite
            </a>
        </div>
        <?php if (isset($this->view['page_editor']) && $this->view['page_editor']['cat_id'] == $contents['cat_id']): ?>
            <form action="<?php echo $this->view['PAGEROOT'] ?>admin/cms/page/<?= $this->view['action'] ?>"
                  method="post"
                  enctype="multipart/form-data">
                <input type="hidden" name="p_id" value="<?php echo $this->view['page_editor']['p_id'] ?>"/>

                <div class="row">
                    <div class="three columns">
                        Dateiname:
                    </div>
                    <div class="nine columns">
                        <input type="text" class="u-full-width" placeholder="Dateiname"
                               value="<?= $this->view['page_editor']['handle'] ?>" name="handle"/>
                    </div>
                </div>


                <div class="row">
                    <div class="three columns">
                        Seitentitel:
                    </div>
                    <div class="nine columns">
                        <input type="text" class="u-full-width" placeholder="Seitentitel"
                               value="<?= $this->view['page_editor']['title'] ?>" name="title"/>
                    </div>
                </div>
                <div class="row">
                    <div class="three columns">
                        MetaInfos
                    </div>
                    <div class="three columns">
                        <input type="text" class="u-full-width" placeholder="keywords"
                               value="<?= $this->view['page_editor']['keywords'] ?>" name="keywords"/>
                    </div>
                    <div class="three columns">
                        <input type="text" class="u-full-width" placeholder="description"
                               value="<?= $this->view['page_editor']['description'] ?>" name="description"/>
                    </div>
                    <div class="three columns">
                        <select name="cat_id"
                                class="u-full-width"><?php foreach ($this->view['categories'] as $id => $value) : ?>

                                <option
                                    value="<?= $id ?>"<?= ($this->view['page_editor']['cat_id'] == $id) ? $checked = ' selected="true"' : false; ?>><?= $value['handle'] ?></option>
                                <?php endforeach; ?>
                        </select>
                        <select name="lvl">
                            <?php foreach ($this->view['USER']->get_levels() as $lvl => $description):?>
                                <option <?=$this->view['page_editor']['lvl'] == $lvl ? ' selected="true"' : ''?> value="<?=$lvl?>"><?=$description?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                </div>


                <div class="row">
                    <div class="three columns">
                        Einleitungstext:
                    </div>
                    <div class="nine columns">

                        <div class="showTeaser">
                            <textarea name="teaser"
                                      class="ckeditor"><?= htmlspecialchars_decode($this->view['page_editor']['teaser']) ?></textarea>
                        </div>

                    </div>
                    <a href="#" id="entryText" class="button">
                        bearbeiten
                    </a>

                </div>
                <div class="row">
                    <div class="three columns">
                        Haupttext:
                    </div>
                    <div class="nine columns">
                        <textarea class="ckeditor" name="contents" cols="80"
                                  rows="25"><?php echo htmlspecialchars($this->view['page_editor']['contents']) ?></textarea>

                    </div>
                </div>
                <div class="row">
                    <div class="twelve columns">
                        <input type="submit" name="save" value="Speichern"/>
                    </div>
                </div>

            </form>

        <?php endif; ?>


    </div>
<?php endforeach; ?>
<div data-cat="newfolder" class="contentlist">
    <form action="<?= WEB ?>admin/cms/category/new_save" method="post">
        <div class="row">
            <div class="three columns">
                Ordnername:
            </div>
            <div class="nine columns">
                <input type="text" class="u-full-width" name="handle" value="" placeholder="Ordnername"/><br/>

            </div>
        </div>
        <div class="row">
            <div class="three columns">
                Beschreibender Text:
            </div>
            <div class="nine columns">
                <textarea row="25" cols="80" class="u-full-width" name="category_description"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="twelve columns">
                <input type="submit" name="save" value="speichern">
            </div>
        </div>

    </form>
</div>

<?php include 'foot.php'?>

