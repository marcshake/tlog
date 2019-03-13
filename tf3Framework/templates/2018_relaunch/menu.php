
<div class="menuItems">

<h3>Menu</h3>

<a href="<?=WEB?>blog">Blog</a>
<a href="<?=WEB?>music">Music</a>
<a href="<?=WEB?>blog/show/Linux">
Linux
</a>
<a href="<?=WEB?>blog/archive">
Archive
</a>
<?php if (isset($this->view['moremenus'])) : ?>

<h4>
    Kategorien
</h4>
<?php foreach ($this->view['moremenus'] as $row => $vale) : ?>
    <a href="<?= WEB ?>blog/show/<?= urlencode($vale['handle']) ?>"><?= $vale['handle'] ?></a>
<?php endforeach; ?>
<?php endif; ?>

</div>

