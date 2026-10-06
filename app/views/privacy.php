<?php $org = site('organisator'); ?>
<section class="sectie-s donker">
  <div class="wrap smal">
    <p class="eyebrow">Privacy</p>
    <h1 style="font-size:clamp(2rem,4.5vw,3rem)">Privacyverklaring</h1>
    <p class="lead">Kort en duidelijk: we vragen enkel wat nodig is om je avond te organiseren, en we verkopen niets door.</p>
  </div>
</section>
<section class="sectie">
  <div class="wrap smal tekst">
    <p><em>Laatst bijgewerkt: <?= e(datum_lang(new DateTimeImmutable('2026-10-06'))) ?></em></p>

    <h2>Wie zijn we?</h2>
    <p>Het platform energie.cafe wordt beheerd door <strong><?= e($org['naam']) ?></strong>, <?= e($org['adres']) ?>, ondernemingsnummer <?= e($org['kbo']) ?> ("de organisator"). Elk Energiecafé vindt plaats bij een installateur ("de gastheer"), die op de pagina van de avond vermeld staat.</p>
    <ul>
      <li><strong>De organisator</strong> is verantwoordelijk voor deze website en voor het verwerken van je inschrijving.</li>
      <li><strong>De gastheer</strong> ontvangt je gegevens om de avond praktisch te organiseren en is zelf verantwoordelijk voor wat hij daarna met je gegevens doet, binnen de grenzen hieronder.</li>
    </ul>

    <h2>Welke gegevens en waarom?</h2>
    <h3>Als je je inschrijft voor een avond</h3>
    <p>We vragen je naam, e-mailadres, telefoonnummer, postcode, het aantal personen en eventueel een vraag. We gebruiken die om:</p>
    <ul>
      <li>je plaats te reserveren en het aantal deelnemers te kennen;</li>
      <li>je een bevestiging te sturen en je te verwittigen bij een wijziging (bv. als de avond verplaatst wordt);</li>
      <li>je vraag vooraf mee te nemen in de uitleg.</li>
    </ul>
    <p>Rechtsgrond: dit is nodig om je deelname mogelijk te maken (art. 6.1.b AVG). Je gegevens worden daarvoor gedeeld met de gastheer van de avond die je kiest.</p>

    <h3>Als je aanvinkt dat de gastheer je mag contacteren</h3>
    <p>Dan mag de gastheer je na de avond contacteren voor persoonlijk advies over jouw situatie. Dat vakje is optioneel en staat standaard uit. Rechtsgrond: je toestemming (art. 6.1.a AVG). Je kan die toestemming altijd intrekken door het de gastheer of ons te laten weten.</p>
    <p>Zonder die toestemming gebruikt de gastheer je gegevens enkel voor de organisatie van de avond.</p>

    <h3>Als je vraagt om op de hoogte te blijven</h3>
    <p>Dan bewaren we je voornaam, e-mailadres en eventueel je postcode om je één bericht te sturen wanneer er nieuwe datums zijn bij die gastheer. Rechtsgrond: je toestemming.</p>

    <h2>Met wie delen we je gegevens?</h2>
    <ul>
      <li>Met de <strong>gastheer</strong> van de avond waarvoor je inschrijft.</li>
      <li>Met onze <strong>hostingpartner</strong>, die de website en de mail technisch verzorgt in de EU.</li>
    </ul>
    <p>We verkopen je gegevens niet en geven ze niet door aan andere bedrijven. De gastheer engageert zich contractueel om je gegevens ook niet aan derden door te geven.</p>

    <h2>Hoe lang bewaren we ze?</h2>
    <p>Inschrijvingen bewaren we tot 12 maanden na de avond. Daarna worden je naam en contactgegevens automatisch gewist; enkel anonieme aantallen blijven over. Wie op de hoogte wil blijven, bewaren we tot je je uitschrijft, en maximaal 2 jaar.</p>

    <h2>Cookies</h2>
    <p>Deze site gebruikt geen tracking- of advertentiecookies en geen externe statistieken. Er wordt enkel een technisch noodzakelijk cookie gezet als je je inschrijft of inlogt, zodat de site weet wie je bent tijdens dat bezoek.</p>

    <h2>Jouw rechten</h2>
    <p>Je hebt het recht om je gegevens in te kijken, te laten verbeteren of wissen, de verwerking te beperken, bezwaar te maken en je toestemming in te trekken. Stuur daarvoor een mail naar <a href="mailto:<?= e($org['email']) ?>"><?= e($org['email']) ?></a>. Je kan je ook altijd afmelden via de link in je bevestigingsmail.</p>
    <p>Ben je niet tevreden over hoe we met je gegevens omgaan? Dan kan je klacht indienen bij de <a href="https://www.gegevensbeschermingsautoriteit.be" target="_blank" rel="noopener">Gegevensbeschermingsautoriteit</a>.</p>
  </div>
</section>
