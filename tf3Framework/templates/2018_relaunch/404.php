<?php
$this->view['PAGETITLE'] = '4.0.4.';
?>
<?php include 'header.php'?>
<div class="container">
<h2>
    <?= i18n('Fehler'); ?>
</h2>
<div class="row">
    <div class="six columns">
        <p>Diese Seite wird immer dann angezeigt, wenn es einen Fehler gab. Dieser
            Fehler wird hier rechts angezeigt.
        </p>
    </div>
    <div class="six columns">
        <div class="errormessage">
            <?php echo $this->view['errormessage'] ?>
        </div>

    </div>
</div>

<?php $this->view['show_comments'] = true ?>
</div>
<?php include 'footer.php'?>
