<?php include 'head.php'; ?>
<h2>
    Dashboard
</h2>
<div class="row">
    <div class="six columns">
        <h2>Neuer Blogeintrag</h2>
        <form action="<?= $this->view['PAGEROOT'] ?>admin/blog/newblog"
              enctype="multipart/form-data" method="post">
            <input class="u-full-width" name="edit_title" placeholder=
                   "<?= i18n('Seitentitel') ?>" type="text">
            <textarea class="u-full-width ckeditor" cols="80" name=
                      "edit_content" placeholder="<?= i18n('Text') ?>" rows="25">
            </textarea> <input class="button button-primary" name="go" type="submit"
                               value="<?= i18n('Speichern') ?>">
        </form>
    </div>

    <div class="three columns">

        <h3>Pflege</h3>
        <p>
            <a class="button" href="<?= $this->view['PAGEROOT'] ?>admin/purge"><?= i18n('Caches leeren'); ?></a>
        </p>
    </div>
    <div class="three columns">
        <h3>Übersicht</h3>
        <ul>
            <li><?= $this->view['stats']['numBlogs'] ?>
                <a href="<?= WEB ?>admin/blog/list">Blogs
                </a>
            </li>
            <li>
                <?= $this->view['stats']['numComments'] ?>
                <a href="<?= WEB ?>admin/comments">Kommentare</a>
            </li>
            <li>
                <?= $this->view['stats']['numPages'] ?>
                <a href="<?= WEB ?>admin/cms">Contentseiten</a>
            </li>

        </ul>

    </div>
</div>

<?php include 'foot.php'; ?>