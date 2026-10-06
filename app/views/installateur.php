<?php
/**
 * @var array $inst
 * @var array $bez
 * @var array $fouten          Fouten van het inschrijfformulier
 * @var array $waarden         Ingevulde waarden (na een fout)
 * @var array $update_fouten
 * @var array $update_waarden
 * @var bool  $update_ok
 */
$komend = komende_sessies($inst);
$voorbeeld = $inst['status'] === 'voorbeeld';
$loc = reset($inst['locaties']) ?: [];
$w = fn(string $k, $d = '') => $waarden[$k] ?? $d;
$f = fn(string $k) => isset($fouten[$k]) ? '<span class="foutmelding" id="fout-' . $k . '">' . e($fouten[$k]) . '</span>' : '';
$fc = fn(string $k) => isset($fouten[$k]) ? ' fout' : '';
$aria = fn(string $k) => isset($fouten[$k]) ? ' aria-invalid="true" aria-describedby="fout-' . $k . '"' : '';
$gekozen = $w('sessie');
if ($gekozen === '') {
    foreach ($komend as $s) { if (vrije_plaatsen($s, $bez) > 0 && empty($s['gesloten'])) { $gekozen = $s['id']; break; } }
}
$personen = (int) $w('personen', 2);
$uw = fn(string $k) => $update_waarden[$k] ?? '';
?>
<?php if ($voorbeeld): ?>
  <div class="voorbeeld-balk">Voorbeeldpagina: zo kan het Energiecafé bij <?= e($inst['naam']) ?> eruitzien. Inschrijven is nog niet mogelijk.</div>
<?php endif; ?>

<section class="hero inst-hero">
  <div class="wrap">
    <div class="hero-tekst">
      <p class="kruimel"><a href="/">Energiecafé</a> &nbsp;›&nbsp; <?= e($inst['gemeente']) ?></p>
      <p class="eyebrow">Energiecafé bij <?= e($inst['naam']) ?></p>
      <h1>Gratis infoavond over slim omgaan met energie, in <em><?= e($inst['gemeente']) ?></em>.</h1>
      <p class="lead">Tijs Neutjens legt helder uit hoe je zelf opgewekte energie slim gebruikt, opslaat en beheert. Daarna praat je na met een drankje, samen met de vakmensen van <?= e($inst['naam']) ?>.</p>
      <div class="hero-knoppen">
        <a class="knop knop-goud" href="#inschrijven"><?= $komend ? 'Kies een datum' : 'Hou me op de hoogte' ?> <?= icoon('pijl') ?></a>
      </div>
      <ul class="hero-feiten">
        <li><?= icoon('pin') ?> <?= e(($loc['naam'] ?? '') . ', ' . ($loc['gemeente'] ?? '')) ?></li>
        <li><?= icoon('klok') ?> ± 45 min uitleg + vragen</li>
        <li><?= icoon('euro') ?> Inkom gratis</li>
      </ul>
    </div>
    <div class="inst-logo" style="background:<?= e($inst['logo_achtergrond'] ?? '#fff') ?>">
      <img src="/assets/img/<?= e($inst['logo']) ?>" alt="<?= e($inst['naam']) ?>">
    </div>
  </div>
  <div class="wrap">
    <div class="lijntekening teken" aria-hidden="true" style="margin-top:28px">
      <img src="/assets/img/lijntekening-goud.png" alt="" width="1606" height="600">
    </div>
  </div>
</section>

<section class="sectie creme" id="inschrijven">
  <div class="wrap inschrijf-grid">
    <div>
      <?php if ($komend): ?>
      <form class="formulier" method="post" action="/<?= e($inst['slug']) ?>#inschrijven" novalidate>
        <input type="hidden" name="actie" value="inschrijven">
        <input type="hidden" name="stempel" value="<?= e(formulier_stempel()) ?>">
        <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <?php if ($fouten): ?>
          <div class="fout-blok" role="alert">Er ontbreekt nog iets. Kijk de velden hieronder even na.</div>
        <?php endif; ?>

        <fieldset class="stap">
          <legend><span class="nr">1</span> Kies je datum</legend>
          <?= $f('sessie') ?>
          <div class="sessies" role="radiogroup">
            <?php foreach ($komend as $s):
              $vrij = vrije_plaatsen($s, $bez);
              $dicht = $vrij <= 0 || !empty($s['gesloten']);
              $sl = $s['loc']; ?>
              <label class="sessie<?= $dicht ? ' volzet' : '' ?>">
                <input type="radio" name="sessie" value="<?= e($s['id']) ?>" <?= $dicht || $voorbeeld ? 'disabled' : '' ?> <?= $s['id'] === $gekozen && !$dicht ? 'checked' : '' ?> required>
                <span class="sessie-kaart">
                  <span class="sessie-check"><?= icoon('check') ?></span>
                  <span class="sessie-dag"><?= e(DAGEN[(int) $s['start']->format('w')]) ?></span>
                  <span class="sessie-datum"><?= e($s['start']->format('j') . ' ' . MAANDEN[(int) $s['start']->format('n')]) ?></span>
                  <span class="sessie-meta"><?= e(uur($s['start'])) ?> · <?= e($sl['gemeente'] ?? '') ?></span>
                  <?php if (count($inst['locaties']) > 1): ?><span class="sessie-meta"><?= e($sl['naam'] ?? '') ?></span><?php endif; ?>
                  <?php if (!empty($s['gesloten'])): ?>
                    <span class="sessie-plaats">Inschrijvingen gesloten</span>
                  <?php elseif ($vrij <= 0): ?>
                    <span class="sessie-plaats">Volzet</span>
                  <?php elseif ($vrij <= 8): ?>
                    <span class="sessie-plaats bijna">Nog <?= $vrij ?> <?= $vrij === 1 ? 'plaats' : 'plaatsen' ?></span>
                  <?php else: ?>
                    <span class="sessie-plaats">Plaatsen vrij</span>
                  <?php endif; ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        </fieldset>

        <fieldset class="stap">
          <legend><span class="nr">2</span> Je gegevens</legend>
          <div class="velden">
            <div class="veld<?= $fc('voornaam') ?>">
              <label for="voornaam">Voornaam</label>
              <input id="voornaam" name="voornaam" autocomplete="given-name" required value="<?= e($w('voornaam')) ?>"<?= $aria('voornaam') ?>>
              <?= $f('voornaam') ?>
            </div>
            <div class="veld<?= $fc('naam') ?>">
              <label for="naam">Achternaam</label>
              <input id="naam" name="naam" autocomplete="family-name" required value="<?= e($w('naam')) ?>"<?= $aria('naam') ?>>
              <?= $f('naam') ?>
            </div>
            <div class="veld<?= $fc('email') ?>">
              <label for="email">E-mailadres</label>
              <input id="email" name="email" type="email" autocomplete="email" inputmode="email" required value="<?= e($w('email')) ?>"<?= $aria('email') ?>>
              <?= $f('email') ?>
            </div>
            <div class="veld<?= $fc('telefoon') ?>">
              <label for="telefoon">Telefoon</label>
              <input id="telefoon" name="telefoon" type="tel" autocomplete="tel" inputmode="tel" required value="<?= e($w('telefoon')) ?>"<?= $aria('telefoon') ?>>
              <?= $f('telefoon') ?>
            </div>
            <div class="veld<?= $fc('postcode') ?>">
              <label for="postcode">Postcode</label>
              <input id="postcode" name="postcode" autocomplete="postal-code" inputmode="numeric" maxlength="8" required value="<?= e($w('postcode')) ?>"<?= $aria('postcode') ?>>
              <?= $f('postcode') ?>
            </div>
            <div class="veld<?= $fc('personen') ?>">
              <span class="label" id="lbl-personen">Met hoeveel kom je?</span>
              <div class="keuze" role="radiogroup" aria-labelledby="lbl-personen">
                <label><input type="radio" name="personen" value="1" <?= $personen === 1 ? 'checked' : '' ?>><span>1 persoon</span></label>
                <label><input type="radio" name="personen" value="2" <?= $personen !== 1 ? 'checked' : '' ?>><span>2 personen</span></label>
              </div>
              <span class="hulp">Met meer dan twee? Schrijf je dan nog eens in.</span>
              <?= $f('personen') ?>
            </div>
            <div class="veld vol<?= $fc('vraag') ?>">
              <label for="vraag">Heb je al een vraag? <span class="optioneel">(optioneel)</span></label>
              <textarea id="vraag" name="vraag" maxlength="1500" placeholder="Bv. Ik heb zonnepanelen uit 2012, is een batterij voor mij interessant?"<?= $aria('vraag') ?>><?= e($w('vraag')) ?></textarea>
              <?= $f('vraag') ?>
            </div>
            <div class="veld vol">
              <label class="vinkje">
                <input type="checkbox" name="contact_ok" value="1" <?= $w('contact_ok') ? 'checked' : '' ?>>
                <span>Ja, <?= e($inst['naam']) ?> mag me na de avond contacteren voor persoonlijk advies over mijn situatie. <span class="optioneel">(optioneel)</span></span>
              </label>
            </div>
          </div>
          <p class="privacy-noot">Je gegevens worden gebruikt om deze avond te organiseren en worden gedeeld met <?= e($inst['naam']) ?> (gastheer) en TechNeutjens BV (organisator van het Energiecafé). Meer in de <a href="/privacy/">privacyverklaring</a>.</p>
        </fieldset>

        <div class="verzenden">
          <button class="knop knop-zwart" type="submit" <?= $voorbeeld ? 'disabled' : '' ?>>Schrijf me in <?= icoon('pijl') ?></button>
          <span class="hulp" style="color:var(--tekst-zacht);font-size:.92rem">Je krijgt meteen een bevestiging per mail.</span>
        </div>
      </form>
      <?php endif; ?>

      <div class="formulier" style="<?= $komend ? 'margin-top:20px;box-shadow:none' : '' ?>" id="hou-me-op-de-hoogte">
        <?php if (!$komend): ?>
          <div class="geen-datums">
            <?= icoon('kalender') ?>
            <h2 style="font-size:1.6rem">Nieuwe datums volgen binnenkort</h2>
            <p class="lead" style="margin:0 auto 8px">Laat je e-mailadres achter. Je krijgt een bericht zodra je kan inschrijven voor het Energiecafé bij <?= e($inst['naam']) ?>.</p>
          </div>
        <?php endif; ?>
        <?php if ($update_ok): ?>
          <div class="melding ok" role="status">Bedankt! We laten je weten zodra er nieuwe datums zijn.</div>
        <?php else: ?>
          <?php if ($komend): ?>
            <details class="update-blok" style="margin:0;padding:0;border:0" <?= $update_fouten ? 'open' : '' ?>>
              <summary>Geen datum die past? Hou me op de hoogte van nieuwe datums.</summary>
          <?php endif; ?>
          <form class="update-form" method="post" action="/<?= e($inst['slug']) ?>#hou-me-op-de-hoogte" novalidate>
            <input type="hidden" name="actie" value="update">
            <input type="hidden" name="stempel" value="<?= e(formulier_stempel()) ?>">
            <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="velden">
              <div class="veld<?= isset($update_fouten['voornaam']) ? ' fout' : '' ?>">
                <label for="u-voornaam">Voornaam</label>
                <input id="u-voornaam" name="voornaam" autocomplete="given-name" value="<?= e($uw('voornaam')) ?>">
                <?php if (isset($update_fouten['voornaam'])): ?><span class="foutmelding"><?= e($update_fouten['voornaam']) ?></span><?php endif; ?>
              </div>
              <div class="veld<?= isset($update_fouten['email']) ? ' fout' : '' ?>">
                <label for="u-email">E-mailadres</label>
                <input id="u-email" name="email" type="email" autocomplete="email" value="<?= e($uw('email')) ?>">
                <?php if (isset($update_fouten['email'])): ?><span class="foutmelding"><?= e($update_fouten['email']) ?></span><?php endif; ?>
              </div>
              <div class="veld<?= isset($update_fouten['postcode']) ? ' fout' : '' ?>">
                <label for="u-postcode">Postcode <span class="optioneel">(opt.)</span></label>
                <input id="u-postcode" name="postcode" inputmode="numeric" maxlength="8" autocomplete="postal-code" value="<?= e($uw('postcode')) ?>">
              </div>
            </div>
            <div class="verzenden" style="margin-top:16px">
              <button class="knop knop-<?= $komend ? 'rand' : 'zwart' ?> knop-klein" type="submit" <?= $voorbeeld ? 'disabled' : '' ?>><?= icoon('bel') ?> Hou me op de hoogte</button>
            </div>
            <p class="privacy-noot">We gebruiken je e-mailadres enkel om je te verwittigen over nieuwe Energiecafés bij <?= e($inst['naam']) ?>. <a href="/privacy/">Privacy</a>.</p>
          </form>
          <?php if ($komend): ?></details><?php endif; ?>
        <?php endif; ?>
      </div>
    </div>

    <aside class="zijkolom" id="over">
      <div class="blok">
        <p class="blok-titel">Locatie</p>
        <?php foreach ($inst['locaties'] as $l): ?>
          <address class="locatie-adres">
            <strong><?= e($l['naam']) ?></strong>
            <?= e($l['adres']) ?><br><?= e($l['postcode'] . ' ' . $l['gemeente']) ?>
          </address>
          <a class="link-pijl" href="<?= e(maps_link($l)) ?>" target="_blank" rel="noopener">Route plannen <?= icoon('pijl') ?></a>
        <?php endforeach; ?>
      </div>
      <div class="blok">
        <p class="blok-titel">Je gastheer</p>
        <h3><?= e($inst['naam']) ?></h3>
        <?php foreach ($inst['intro'] as $p): ?><p style="color:var(--tekst-zacht);font-size:.97rem"><?= e($p) ?></p><?php endforeach; ?>
        <?php if (!empty($inst['troeven'])): ?>
          <ul class="troeven">
            <?php foreach ($inst['troeven'] as $t): ?><li><?= icoon('check') ?><span><?= e($t) ?></span></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <ul class="contact-regels">
          <li><a href="<?= e($inst['website']) ?>" target="_blank" rel="noopener"><?= e(preg_replace('#^https?://(www\.)?#', '', $inst['website'])) ?></a></li>
          <li><a href="tel:<?= e(preg_replace('/\s+/', '', $inst['telefoon'])) ?>"><?= e($inst['telefoon']) ?></a></li>
        </ul>
      </div>
      <div class="blok spreker-mini">
        <img src="/assets/img/<?= e(site('spreker')['foto']) ?>" alt="" width="88" height="88" loading="lazy">
        <p><span class="blok-titel" style="display:block;margin-bottom:4px">Spreker</span><strong><?= e(site('spreker')['naam']) ?></strong><br><span style="color:var(--tekst-zacht);font-size:.94rem">25+ energiecafés, 700+ deelnemers</span></p>
      </div>
    </aside>
  </div>
</section>

<?= view('_avond', ['inst' => $inst]) ?>

<?= view('_spreker') ?>

<?= view('_faq', ['inst' => $inst]) ?>
