<?php
/*
 * ENERGIECAFÉ — INSTALLATEURS EN DATUMS
 * =====================================
 * Dit is het enige bestand dat je moet aanpassen om installateurs, locaties
 * en datums te beheren. Na een wijziging: committen en pushen, de site
 * wordt automatisch bijgewerkt.
 *
 * Per installateur:
 *   naam            Naam zoals getoond op de site.
 *   status          'actief'    = staat op de homepage, inschrijven mogelijk
 *                   'voorbeeld' = pagina enkel via directe link, niet op de
 *                                 homepage, niet in Google, inschrijven uit
 *                   'verborgen' = pagina bestaat niet
 *   gemeente        Getoond op de homepage-kaart ("Energiecafé in ...").
 *   intro           Korte voorstelling (lijst van alinea's).
 *   troeven         Max. 4 korte punten over de installateur.
 *   logo            Bestand in www/assets/img/.
 *   logo_achtergrond Kleur achter het logo op kaarten (meestal '#ffffff').
 *   kleur           Accentkleur in de bevestigingsmail voor de klant.
 *   website, telefoon, email   Publieke contactgegevens.
 *   meldingen       E-mailadressen die bij elke inschrijving een melding
 *                   krijgen. info@techneutjens.be krijgt ALTIJD een kopie
 *                   (zie config/site.php), dat hoef je hier niet te zetten.
 *   beheerders      E-mailadressen die kunnen inloggen op energie.cafe/beheer
 *                   om inschrijvingen te bekijken en zelf in te voeren.
 *   bevestiging_extra  Optionele extra alinea in de bevestigingsmail
 *                   (parking, ingang, ...). Leeg laten mag.
 *   locaties        Eén of meer locaties, elk met een eigen sleutel.
 *   sessies         De datums. Per sessie:
 *                     id        Unieke code, NOOIT meer wijzigen zodra er
 *                               inschrijvingen zijn (bv. 'bes-2026-11-12').
 *                     datum     'JJJJ-MM-DD'
 *                     uur       'UU:MM' (aanvang)
 *                     locatie   Sleutel uit 'locaties'.
 *                     plaatsen  Maximum aantal PERSONEN.
 *                     gesloten  (optioneel) true = inschrijvingen dicht.
 *
 * Voorbije sessies verdwijnen vanzelf van de site. Laat ze gerust staan:
 * in het beheer blijven hun inschrijvingen zichtbaar.
 */

return [

    'techneutjens' => [
        'naam'      => 'TechNeutjens',
        'status'    => 'actief',
        'gemeente'  => 'Duffel',
        'intro'     => [
            'TechNeutjens is een installateur uit Duffel, gespecialiseerd in zonnepanelen, thuisbatterijen, laadpalen en slim energiebeheer.',
            'Het Energiecafé vindt plaats in de EnergyLoft, de eigen toonzaal van TechNeutjens. Na de uitleg zie je de technieken er ook in het echt staan.',
        ],
        'troeven'   => [
            'Zonnepanelen, thuisbatterijen en laadpalen',
            'Slim energiebeheer en dynamische tarieven',
            'Persoonlijke begeleiding van advies tot nazorg',
        ],
        'logo'             => 'logo-techneutjens.png',
        'logo_achtergrond' => '#ffffff',
        'kleur'     => '#DBAA49',
        'website'   => 'https://techneutjens.be',
        'telefoon'  => '015 54 05 54',
        'email'     => 'info@techneutjens.be',
        'meldingen' => ['info@techneutjens.be'],
        'beheerders'=> ['tijs@techneutjens.be', 'info@techneutjens.be'],
        'bevestiging_extra' => '',
        'locaties'  => [
            'energyloft' => [
                'naam'     => 'EnergyLoft TechNeutjens',
                'adres'    => 'Notmeir 46B',
                'postcode' => '2570',
                'gemeente' => 'Duffel',
            ],
        ],
        'sessies'   => [
            ['id' => 'tn-2026-10-17', 'datum' => '2026-10-17', 'uur' => '10:00', 'locatie' => 'energyloft', 'plaatsen' => 30],
            ['id' => 'tn-2026-11-09', 'datum' => '2026-11-09', 'uur' => '19:00', 'locatie' => 'energyloft', 'plaatsen' => 30],
            ['id' => 'tn-2026-12-08', 'datum' => '2026-12-08', 'uur' => '19:00', 'locatie' => 'energyloft', 'plaatsen' => 30],
        ],
    ],

    'bes' => [
        'naam'      => 'Belgian Energy Systems',
        'status'    => 'actief',
        'gemeente'  => 'Ledegem',
        'intro'     => [
            'Belgian Energy Systems (BES) is een familiebedrijf uit Ledegem, sinds 2001 gespecialiseerd in zonne-energie en batterijopslag. Intussen staan er meer dan 5.000 installaties bij particulieren en bedrijven.',
            'Het kleine, hechte team werkt enkel met betrouwbare merken en hecht veel belang aan persoonlijk contact: je hebt altijd één aanspreekpunt voor je vragen.',
        ],
        'troeven'   => [
            'Familiebedrijf sinds 2001',
            'Meer dan 5.000 installaties',
            'Zonnepanelen, batterijen, laadpalen en energiebeheer',
        ],
        'logo'             => 'logo-bes.png',
        'logo_achtergrond' => '#ffffff',
        'kleur'     => '#F2B705',
        'website'   => 'https://www.bes.be',
        'telefoon'  => '051 22 82 03',
        'email'     => 'info@bes.be',
        'meldingen' => ['info@bes.be'],
        'beheerders'=> ['info@bes.be'],
        'bevestiging_extra' => '',
        'locaties'  => [
            'kantoor' => [
                'naam'     => 'Belgian Energy Systems',
                'adres'    => 'Nijverheidslaan 1B',
                'postcode' => '8880',
                'gemeente' => 'Ledegem',
            ],
        ],
        'sessies'   => [
            ['id' => 'bes-2026-11-17', 'datum' => '2026-11-17', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 50],
            ['id' => 'bes-2026-12-02', 'datum' => '2026-12-02', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 50],
            ['id' => 'bes-2027-01-26', 'datum' => '2027-01-26', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 50],
            ['id' => 'bes-2027-02-24', 'datum' => '2027-02-24', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 50],
        ],
    ],

    // DEMO Pull The Plug — staat uit. Op 'voorbeeld' zetten = pagina enkel via directe link, niet op de homepage, geen inschrijvingen.
    // Inschrijvingen komen nu enkel bij info@techneutjens.be terecht.
    'pulltheplug' => [
        'naam'      => 'Pull The Plug',
        'status'    => 'verborgen',
        'gemeente'  => 'Overijse',
        'intro'     => [
            'Pull The Plug uit Overijse plaatst al meer dan 15 jaar zonnepanelen, batterijsystemen en slimme laadpalen bij particulieren en bedrijven.',
            'Hun ploeg staat bekend om de hoge afwerkingsgraad en een aanpak van A tot Z: van het eerste advies tot de nazorg.',
        ],
        'troeven'   => [
            'Meer dan 15 jaar ervaring',
            'Zeer hoge afwerkingsgraad',
            'Ontzorging van A tot Z',
        ],
        'logo'             => 'logo-pulltheplug.png',
        'logo_achtergrond' => '#ffffff',
        'kleur'     => '#22A653',
        'website'   => 'https://pulltheplug.be',
        'telefoon'  => '02 615 17 77',
        'email'     => 'info@pulltheplug.be',
        'meldingen' => [],
        'beheerders'=> [],
        'bevestiging_extra' => '',
        'locaties'  => [
            'kantoor' => [
                'naam'     => 'Pull The Plug',
                'adres'    => 'Kerkstraat 15',
                'postcode' => '3090',
                'gemeente' => 'Overijse',
            ],
        ],
        'sessies'   => [
            // Voorbeelddatums, enkel ter illustratie.
            ['id' => 'ptp-voorbeeld-1', 'datum' => '2027-01-21', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 40],
            ['id' => 'ptp-voorbeeld-2', 'datum' => '2027-03-18', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 40],
        ],
    ],

    // DEMO BW-Tec — staat uit. Op 'voorbeeld' zetten = pagina enkel via directe link, niet op de homepage, geen inschrijvingen.
    // Inschrijvingen komen nu enkel bij info@techneutjens.be terecht.
    'bwtec' => [
        'naam'      => 'BW-Tec',
        'status'    => 'verborgen',
        'gemeente'  => 'Lo-Reninge',
        'intro'     => [
            'BW-Tec uit Noordschote (Lo-Reninge) is het installatiebedrijf van Wouter Beirnaert, actief in residentiële en industriële projecten.',
            'Van zonnepanelen en thuisbatterijen over algemene elektriciteit tot verwarming, ventilatie en warmtepompen: alles met de precisie van een vakman.',
        ],
        'troeven'   => [
            'Zonnepanelen en thuisbatterijen',
            'Elektriciteit, HVAC en warmtepompen',
            'Residentieel en industrieel',
        ],
        'logo'             => 'logo-bwtec.png',
        'logo_achtergrond' => '#ffffff',
        'kleur'     => '#E8630A',
        'website'   => 'https://www.bwtec.be',
        'telefoon'  => '0491 56 96 49',
        'email'     => 'info@bwtec.be',
        'meldingen' => [],
        'beheerders'=> [],
        'bevestiging_extra' => '',
        'locaties'  => [
            'kantoor' => [
                'naam'     => 'BW-Tec',
                'adres'    => 'Middelstraat 36',
                'postcode' => '8647',
                'gemeente' => 'Noordschote (Lo-Reninge)',
            ],
        ],
        'sessies'   => [
            // Voorbeelddatums, enkel ter illustratie.
            ['id' => 'bwtec-voorbeeld-1', 'datum' => '2027-02-11', 'uur' => '19:30', 'locatie' => 'kantoor', 'plaatsen' => 40],
            ['id' => 'bwtec-voorbeeld-2', 'datum' => '2027-04-22', 'uur' => '19:30', 'locatie' => 'kantoor', 'plaatsen' => 40],
        ],
    ],

];
