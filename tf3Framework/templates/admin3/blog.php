<?php include 'head.php'?>
<?php if ($this->view['bloglist']):?>
<?php include 'bloglist.php'?>
<?php endif; ?>

<?php if ($this->view['action'] == 'editor'): ?>
<?php include 'blogeditor.php'?>
<?php endif; ?>

<?php include 'foot.php'?>
