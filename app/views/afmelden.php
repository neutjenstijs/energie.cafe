<?php
/** @var string $status  @var array|null $ins  @var array|null $sessie  @var array|null $inst  @var string $token */
?>
<section class="sectie creme">
  <div class="wrap smal" style="max-width:640px">
    <div class="formulier" style="text-align:center">
      <?php if ($status === 'vraag'): ?>
        <h1 style="font-size:2rem">Afmelden?</h1>
        <p class="lead" style="margin:0 auto 22px">Dag <?= e($ins['voornaam']) ?>, wil je je inschrijving voor het Energiecafé bij <strong><?= e($inst['naam']) ?></strong> op <strong><?= e(datum_lang($sessie['start'])) ?></strong> annuleren?</p>
        <form method="post" action="/afmelden.php">
          <?= csrf_veld() ?>
          <input type="hidden" name="t" value="<?= e($token) ?>">
          <div class="knoppenrij">
            <button class="knop knop-zwart" type="submit">Ja, meld me af</button>
            <a class="knop knop-rand" href="/<?= e($inst['slug']) ?>">Nee, ik kom toch</a>
          </div>
        </form>
      <?php elseif ($status === 'gedaan'): ?>
        <h1 style="font-size:2rem">Je bent afgemeld</h1>
        <p class="lead" style="margin:0 auto 22px">Jammer dat je er niet bij kan zijn. Je plaats gaat naar iemand anders. Misschien past een andere datum?</p>
        <a class="knop knop-zwart" href="/<?= e($inst['slug']) ?>">Bekijk andere datums</a>
      <?php elseif ($status === 'al'): ?>
        <h1 style="font-size:2rem">Al afgemeld</h1>
        <p class="lead" style="margin:0 auto 22px">Deze inschrijving was al geannuleerd.</p>
        <a class="knop knop-zwart" href="/<?= e($inst['slug']) ?>">Bekijk de datums</a>
      <?php elseif ($status === 'voorbij'): ?>
        <h1 style="font-size:2rem">Deze avond is al voorbij</h1>
        <p class="lead" style="margin:0 auto 22px">Afmelden kan niet meer.</p>
        <a class="knop knop-zwart" href="/">Naar de startpagina</a>
      <?php else: ?>
        <h1 style="font-size:2rem">Link niet gevonden</h1>
        <p class="lead" style="margin:0 auto 22px">Deze afmeldlink is ongeldig. Neem gerust contact op met de installateur waar je je inschreef.</p>
        <a class="knop knop-zwart" href="/">Naar de startpagina</a>
      <?php endif; ?>
    </div>
  </div>
</section>
