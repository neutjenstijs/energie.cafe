<?php
/**
 * @var string $inhoud
 * @var string $titel
 * @var string|null $beschrijving
 * @var bool|null $noindex
 * @var string|null $nav      'home' of 'installateur'
 * @var string|null $canonical
 */
$volledigeTitel = isset($titel) && $titel !== '' ? $titel . ' · Energiecafé' : 'Energiecafé · Gratis infoavond over slim omgaan met energie';
$beschrijving ??= 'Gratis infoavond over thuisbatterijen, dynamische tarieven, het capaciteitstarief en slim energiebeheer. Helder uitgelegd door Tijs Neutjens, bij een installateur in je buurt.';
$nav ??= 'home';
$org = site('organisator');
?><!doctype html>
<html lang="nl-BE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($volledigeTitel) ?></title>
<meta name="description" content="<?= e($beschrijving) ?>">
<?php if (!empty($noindex)): ?><meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<?php if (!empty($canonical)): ?><link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<meta name="theme-color" content="#0d0d0d">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($volledigeTitel) ?>">
<meta property="og:description" content="<?= e($beschrijving) ?>">
<meta property="og:image" content="<?= e(url('assets/img/og-energiecafe.jpg')) ?>">
<meta property="og:locale" content="nl_BE">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="preload" href="/assets/fonts/inter-latin-800-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<?= $kop_extra ?? '' ?>
</head>
<body>
<a class="sr" href="#inhoud">Naar de inhoud</a>
<header class="kop" id="kop">
  <div class="wrap">
    <a class="kop-logo" href="/" aria-label="Energiecafé, naar de startpagina"><img src="/assets/img/energiecafe-logo.svg" alt="Energiecafé" width="1000" height="250"></a>
    <button class="menu-knop" type="button" aria-expanded="false" aria-controls="hoofdmenu" aria-label="Menu"><?= icoon('menu') ?></button>
    <nav id="hoofdmenu" aria-label="Hoofdmenu">
      <?php if ($nav === 'installateur'): ?>
        <a href="#over">Over <?= e($menu_naam ?? 'de installateur') ?></a>
        <a href="#avond">De avond</a>
        <a href="#faq">Vragen</a>
        <a href="/">Alle energiecafés</a>
        <a class="knop knop-goud" href="#inschrijven">Schrijf je in</a>
      <?php else: ?>
        <a href="/#waarom">Waarom</a>
        <a href="/#avond">De avond</a>
        <a href="/#spreker">Spreker</a>
        <a href="/#faq">Vragen</a>
        <a class="knop knop-goud" href="/#kies">Kies je energiecafé</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main id="inhoud">
<?= $inhoud ?>
</main>

<footer class="voet">
  <div class="wrap">
    <div class="voet-grid">
      <div>
        <img class="voet-logo" src="/assets/img/energiecafe-logo.svg" alt="Energiecafé" width="1000" height="250" loading="lazy">
        <p style="max-width:30em">Gratis infoavonden over slim omgaan met energie. Helder uitgelegd, bij een installateur in je buurt.</p>
      </div>
      <div>
        <h4>Energiecafés</h4>
        <ul>
          <?php foreach (actieve_installateurs() as $i): ?>
            <li><a href="/<?= e($i['slug']) ?>"><?= e($i['naam']) ?>, <?= e($i['gemeente']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4>Info</h4>
        <ul>
          <li><a href="/#faq">Veelgestelde vragen</a></li>
          <li><a href="/privacy/">Privacyverklaring</a></li>
          <li><a href="mailto:<?= e($org['email']) ?>"><?= e($org['email']) ?></a></li>
          <li><a href="/beheer/">Login installateurs</a></li>
        </ul>
      </div>
    </div>
    <div class="voet-onder">
      <span>© <?= date('Y') ?> Energiecafé. Een concept van <?= e($org['naam']) ?>, <?= e($org['adres']) ?>, <?= e($org['kbo']) ?>.</span>
      <span>Deze site gebruikt geen tracking-cookies.</span>
    </div>
  </div>
</footer>
<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
