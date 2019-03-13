<?php include 'header.php'?>
<div class="entry_content container blogpost">
    <?php if ($this->view['do_index']) : ?>
        <article><header>
                <?php echo $this->view['do_index']['describe']; ?>
            </header><div class="textelemente">
                <ul>
                    <?php foreach ($this->view['do_index']['topics'] as $v => $row) : ?>
                        <li><a href="<?php echo $this->view['PAGEROOT']; ?>page/cms/<?php echo $this->view['do_index']['title_url']; ?>/<?php echo $row['url']; ?>">
                                <?php echo $row['title']; ?>
                            </a></li>
                    <?php endforeach; ?>
                </ul></div>
        </article>
    <?php endif; ?>


    <?php if ($this->view['contents']) : ?>
        <article><header>

            </header><div class="textelemente">
                <?php echo $this->view['contents']['contents'] ?></div>
        </article>

    <?php endif; ?>
</div>
<?php include 'footer.php'?>
