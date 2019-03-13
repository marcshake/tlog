<?php $this->view['PAGETITLE'] = $this->view['page']['title']; ?>
<?php $this->view['DESCRIPTION'] = $this->view['page']['description'] ?>
<?php $this->view['KEYWORDS'] = $this->view['page']['keywords'] ?>
<?php include 'header.php'?>
<div class="entry_content container">

    <h1><?php echo $this->view['page']['title'] ?></h1>
    <?php echo $this->view['page']['contents'] ?>
</div>
<?php include 'footer.php'?>
