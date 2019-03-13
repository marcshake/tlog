<?php if (!$this->view['ajax']): ?>
<?php if (!$this->view['show_comments']):?>
<?php if (!empty($this->view['pagelist'])) : ?>
<div class="container">
    <?php echo $this->view['pagelist'] ?>
</div>
<?php endif; ?>
<?php endif; ?>
<hr>
<footer>
    <div class="container">
        <div class="row">
            <?php foreach ($this->view['quickload'] as $row => $value) : ?>
            <div class="four columns">
                <b>
                    <?php echo $value['title'] ?>
                </b>
                <?php echo $value['contents'] ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</footer>

</div>
<div class="container">
<a href="<?=WEB?>about">Über...</a>

</div>
<div id="cookiebox">
<p>
Diese Website nutzt Cookies. Wofür genau, steht hier: <a href="https://www.trancefish.de/blog/show/coding/Zum+Thema+Cookies/">Zum Thema Cookies</a> und natürlich in 
der <a href="https://www.trancefish.de/page/cms/Datenschutzerklaerung">Datenschutzerklärung</a>. 
<a class="button" href="#" onclick="accept_cookies()">Hab ich verstanden</a>

</p>

</div>
<script type="text/javascript" src="<?=WEB?>assetsdir/tlog.js"></script>
</body>
</html>
<?php endif;?>