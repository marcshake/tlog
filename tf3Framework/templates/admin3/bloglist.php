<h2>Beiträge</h2>

<div class="row">
    <div class="six columns">
        <label for="selecta"><input type="checkbox" id="selecta"> Alle auswählen</label>
    </div>
    <div class="six columns">
    <form action="<?=path('admin/blog/list')?>" method="get">
    <input type="text" class="u-full-width" placeholder="Filtern..." name="q" value="<?=$this->view['q']?>">
    </form>
    </div>
</div>
<form action="<?= WEB ?>admin/blog/massdelete" method="post" class="u-full-width" onsubmit="return doconfirm('Das löscht alle hier angezeigten Beiträge! Bist du dir sicher?')">
    <input type="submit" value="Markierte löschen" />    
    <table class="u-full-width">
        <thead>
            <tr>
                <th>Nummer</th>
                <th>Titel</th>
                <th>Autor</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <?php if ($this->view['bloglist'] == ''): ?>
            <tr><td colspan="4">
                    Leer. Nichts hinterlegt.
                </td></tr>
        <?php endif; ?>

        <?php foreach ($this->view['bloglist'] as $r => $row) : ?>

            <tr class="<?= $r % 2 == 0 ? 'odd' : 'even' ?>">
                <td><?= $r + 1 ?>
                    <input type="checkbox" class="deletion" name="delete[]" value="<?= $row['p_id'] ?>">
                </td>
                <td>
                    <a href="<?php echo $this->view['PAGEROOT']; ?>admin/blog/edit/<?php echo $row['p_id'] ?>">
                        <?php if ($row['handle'] == 'qload'): ?>
                            <i title="wird automatisch geladen" class="fa fa-arrow-right"></i>
                        <?php endif; ?>

                        <?php echo $row['title'] ?>
                    </a>
                </td>
                <td>
                    <?= $row['author'] ?>
                </td>
                <td>

                    <a href="<?php echo $this->view['PAGEROOT']; ?>admin/blog/change_status/<?php echo $row['p_id'] ?>" title="<?= $row['vis'] == 1 ? 'Sichtbar' : 'Unsichtbar'; ?>">
                        <i class="fa-eye<?= $row['vis'] == 1 ? '' : '-slash'; ?> fa"></i></a>

                    <a class="confirmation" href="<?php echo $this->view['PAGEROOT']; ?>admin/blog/deleteblog/<?php echo $row['p_id'] ?>">
                        <i class="fa fa-trash"> </i>
                    </a>
                    <a href="<?= WEB ?>admin/zen/<?= $row['p_id'] ?>" onclick="return confirm('Im ZEN-Modus gehen alle Formatierungen verloren. Fortsetzen?');">
                        <i class="fa fa-code"> </i>
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>
    </table>

</form>

