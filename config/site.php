<?php
/*
 * Algemene instellingen van energie.cafe.
 * Geheime gegevens (SMTP-wachtwoord, sleutel) staan NIET hier maar in
 * data/geheim.php op de server. Zie BEHEER.md.
 */
return [
    'url'  => 'https://energie.cafe',

    // Krijgt een kopie van ELKE inschrijving, bij elke installateur.
    'centrale_melding' => 'info@techneutjens.be',

    // Mogen inloggen op /beheer en zien alle installateurs.
    'admins' => ['tijs@techneutjens.be', 'info@techneutjens.be'],

    // Afzender van alle mails. Antwoorden gaan naar de installateur.
    'afzender_email' => 'inschrijving@energie.cafe',
    'afzender_naam'  => 'Energiecafé',

    // De spreker.
    'spreker' => [
        'naam'  => 'Tijs Neutjens',
        'foto'  => 'tijs-neutjens.jpg',
    ],

    // Cijfers op de homepage.
    'cijfers' => [
        ['waarde' => '25+',  'label' => 'energiecafés sinds 2023'],
        ['waarde' => '700+', 'label' => 'deelnemers'],
        ['waarde' => '45′',  'label' => 'heldere uitleg, daarna al je vragen'],
    ],

    // "In het verleden al te horen bij": verwijder een regel als je het niet mag vermelden.
    'referenties' => [
        'ElParo (Hamont-Achel)',
        'Elektro KG Sun (Beveren)',
        'Masterclass van ODE Vlaanderen',
    ],

    // Wettelijke vermelding (beheerder van het platform).
    'organisator' => [
        'naam'   => 'TechNeutjens BV',
        'adres'  => 'Ekelenhoek 41a, 2860 Sint-Katelijne-Waver',
        'kbo'    => 'BE 0695.435.560',
        'email'  => 'info@techneutjens.be',
    ],
];
