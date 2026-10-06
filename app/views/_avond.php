<?php
/** @var array|null $inst  Installateur (op een installateurspagina) */
$bij = isset($inst) ? $inst['naam'] : 'de installateur';
?>
<section class="sectie donker" id="avond">
  <div class="wrap">
    <div class="sectie-kop">
      <p class="eyebrow">Zo verloopt de avond</p>
      <h2>Een klein uur uitleg. Daarna alle tijd voor jouw vragen.</h2>
      <p class="lead">Geen verkooppraatje, geen ingewikkelde grafieken. Wel een helder verhaal waarmee je thuis meteen verder kan.</p>
    </div>
    <ol class="verloop">
      <li>
        <span class="tijd">Bij aankomst</span>
        <h3>Welkom</h3>
        <p>Kom een tiental minuten vooraf. Zo starten we samen op tijd.</p>
      </li>
      <li>
        <span class="tijd">± 45 minuten</span>
        <h3>De uitleg</h3>
        <p>Zonnepanelen, thuisbatterij, dynamische tarieven, capaciteitstarief en energiebeheer: hoe het in elkaar zit en wat het voor jou betekent.</p>
      </li>
      <li>
        <span class="tijd">Zolang als nodig</span>
        <h3>Jouw vragen</h3>
        <p>Vragen die je bij je inschrijving doorgaf, komen zeker aan bod. Tijs blijft tot de laatste vraag beantwoord is.</p>
      </li>
      <li>
        <span class="tijd">Tot slot</span>
        <h3>Napraten met een drankje</h3>
        <p>Praat na met Tijs en met de vakmensen van <?= e($bij) ?>. Vrijblijvend, in je eigen tempo.</p>
      </li>
    </ol>
    <div class="praktisch">
      <span class="pil"><?= icoon('euro') ?> Inkom gratis</span>
      <span class="pil"><?= icoon('mensen') ?> Eén inschrijving = 1 of 2 personen</span>
      <span class="pil"><?= icoon('glas') ?> Drankje inbegrepen</span>
      <span class="pil"><?= icoon('balans') ?> Merkneutraal</span>
    </div>
  </div>
</section>
