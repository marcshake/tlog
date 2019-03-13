<?php include 'head.php'?>
    <h3><?=i18n('Kommentare')?></h3>
    <div class="infobox">
        <?php if ($this->view['comment_editor']) : ?>
        <?php rsort($this->view['comment_editor']); ?>
            <dl>
                <?php foreach ($this->view['comment_editor'] as $row => $blogdata) : ?>
                <dt><h3><?= $blogdata['title'] ?></h3></dt>
                <dd>
                    <?php foreach ($blogdata['comments'] as $rw => $row) : ?>
                        <table class="u-full-width" width="100%">
                            <thead>
                                <tr>
                                    <th width="33%"><?= $row['user'] ?></th>
                                    <th width="33%"><?php if ($row['url']): ?>
                                            <a href="<?= $row['url'] ?>" target="_blank"><?= $row['url'] ?></a>
                                        <?php else: ?>
                                            <?=i18n('keine Homepage'); ?>
                                        <?php endif; ?></th>
                                    <th width="33%"><?php echo date('d.m.Y', $row['stamp']); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <?= nl2br($row['comment']); ?>
                                        <?php if ($this->view['USER']->get_level() == 1): ?>
                                            <p>
                                                <a href="<?= $this->view['PAGEROOT'] ?>admin/comments/delete/<?= $row['cid'] ?>" class="button button-primary"><?=i18n('Löschen')?></a>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    <?php endforeach; ?>
                </dd>
            <?php endforeach; ?>
            </dl>
        <?php endif; ?>
    </div>
<?php include 'foot.php'?>