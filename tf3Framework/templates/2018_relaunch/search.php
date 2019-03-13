<?php include 'header.php'?>
<div class="container">
    <?php if ($this->view['result']): ?>
    <ul>
        <?php foreach ($this->view['result'] as $r => $row) : ?>
        <li>
            <a href="<?php echo $row['url'] ?>/">
                <?php echo $row['title'] ?>
            </a>
            <?= $row['content'] ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <?= i18n('Leider hat deine Suche nach %s kein Ergebnis geliefert. Das ist ja doof. Aber tröste dich, es gibt hier noch viel mehr zu sehen.', $this->view['suchbegriff']); ?>

        <?php endif; ?>
</div>
<?php include 'footer.php'?>