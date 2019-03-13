<?php include 'header.php' ?>
<?php if ($this->view['archive']): ?>
<div class="container">
    <?php include 'blogarchive.php'; ?>
</div>
<?php endif; ?>

<?php if ($this->view['blogposts']): ?>
<?php foreach ($this->view['blogposts'] as $row => $value) : ?>
<?php if ($this->view['show_comments']) :?>
<div id="lintro" class="intro" style="background-image:url(<?php echo $this->view['PAGEROOT'] ?>thumb/Img/1350/600/<?php echo $value['headimg'] ?>)">
        <h1>
            <?=$value['title']?>
        </h1>
</div>
<?php endif; ?>

<div class="container">
    <div class="row">
        <div class="nine columns">
            <?php if (!$this->view['show_comments']):?>
            <a href="<?= WEB ?>blog/show/<?= urlencode($value['category']) ?>/<?= urlencode($value['title']) ?>/">
                <h2>
                    <?= $value['title'] ?>
                </h2>
            </a>

            <?php endif;?>

            <?php if ($this->view['show_comments']):?>
            <h2>
                <?= $value['title'] ?>
            </h2>
            <?php endif;?>
            <?php if ($this->view['show_comments']):?>
            <div class="blogpost">
                <?php endif;?>
                <?=$value['contents']?>
                    <?php if ($this->view['show_comments']):?>
            </div>
            <?php endif;?>

            <?php if ($this->view['show_comments']):?>
            <?php include('comment.php') ?>
            <?php endif;?>
            <?php if ($this->view['similar_posts']) : ?>
            <div class="meta">
                Ähnliche Posts
                <?php foreach ($this->view['similar_posts'] as $row2 => $value2) : ?>
                <a href="<?= $this->view['PAGEROOT'] ?>blog/show/<?php echo urlencode($value2['category']) ?>/<?php echo urlencode($value2['title']) ?>/">
                    <?= $value2['title'] ?> &middot;
                </a>


                <?php endforeach; ?>
                <a href="<?= WEB ?>search/?q=<?= urlencode($value2['category']) ?>">
                    <?= $value2['category'] ?>
                </a>
            </div>
            <?php endif; ?>


        </div>
        <div class="three columns subItems">
            <?php if (!$this->view['show_comments']) :?>
            <img class="u-full-width" src="/assetsdir/ajax-loader.gif" alt="<?= $value['title'] ?>" data-src="<?php echo $this->view['PAGEROOT'] ?>thumb/Img/200/200/<?php echo $value['headimg'] ?>"
            />
            <?php endif; ?>
            <?php if ($this->view['show_comments']): ?>
            <?php include 'menu.php' ?>

            <?php endif;?> Lesezeit:
            <?=$value['readtime']?> Minuten
        </div>
    </div>
</div>
<?php endforeach;?>
<?php endif;?>
<?php if ($this->view['show_comments']): ?>
<script>
    window.onscroll = function () {
        changeMenu()
    }

    function changeMenu() {
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

        var scrollBarPosition = window.pageYOffset | document.body.scrollTop;
        if (w > 550) {
            var intro = document.getElementById('lintro');
            intro.style.filter = 'blur(' + scrollBarPosition / 50 + 'px)';
        }

    }
</script>
<?php endif; ?>

<?php include 'footer.php' ?>