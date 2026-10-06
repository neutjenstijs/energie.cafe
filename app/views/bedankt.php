<?php
/** @var array|null $ins  @var array|null $sessie  @var array|null $inst */
?>
<section class="sectie donker bedankt">
  <div class="wrap smal">
    <div class="vink"><?= icoon('check') ?></div>
    <?php if ($ins && $sessie && $inst): ?>
      <p class="eyebrow" style="justify-content:center">Inschrijving bevestigd</p>
      <h1 style="font-size:clamp(2rem,4.5vw,3rem)">Tot binnenkort, <?= e($ins['voornaam']) ?>!</h1>
      <p class="lead" style="margin:0 auto">We stuurden een bevestiging naar <strong style="color:#fff"><?= e($ins['email']) ?></strong>. Niets ontvangen? Kijk even in je spam.</p>
      <div class="samenvatting">
        <dl>
          <dt>Waar</dt><dd><?= e($sessie['loc']['naam'] ?? $inst['naam']) ?><br><span style="font-weight:400"><?= e(adres_regel($sessie['loc'])) ?></span></dd>
          <dt>Wanneer</dt><dd><?= e(ucfirst(datum_lang($sessie['start']))) ?>, <?= e(uur($sessie['start'])) ?></dd>
          <dt>Personen</dt><dd><?= (int) $ins['personen'] ?></dd>
        </dl>
      </div>
      <div class="knoppenrij">
        <a class="knop knop-goud" href="/agenda.php?s=<?= e(rawurlencode($sessie['id'])) ?>"><?= icoon('kalender') ?> Zet in je agenda</a>
        <a class="knop knop-rand" href="<?= e(maps_link($sessie['loc'])) ?>" target="_blank" rel="noopener"><?= icoon('pin') ?> Route plannen</a>
      </div>
      <p style="margin-top:36px;color:#a8a39a">Kom je met meer dan twee? <a href="/<?= e($inst['slug']) ?>#inschrijven" style="color:var(--goud)">Schrijf je nog een keer in</a>.</p>
    <?php else: ?>
      <h1 style="font-size:clamp(2rem,4.5vw,3rem)">Bedankt!</h1>
      <p class="lead" style="margin:0 auto 28px">Je inschrijving is goed ontvangen. Je krijgt een bevestiging per mail.</p>
      <a class="knop knop-goud" href="/">Naar de startpagina</a>
    <?php endif; ?>
  </div>
  <div class="wrap">
    <div class="lijntekening" aria-hidden="true" style="opacity:.6"><img src="/assets/img/lijntekening-goud.png" alt="" width="1606" height="600"></div>
  </div>
</section>
