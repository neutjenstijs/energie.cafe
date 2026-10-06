<?php
/**
 * @var array $inst  @var array $mijn  @var bool $admin  @var array $perSessie  @var array $updates
 * @var array|null $melding  @var array $fouten  @var array $waarden  @var string $open  @var string $email
 */
$komend = komende_sessies($inst);
$voorbij = array_reverse(array_values(array_filter($inst['sessies'], 'sessie_voorbij')));
$q = '?i=' . rawurlencode($inst['slug']);
$w = fn(string $k, $d = '') => $waarden[$k] ?? $d;
$fm = fn(string $k) => isset($fouten[$k]) ? '<span class="foutmelding">' . e($fouten[$k]) . '</span>' : '';

$sessieBlok = function (array $s, bool $isOpen) use ($perSessie, $q, $admin) {
    $rijen = $perSessie[$s['id']] ?? [];
    $actief = array_filter($rijen, fn($r) => !$r['geannuleerd']);
    $pers = array_sum(array_column($actief, 'personen'));
    $pct = $s['plaatsen'] ? min(100, round($pers / $s['plaatsen'] * 100)) : 0;
    $voorbij = sessie_voorbij($s);
    ob_start(); ?>
    <details class="b-sessie<?= $voorbij ? ' voorbij' : '' ?>" id="s-<?= e($s['id']) ?>" <?= $isOpen ? 'open' : '' ?>>
      <summary>
        <div>
          <div class="b-titel"><?= e(ucfirst(datum_lang($s['start']))) ?> · <?= e(uur($s['start'])) ?></div>
          <div class="b-sub"><?= e(($s['loc']['naam'] ?? '') . ', ' . ($s['loc']['gemeente'] ?? '')) ?><?= $voorbij ? ' · voorbij' : '' ?><?= !empty($s['gesloten']) ? ' · inschrijvingen gesloten' : '' ?></div>
        </div>
        <div>
          <div class="balk<?= $pers >= $s['plaatsen'] ? ' vol' : '' ?>"><span style="width:<?= $pct ?>%"></span></div>
          <div class="b-tal"><strong><?= $pers ?></strong> / <?= (int) $s['plaatsen'] ?> personen · <strong><?= count($actief) ?></strong> inschrijvingen</div>
        </div>
        <div class="b-acties">
          <a class="knop knop-rand knop-klein" href="/beheer/export.php<?= $q ?>&s=<?= e(rawurlencode($s['id'])) ?>" onclick="event.stopPropagation()"><?= icoon('download') ?> Excel</a>
        </div>
      </summary>
      <div class="b-inhoud">
        <?php if (!$rijen): ?>
          <p class="leeg">Nog geen inschrijvingen.</p>
        <?php else: ?>
          <div class="tabel-wrap">
            <table class="lijst">
              <thead><tr><th>Naam</th><th>Pers.</th><th>Contact</th><th>E-mail / telefoon</th><th>Postcode</th><th>Vraag</th><th>Bron</th><th></th></tr></thead>
              <tbody>
              <?php foreach ($rijen as $r): ?>
                <tr class="<?= $r['geannuleerd'] ? 'geannuleerd' : '' ?>">
                  <td><strong><?= e($r['voornaam'] . ' ' . $r['naam']) ?></strong><br><span style="color:var(--tekst-zacht);font-size:.82rem"><?= e(date('d/m H:i', strtotime($r['aangemaakt']))) ?></span></td>
                  <td><?= (int) $r['personen'] ?></td>
                  <td><?= $r['contact_ok'] ? '<span class="tag ja">ja</span>' : '<span class="tag">nee</span>' ?></td>
                  <td><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><br><a href="tel:<?= e(preg_replace('/\s+/', '', $r['telefoon'])) ?>"><?= e($r['telefoon']) ?></a></td>
                  <td><?= e($r['postcode']) ?></td>
                  <td style="max-width:280px"><?= nl2br(e($r['vraag'])) ?></td>
                  <td><?= $r['bron'] === 'manueel' ? '<span class="tag man" title="' . e($r['ingevoerd_door']) . '">manueel</span>' : '<span class="tag">website</span>' ?></td>
                  <td class="acties">
                    <?php if ($r['geannuleerd']): ?>
                      <span class="tag">geannuleerd</span>
                    <?php elseif (!$voorbij || $admin): ?>
                      <form method="post" action="/beheer/<?= $q ?>" onsubmit="return confirm('Inschrijving van <?= e(addslashes($r['voornaam'] . ' ' . $r['naam'])) ?> annuleren?')">
                        <?= csrf_veld() ?>
                        <input type="hidden" name="actie" value="annuleren">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <button class="knop-link" type="submit">annuleren</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </details>
    <?php return ob_get_clean();
};
?>

<?php if (count($mijn) > 1): ?>
  <nav class="tabs" aria-label="Installateurs">
    <?php foreach ($mijn as $i): ?>
      <a href="/beheer/?i=<?= e(rawurlencode($i['slug'])) ?>" class="<?= $i['slug'] === $inst['slug'] ? 'actief' : '' ?>"><?= e($i['naam']) ?><?= $i['status'] !== 'actief' ? ' (' . e($i['status']) . ')' : '' ?></a>
    <?php endforeach; ?>
  </nav>
<?php endif; ?>

<div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:end;gap:16px;margin-bottom:24px">
  <div>
    <p class="eyebrow" style="margin-bottom:8px">Inschrijvingen</p>
    <h1 style="margin:0"><?= e($inst['naam']) ?></h1>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a class="knop knop-rand knop-klein" href="/<?= e($inst['slug']) ?>" target="_blank" rel="noopener">Bekijk publieke pagina</a>
    <a class="knop knop-zwart knop-klein" href="/beheer/export.php<?= $q ?>"><?= icoon('download') ?> Alles naar Excel</a>
  </div>
</div>

<?php if ($melding): ?>
  <div class="melding <?= e($melding[0]) ?>"><?= e($melding[1]) ?></div>
<?php endif; ?>

<?php if ($komend): ?>
<details class="b-sessie" <?= $fouten ? 'open' : '' ?> id="toevoegen">
  <summary style="grid-template-columns:1fr auto">
    <div>
      <div class="b-titel">+ Inschrijving toevoegen</div>
      <div class="b-sub">Iemand schreef zich in via telefoon, mail of WhatsApp? Voer het hier in, zo blijft het overzicht volledig.</div>
    </div>
  </summary>
  <div class="b-inhoud">
    <form method="post" action="/beheer/<?= $q ?>" class="toevoegen" novalidate>
      <?= csrf_veld() ?>
      <input type="hidden" name="actie" value="toevoegen">
      <?php if ($fouten): ?><div class="melding fout">Kijk de velden even na.</div><?php endif; ?>
      <div class="velden">
        <div class="veld vol">
          <label for="t-sessie">Avond</label>
          <select id="t-sessie" name="sessie">
            <?php foreach ($komend as $s):
              $pers = array_sum(array_column(array_filter($perSessie[$s['id']] ?? [], fn($r) => !$r['geannuleerd']), 'personen')); ?>
              <option value="<?= e($s['id']) ?>" <?= $w('sessie') === $s['id'] ? 'selected' : '' ?>><?= e(ucfirst(datum_lang($s['start'])) . ', ' . uur($s['start'])) ?> (<?= $pers ?>/<?= (int) $s['plaatsen'] ?>)</option>
            <?php endforeach; ?>
          </select>
          <?= $fm('sessie') ?>
        </div>
        <div class="veld"><label for="t-voornaam">Voornaam</label><input id="t-voornaam" name="voornaam" value="<?= e($w('voornaam')) ?>"><?= $fm('voornaam') ?></div>
        <div class="veld"><label for="t-naam">Achternaam</label><input id="t-naam" name="naam" value="<?= e($w('naam')) ?>"><?= $fm('naam') ?></div>
        <div class="veld"><label for="t-postcode">Postcode</label><input id="t-postcode" name="postcode" value="<?= e($w('postcode')) ?>"><?= $fm('postcode') ?></div>
        <div class="veld"><label for="t-email">E-mail</label><input id="t-email" name="email" type="email" value="<?= e($w('email')) ?>"><?= $fm('email') ?></div>
        <div class="veld"><label for="t-telefoon">Telefoon</label><input id="t-telefoon" name="telefoon" value="<?= e($w('telefoon')) ?>"><?= $fm('telefoon') ?></div>
        <div class="veld"><span class="label">Personen</span>
          <div class="keuze">
            <label><input type="radio" name="personen" value="1" <?= (int) $w('personen', 2) === 1 ? 'checked' : '' ?>><span>1</span></label>
            <label><input type="radio" name="personen" value="2" <?= (int) $w('personen', 2) !== 1 ? 'checked' : '' ?>><span>2</span></label>
          </div><?= $fm('personen') ?>
        </div>
        <div class="veld vol"><label for="t-vraag">Vraag <span class="optioneel">(optioneel)</span></label><input id="t-vraag" name="vraag" value="<?= e($w('vraag')) ?>"></div>
        <div class="veld vol">
          <label class="vinkje"><input type="checkbox" name="contact_ok" value="1" <?= $w('contact_ok') ? 'checked' : '' ?>><span>De deelnemer gaf toestemming om na de avond gecontacteerd te worden.</span></label>
          <label class="vinkje"><input type="checkbox" name="bevestig" value="1" checked><span>Stuur de deelnemer een bevestigingsmail (met afmeldlink).</span></label>
        </div>
      </div>
      <div class="verzenden" style="margin-top:16px"><button class="knop knop-zwart knop-klein" type="submit">Inschrijving toevoegen</button>
        <span class="hulp" style="font-size:.88rem;color:var(--tekst-zacht)">Manuele inschrijvingen mogen boven het maximum gaan.</span></div>
    </form>
  </div>
</details>
<?php endif; ?>

<h2 style="font-size:1.3rem;margin:34px 0 14px">Komende avonden</h2>
<?php if (!$komend): ?>
  <p class="leeg">Er staan geen komende avonden gepland. Nieuwe datums worden toegevoegd door de organisator.</p>
<?php endif; ?>
<?php foreach ($komend as $n => $s) echo $sessieBlok($s, $open === $s['id'] || ($open === '' && $n === 0)); ?>

<?php if ($voorbij): ?>
  <h2 style="font-size:1.3rem;margin:34px 0 14px">Voorbije avonden</h2>
  <?php foreach ($voorbij as $s) echo $sessieBlok($s, $open === $s['id']); ?>
<?php endif; ?>

<h2 style="font-size:1.3rem;margin:34px 0 14px">Wil op de hoogte blijven (<?= count($updates) ?>)</h2>
<div class="b-sessie" style="padding:6px 22px 18px">
  <?php if (!$updates): ?>
    <p class="leeg">Nog niemand.</p>
  <?php else: ?>
    <div class="tabel-wrap">
      <table class="lijst">
        <thead><tr><th>Voornaam</th><th>E-mail</th><th>Postcode</th><th>Sinds</th></tr></thead>
        <tbody>
        <?php foreach ($updates as $u): ?>
          <tr><td><?= e($u['voornaam']) ?></td><td><a href="mailto:<?= e($u['email']) ?>"><?= e($u['email']) ?></a></td><td><?= e($u['postcode']) ?></td><td><?= e(date('d/m/Y', strtotime($u['aangemaakt']))) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p style="margin:14px 0 0"><a class="knop knop-rand knop-klein" href="/beheer/export.php<?= $q ?>&wat=updates"><?= icoon('download') ?> Excel</a></p>
  <?php endif; ?>
</div>
