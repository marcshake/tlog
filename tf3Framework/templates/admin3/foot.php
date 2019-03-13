</div>

</div>
</div>
<?php include 'modal.php'?>

<?php if (_request('saved', 0) == 1): ?>
<div class="showsaved">
    Wurde gespeichert
</div>
<script type="text/javascript">
    $('document').ready(function () {
        $('.showsaved').fadeOut(5000);
    });
</script>
<?php endif; ?>
</body>
</html>