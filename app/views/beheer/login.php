<?php /** @var bool $verstuurd  @var string $fout */ ?>
<div class="login-kaart">
  <p class="eyebrow">Voor installateurs</p>
  <h1 style="font-size:1.7rem">Inloggen</h1>
  <?php if ($verstuurd): ?>
    <div class="melding ok">Check je mailbox. Als dit adres toegang heeft, ontvang je binnen een minuut een inloglink.</div>
    <p style="color:var(--tekst-zacht);font-size:.95rem">De link is 30 minuten geldig. Niets ontvangen? Kijk in je spam of <a href="/beheer/">probeer opnieuw</a>.</p>
  <?php else: ?>
    <p style="color:var(--tekst-zacht)">Vul het e-mailadres in dat bij je installateursaccount hoort. Je krijgt een link om in te loggen, een wachtwoord heb je niet nodig.</p>
    <?php if ($fout): ?><div class="melding fout"><?= e($fout) ?></div><?php endif; ?>
    <form method="post" action="/beheer/">
      <?= csrf_veld() ?>
      <div class="veld" style="margin-bottom:16px">
        <label for="email">E-mailadres</label>
        <input id="email" name="email" type="email" autocomplete="email" required autofocus>
      </div>
      <button class="knop knop-zwart" type="submit" style="width:100%">Stuur me een inloglink</button>
    </form>
  <?php endif; ?>
</div>
