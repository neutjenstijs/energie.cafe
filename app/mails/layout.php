<?php
/** @var array $inst  @var string $titel  @var string $inhoud */
$kleur = $inst['kleur'] ?? '#DBAA49';
?><!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titel) ?></title>
</head>
<body style="margin:0;padding:0;background:#f3f1ec;font-family:-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1a1a1a">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f1ec">
  <tr><td align="center" style="padding:24px 12px">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden">
      <tr>
        <td style="background:#0d0d0d;padding:22px 28px">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
            <td style="color:#ffffff;font-size:13px;letter-spacing:3px;text-transform:uppercase;font-weight:600">Energie<span style="color:#DBAA49">café</span></td>
            <?php if (!empty($inst['logo'])): ?>
            <td align="right"><span style="display:inline-block;background:<?= e($inst['logo_achtergrond'] ?? '#fff') ?>;border-radius:6px;padding:6px 10px"><img src="<?= e(url('assets/img/' . $inst['logo'])) ?>" alt="<?= e($inst['naam']) ?>" height="32" style="display:block;height:32px;width:auto;max-width:160px"></span></td>
            <?php endif; ?>
          </tr></table>
        </td>
      </tr>
      <tr><td style="height:4px;background:<?= e($kleur) ?>;font-size:0;line-height:0">&nbsp;</td></tr>
      <tr>
        <td style="padding:28px 28px 8px">
          <h1 style="margin:0 0 18px;font-size:22px;line-height:1.25;color:#0d0d0d"><?= e($titel) ?></h1>
          <div style="font-size:15px;line-height:1.6">
            <?= $inhoud ?>
          </div>
        </td>
      </tr>
      <tr>
        <td style="padding:20px 28px 26px;color:#8a8a8a;font-size:12px;line-height:1.5;border-top:1px solid #eee">
          Energiecafé · infoavonden over slim elektrificeren · <a href="<?= e(url()) ?>" style="color:#8a8a8a">energie.cafe</a>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
