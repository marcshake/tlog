<?php include 'header.php' ?>
<?php $x = 0; ?>
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


<div class="container">
    <?php if (!$this->view['show_comments']): ?>
    <?php $x++; ?>
    <div class="row overview">
    <?php if ($x%2==0): ?>
        <div class="four columns">
            <img class="u-full-width" src="/assetsdir/ajax-loader.gif" alt="<?= $value['title'] ?>" data-src="<?php echo $this->view['PAGEROOT'] ?>thumb/Img/400/300/<?php echo $value['headimg'] ?>" />
        </div>
        <div class="eight columns">
            <a href="<?= WEB ?>blog/show/<?= urlencode($value['category']) ?>/<?= urlencode($value['title']) ?>/">
                <h2>
                    <?= $value['title'] ?>
                </h2>
            </a>
            <?=$value['contents']?>
        </div>
        <?php else:?>
        <div class="eight columns">
            <a href="<?= WEB ?>blog/show/<?= urlencode($value['category']) ?>/<?= urlencode($value['title']) ?>/">
                <h2>
                    <?= $value['title'] ?>
                </h2>
            </a>
            <?=$value['contents']?>
        </div>
        <div class="four columns">
            <img class="u-full-width" src="/assetsdir/ajax-loader.gif" alt="<?= $value['title'] ?>" data-src="<?php echo $this->view['PAGEROOT'] ?>thumb/Img/400/300/<?php echo $value['headimg'] ?>" />
        </div>

        <?php endif; ?>

    </div>
    <?php endif; ?>

    <?php if ($this->view['show_comments']):?>
    <div class="row">

        <div class="twelve columns">

            <h2>
                <?= $value['title'] ?>
            </h2>
            <div class="blogpost">
                <?=$value['contents']?>
            </div>
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
        </div>
        <?php endif; ?>

    </div>
    <div class="subItems">
        <?php if ($this->view['show_comments']): ?>
        <?php include 'menu.php' ?>

        Lesezeit:
        <?=$value['readtime']?> Minuten
    </div>
    <?php endif;?>

</div>
<?php endforeach;?>
<?php endif;?>

<?php include 'footer.php' ?>