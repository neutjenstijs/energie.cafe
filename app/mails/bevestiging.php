<?php
/** @var array $ins  @var array $sessie  @var array $inst */
$loc = $sessie['loc'];
$kleur = $inst['kleur'] ?? '#DBAA49';
?>
<p style="margin:0 0 16px">Dag <?= e($ins['voornaam']) ?>,</p>
<p style="margin:0 0 20px">Fijn dat je erbij bent. Je inschrijving voor het Energiecafé bij <strong><?= e($inst['naam']) ?></strong> is bevestigd.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf8f3;border-left:4px solid <?= e($kleur) ?>;border-radius:6px;margin:0 0 22px">
  <tr><td style="padding:16px 18px;font-size:15px;line-height:1.7">
    <strong style="font-size:17px"><?= e(ucfirst(datum_lang($sessie['start']))) ?></strong><br>
    Aanvang <?= e(uur($sessie['start'])) ?> · graag een tiental minuten vooraf aanwezig<br>
    <?= e($loc['naam'] ?? '') ?><br>
    <?= e(adres_regel($loc)) ?><br>
    Ingeschreven: <?= (int) $ins['personen'] ?> <?= $ins['personen'] == 1 ? 'persoon' : 'personen' ?>
  </td></tr>
</table>

<p style="margin:0 0 22px">
  <a href="<?= e(maps_link($loc)) ?>" style="display:inline-block;background:#0d0d0d;color:#ffffff;text-decoration:none;padding:11px 18px;border-radius:6px;font-weight:600;font-size:14px;margin:0 6px 6px 0">Route plannen</a>
  <a href="<?= e(url('agenda.php?s=' . rawurlencode($sessie['id']))) ?>" style="display:inline-block;background:#ffffff;color:#0d0d0d;text-decoration:none;padding:10px 17px;border-radius:6px;font-weight:600;font-size:14px;border:1px solid #0d0d0d;margin:0 0 6px">Zet in je agenda</a>
</p>

<p style="margin:0 0 8px"><strong>Wat mag je verwachten?</strong></p>
<p style="margin:0 0 16px">Tijs Neutjens neemt je in een klein uur mee door thuisbatterijen, dynamische tarieven, het capaciteitstarief en slim energiebeheer. Helder, eerlijk en zonder vakjargon. Daarna is er een drankje en tijd voor al je vragen, ook met de mensen van <?= e($inst['naam']) ?>.</p>

<?php if (!empty($ins['vraag'])): ?>
<p style="margin:0 0 16px">Je stelde vooraf deze vraag: <em>"<?= e($ins['vraag']) ?>"</em>. We nemen ze mee naar de avond.</p>
<?php endif; ?>

<?php if (!empty($inst['bevestiging_extra'])): ?>
<p style="margin:0 0 16px"><?= nl2br(e($inst['bevestiging_extra'])) ?></p>
<?php endif; ?>

<p style="margin:0 0 16px">Vragen over de avond of de locatie? Antwoord gewoon op deze mail of contacteer <?= e($inst['naam']) ?> via <a href="tel:<?= e(preg_replace('/\s+/', '', $inst['telefoon'])) ?>" style="color:#0d0d0d"><?= e($inst['telefoon']) ?></a>.</p>

<p style="margin:0 0 20px">Tot dan!<br><?= $inst['slug'] === 'techneutjens' ? 'Tijs en het team van TechNeutjens' : 'Het team van ' . e($inst['naam']) ?></p>

<p style="margin:0 0 6px;font-size:13px;color:#777">Kan je toch niet? <a href="<?= e(url('afmelden.php?t=' . rawurlencode($ins['token']))) ?>" style="color:#777">Meld je hier af</a>, dan geven we je plaats aan iemand anders.</p>
