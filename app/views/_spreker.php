<?php
$sp = site('spreker');
$refs = site('referenties') ?? [];
?>
<section class="sectie creme" id="spreker">
  <div class="wrap spreker">
    <div class="spreker-foto">
      <img src="/assets/img/<?= e($sp['foto']) ?>" alt="<?= e($sp['naam']) ?>, spreker van het Energiecafé" width="720" height="888" loading="lazy">
    </div>
    <div>
      <p class="eyebrow">Je spreker</p>
      <h2><?= e($sp['naam']) ?></h2>
      <p class="lead">Tijs werkt al jaren dagelijks met zonnepanelen, thuisbatterijen en energiebeheer: op daken, in technische ruimtes en vooral aan keukentafels.</p>
      <p>Daar merkte hij hoe vaak mensen door de bomen het bos niet meer zien. Elke maand een nieuw tarief, een nieuwe regel, een nieuw toestel dat "absoluut moet". Daarom begon hij in 2023 met het Energiecafé: één avond waarop hij alles rustig uitlegt, in gewone mensentaal.</p>
      <p>Intussen stonden er meer dan 700 mensen in de zaal. Wat ze achteraf het vaakst zeggen? <em>"Eindelijk snap ik het."</em></p>
      <blockquote class="citaat">"Wie begrijpt wat een batterij doet, neemt betere beslissingen. En een goed energiemanagementsysteem neemt je de zorgen uit handen. Daar draait het om."</blockquote>
      <?php if ($refs): ?>
        <p class="referenties"><strong>In het verleden al te horen bij:</strong> <?= e(implode(' · ', $refs)) ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>
