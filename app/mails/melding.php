<?php
/** @var array $ins  @var array $sessie  @var array $inst  @var int $bezet */
$rij = fn($k, $v) => '<tr><td style="padding:7px 12px 7px 0;color:#777;vertical-align:top;white-space:nowrap">' . e($k) . '</td><td style="padding:7px 0;vertical-align:top">' . $v . '</td></tr>';
$vol = $sessie['plaatsen'] > 0 ? min(100, (int) round($bezet / $sessie['plaatsen'] * 100)) : 0;
?>
<p style="margin:0 0 6px;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#8a6d2f;font-weight:600">Installateur: <?= e($inst['naam']) ?></p>
<p style="margin:0 0 18px">Er is een nieuwe inschrijving voor het Energiecafé van <strong><?= e(datum_lang($sessie['start'])) ?></strong> om <?= e(uur($sessie['start'])) ?>.</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="font-size:15px;margin:0 0 20px;border-collapse:collapse">
  <?= $rij('Naam', '<strong>' . e($ins['voornaam'] . ' ' . $ins['naam']) . '</strong>') ?>
  <?= $rij('Personen', (string) (int) $ins['personen']) ?>
  <?= $rij('E-mail', '<a href="mailto:' . e($ins['email']) . '" style="color:#0d0d0d">' . e($ins['email']) . '</a>') ?>
  <?= $rij('Telefoon', '<a href="tel:' . e(preg_replace('/\s+/', '', $ins['telefoon'])) . '" style="color:#0d0d0d">' . e($ins['telefoon']) . '</a>') ?>
  <?= $rij('Postcode', e($ins['postcode'])) ?>
  <?= $rij('Contact na de avond', $ins['contact_ok'] ? '<strong style="color:#1a7f37">Ja, wil gecontacteerd worden</strong>' : 'Nee, niet gevraagd') ?>
  <?php if (!empty($ins['vraag'])): ?><?= $rij('Vraag vooraf', nl2br(e($ins['vraag']))) ?><?php endif; ?>
  <?= $rij('Bron', $ins['bron'] === 'manueel' ? 'Manueel ingevoerd door ' . e($ins['ingevoerd_door']) : 'Website') ?>
</table>

<p style="margin:0 0 6px;font-weight:600">Bezetting: <?= (int) $bezet ?> / <?= (int) $sessie['plaatsen'] ?> personen</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px"><tr>
  <td style="background:#eee;border-radius:4px;height:10px;font-size:0;line-height:0">
    <div style="width:<?= $vol ?>%;background:<?= e($inst['kleur'] ?? '#DBAA49') ?>;height:10px;border-radius:4px">&nbsp;</div>
  </td>
</tr></table>

<p style="margin:0;color:#666;font-size:13px">Alle inschrijvingen bekijken of zelf iemand toevoegen: <a href="<?= e(url('beheer/')) ?>" style="color:#666"><?= e(url('beheer/')) ?></a></p>
