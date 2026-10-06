<?php
/** @var array|null $inst */
$bij = isset($inst) ? $inst['naam'] : 'de installateur';
$vragen = [
    ['Wat kost een Energiecafé?', 'Niets. De inkom is gratis en je krijgt na de uitleg een drankje aangeboden.'],
    ['Moet ik iets kopen of een offerte aanvragen?', 'Nee. Het Energiecafé is een infoavond, geen verkoopmoment. Wil je achteraf persoonlijk advies, dan kan dat bij ' . $bij . ', maar het hoeft niet.'],
    ['Is het iets voor mij als ik nog geen zonnepanelen heb?', 'Zeker. Of je nu nog moet beginnen, al jaren zonnepanelen hebt of twijfelt over een batterij: je gaat naar huis met een duidelijk beeld van wat voor jouw situatie zinvol is.'],
    ['Met hoeveel mogen we komen?', 'Eén inschrijving geldt voor 1 of 2 personen uit hetzelfde gezin. Kom je met meer, schrijf je dan een tweede keer in. Zo houden we de plaatsen eerlijk verdeeld.'],
    ['Kan ik vooraf een vraag stellen?', 'Graag zelfs. Vul ze in bij je inschrijving. Tijs neemt ze mee in de uitleg of beantwoordt ze persoonlijk na afloop.'],
    ['Ik kan toch niet komen. Wat nu?', 'In je bevestigingsmail staat een link om je af te melden. Zo komt je plaats vrij voor iemand anders.'],
    ['Hoe lang duurt de avond?', 'De uitleg duurt ongeveer 45 minuten. Reken in totaal op een anderhalf à twee uur, inclusief vragen en napraten.'],
];
?>
<section class="sectie creme" id="faq">
  <div class="wrap smal">
    <div class="sectie-kop">
      <p class="eyebrow">Veelgestelde vragen</p>
      <h2>Goed om te weten</h2>
    </div>
    <div class="faq">
      <?php foreach ($vragen as [$v, $a]): ?>
        <details>
          <summary><?= e($v) ?></summary>
          <p><?= e($a) ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
