Hat dir dieser Beitrag gefallen? Dann lass doch ein Vote da! 
<a href="#" onclick="return vote(<?= $value['p_id'] ?>)">
    <img src="<?= WEB ?>assetsdir/vote.jpeg" width="32" alt="Vote" /> 
</a><span id="likes"><?= $value['votes'] ?></span>



<script type="text/javascript">
    function vote(id) {
        var ajax = new XMLHttpRequest();
        ajax.open('GET', '<?= WEB ?>blog/voter/?bid=' + id, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                var resp = ajax.responseText;
                var liek = document.getElementById('likes');

                liek.innerHTML = resp;
            }
        };
        ajax.send();
        return false;
    }

</script>