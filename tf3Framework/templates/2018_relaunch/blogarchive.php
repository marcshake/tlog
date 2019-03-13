    <?php foreach ($this->view['archive'] as $year => $month): ?>
        <dl>
            <dt><?= $year ?></dt>
            <dd>
                <?php foreach ($month as $thismonth => $postings): ?>
                    <b><?= $thismonth ?></b>
                    <?php foreach ($postings as $data): ?>
                        <ul>
                            <?php foreach ($data as $hour => $content): ?>
                                <li>
                                    <a href="<?= WEB ?>blog/show/<?= $content['url'] ?>">
                                        <?= $content['title'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            </dd>
        </dl>
    <?php endforeach; ?>

