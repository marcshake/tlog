<?php if ($value['comments_allowed'] == 1) : ?>

    <script type="text/javascript">
        var formular = new Object();
        formular = {
            'Name': 'text',
            'email': 'text',
            'Website': 'text',
            'Kommentar': 'comment',
            'fight': 'cbh',
            'Kommentieren': 'submit'
        };
        var f;
        function creatfo(bid) {
            f = document.createElement('form');
            f.setAttribute('method', 'post');
            f.setAttribute('action', '<?= $this->view['PAGEROOT'] ?>comment/save/' + bid);
            for (data in formular) {
                def = (formular[data]);
                g = document.createElement('legend');
                g.innerHTML = data;
                switch (def) {
                    case 'text':
                        l = document.createElement('label');
                        i = document.createElement('input');
                        i.setAttribute('type', 'text');
                        i.setAttribute('class', 'textfeld u-full-width');
                        i.setAttribute('name', data);
                        break;
                    case 'comment':
                        l = document.createElement('label');
                        i = document.createElement('textarea');
                        i.setAttribute('type', 'text');
                        i.setAttribute('cols', 80);
                        i.setAttribute('rows', 25);
                        i.setAttribute('class', 'textfeld multirow u-full-width');
                        i.setAttribute('name', data);
                        break;
                    case 'cbh':
                        i = document.createElement('input');
                        i.setAttribute('type', 'checkbox');
                        i.setAttribute('name', data);
                        i.setAttribute('value', 1);
                        i.setAttribute('style', 'display:none');
                        break;
                    case 'submit':
                        l = document.createElement('label');
                        i = document.createElement('input');
                        i.setAttribute('type', 'submit');
                        i.setAttribute('name', data);
                        i.setAttribute('class', 'button');
                        i.setAttribute('value', 'Abschicken');
                        break;

                }
                if (def != 'cbh') {
                    l.appendChild(g);
                }
                l.appendChild(i);
                f.appendChild(l);

            }
            document.getElementById('comments_form').appendChild(f);
        }

    </script>
<?php endif; ?>
    <div class="comments_tff" id="comments_form">
        <noscript>
            <?= i18n('Die Kommentarfunktion setzt Javascript voraus. Grund: viele Bots, gerade die blöden, billigen, führen kein Javascript aus. Das bremst Spam ein wenig.') ?>
        </noscript>

    </div>
<?php if ($this->view['comments']): ?>
    <h3>Kommentare</h3>
    <?php foreach ($this->view['comments'] as $id => $row) : ?>
        <hr/>
        <div class="row cHeadline">
            <div class="one-third column"><?= $row['user'] ?></div>
            <div class="one-third column">
                <?php if ($row['url']): ?>
                    <a href="<?= $row['url'] ?>" target="_blank"><?= $row['url'] ?></a>
                <?php else: ?>
                    <?= i18n('keine Homepage') ?>
                <?php endif; ?>

            </div>
            <div class="one-third column">
                <?= $row['datum'] ?>
            </div>
        </div>
        <div class="row">
            <div class="twelve columns">
                <?= nl2br($row['comment']) ?>
                <?php if ($this->view['USER']->get_level() == 1): ?>
                    <p>
                        <a href="<?= $this->view['PAGEROOT'] ?>comment/delete/<?= $row['cid'] ?>"><?= i18n('Löschen') ?></a>
                    </p>
                <?php endif; ?>

            </div>
        </div>

    <?php endforeach; ?>
    <hr>
<?php else: ?>
    <?= i18n('Leider hat hier noch keiner seinen Senf zum Thema abgegeben. Sei du doch der erste. Oder die erste. Oder das letzte.') ?>
<?php endif; ?>
<?php if ($value['comments_allowed'] == 1) : ?>
    <script type="text/javascript">
        creatfo(<?= $value['p_id'] ?>);
    </script>
<?php else: ?>
    <p><?= i18n('Kommentare sind hier verboten. Hintergrund: Manche Leute sind zu dämlich, sich im Netz zu benehmen.') ?></p>
<?php endif; ?>