<?php include 'head.php'?>
<?php if ($this->view['plugins']): ?>
    <ul>
        <?php foreach ($this->view['plugins'] as $key => $plugin) : ?>
            <li><a href="<?= WEB ?>admin/plugins/<?= $key ?>/backend"><?= $plugin['name'] ?><br/>
                    <?= $plugin['description'] ?>
                </a> </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if ($this->view['pluginview']): ?>

    <?php include $this->view['pluginview']; ?>

<?php endif; ?>

<?php include 'foot.php'?>
