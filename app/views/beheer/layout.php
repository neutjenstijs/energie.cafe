<?php /** @var string $titel  @var string $inhoud  @var string|null $email */ ?><!doctype html>
<html lang="nl-BE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($titel) ?> · Energiecafé</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="beheer">
<header class="kop">
  <div class="wrap">
    <a class="kop-logo" href="/beheer/"><img src="/assets/img/energiecafe-logo.svg" alt="Energiecafé" width="1000" height="250"></a>
    <div style="display:flex;align-items:center;gap:14px;font-size:.9rem;color:#bdb8ae">
      <?php if (!empty($email)): ?>
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:44vw"><?= e($email) ?></span>
        <a class="knop knop-rand knop-klein" style="color:#fff" href="/beheer/uitloggen.php">Uitloggen</a>
      <?php else: ?>
        <a href="/" style="color:#d8d4cc">Naar de website</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="beheer-main">
  <div class="wrap">
    <?= $inhoud ?>
  </div>
</main>
</body>
</html>
