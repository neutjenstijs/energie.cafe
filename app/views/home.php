<?php
/** @var array $installateurs  @var array $bez */
?>
<section class="hero">
  <div class="wrap">
    <div class="hero-tekst">
      <p class="eyebrow">Gratis infoavond · bij een installateur in je buurt</p>
      <h1>Eén avond. Eindelijk <em>helder</em> wat jij met je energie kan doen.</h1>
      <p class="lead">Capaciteitstarief, thuisbatterij, dynamisch contract, slim laden… Tijs Neutjens legt het uit in gewone mensentaal. Eerlijk, merkneutraal en met een drankje achteraf.</p>
      <div class="hero-knoppen">
        <a class="knop knop-goud" href="#kies">Kies je energiecafé <?= icoon('pijl') ?></a>
        <a class="knop knop-rand" href="#avond">Hoe verloopt de avond?</a>
      </div>
    </div>
    <div class="lijntekening teken" aria-hidden="true">
      <img src="/assets/img/lijntekening-goud.png" alt="" width="1606" height="600">
    </div>
    <div class="cijfers">
      <?php foreach (site('cijfers') as $c): ?>
        <div class="cijfer"><b><?= e($c['waarde']) ?></b><span><?= e($c['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sectie creme" id="waarom">
  <div class="wrap">
    <div class="sectie-kop">
      <p class="eyebrow">Herken je dit?</p>
      <h2>Iedereen heeft het over de energietransitie. Maar wat betekent ze voor jou?</h2>
      <p class="lead">Dit zijn de vragen die we op elk Energiecafé horen. Op één avond krijg je er een helder antwoord op.</p>
    </div>
    <ul class="vragen">
      <li>Wat betekent het capaciteitstarief voor mijn factuur?</li>
      <li>Zijn zonnepanelen nog rendabel met de digitale meter?</li>
      <li>Is een thuisbatterij iets voor mij, of beter nog niet?</li>
      <li>Wat is een dynamisch energiecontract, en wanneer loont het?</li>
      <li>Wat doet een slimme laadpaal anders dan een gewone?</li>
      <li>Hoe laat ik al die toestellen slim samenwerken?</li>
    </ul>
  </div>
</section>

<section class="sectie">
  <div class="wrap">
    <div class="sectie-kop">
      <p class="eyebrow">Waarom een Energiecafé?</p>
      <h2>Betere beslissingen beginnen bij begrijpen.</h2>
    </div>
    <div class="voordelen">
      <div class="voordeel">
        <div class="icoon"><?= icoon('balans') ?></div>
        <h3>Eerlijk en merkneutraal</h3>
        <p>Geen verkooppraatje, wel uitleg. Je hoort wat werkt, wat (nog) niet, en voor wie het loont.</p>
      </div>
      <div class="voordeel">
        <div class="icoon"><?= icoon('lamp') ?></div>
        <h3>Eindelijk begrijpbaar</h3>
        <p>Zonder vakjargon. Na de avond snap je je verbruik, je factuur en je opties een pak beter.</p>
      </div>
      <div class="voordeel">
        <div class="icoon"><?= icoon('vraag') ?></div>
        <h3>Jouw vragen beantwoord</h3>
        <p>Stel ze vooraf bij je inschrijving of gewoon ter plaatse. Niemand gaat naar huis met een vraag.</p>
      </div>
      <div class="voordeel">
        <div class="icoon"><?= icoon('huis') ?></div>
        <h3>Een vakman om de hoek</h3>
        <p>Je leert een installateur uit je regio kennen, zonder verplichtingen. Handig als je later wél iets wil laten doen.</p>
      </div>
    </div>
  </div>
</section>

<?= view('_avond') ?>

<?= view('_spreker') ?>

<section class="sectie" id="kies">
  <div class="wrap">
    <div class="sectie-kop">
      <p class="eyebrow">Kies je energiecafé</p>
      <h2>Bij een installateur in jouw buurt</h2>
      <p class="lead">Elk Energiecafé brengt hetzelfde heldere verhaal. Kies de locatie die voor jou het dichtstbij ligt en schrijf je in voor een datum.</p>
    </div>
    <div class="kaarten">
      <?php foreach ($installateurs as $i):
        $komend = komende_sessies($i); ?>
        <a class="kaart" href="/<?= e($i['slug']) ?>">
          <div class="kaart-logo" style="background:<?= e($i['logo_achtergrond'] ?? '#fff') ?>">
            <img src="/assets/img/<?= e($i['logo']) ?>" alt="<?= e($i['naam']) ?>" loading="lazy">
          </div>
          <div class="kaart-body">
            <h3><?= e($i['naam']) ?></h3>
            <div class="kaart-plaats"><?= icoon('pin') ?> Energiecafé in <?= e($i['gemeente']) ?></div>
            <?php if ($komend): ?>
              <ul class="kaart-datums" aria-label="Komende datums">
                <?php foreach (array_slice($komend, 0, 4) as $s):
                  $vol = vrije_plaatsen($s, $bez) <= 0; ?>
                  <li class="<?= $vol ? 'volzet' : '' ?>"><?= e(datum_kort($s['start'])) ?><?= $vol ? ' · volzet' : '' ?></li>
                <?php endforeach; ?>
              </ul>
              <span class="kaart-actie">Bekijk datums en schrijf in <?= icoon('pijl') ?></span>
            <?php else: ?>
              <p class="kaart-leeg">Nieuwe datums volgen binnenkort.</p>
              <span class="kaart-actie">Hou me op de hoogte <?= icoon('pijl') ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
      <div class="kaart kaart-binnenkort">
        <div>
          <strong>Binnenkort ook in jouw regio?</strong>
          Er komen regelmatig nieuwe installateurs en locaties bij.
        </div>
      </div>
    </div>
  </div>
</section>

<section class="sectie-s">
  <div class="wrap">
    <div class="sfeer">
      <img src="/assets/img/sfeer-1.jpg" alt="Deelnemers op een Energiecafé" loading="lazy" width="1400" height="932">
      <img src="/assets/img/sfeer-2.jpg" alt="Sfeerbeeld van een Energiecafé" loading="lazy">
      <img src="/assets/img/sfeer-3.jpg" alt="Napraten na de uitleg" loading="lazy">
    </div>
  </div>
</section>

<?= view('_faq') ?>
