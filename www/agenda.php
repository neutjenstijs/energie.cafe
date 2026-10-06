<?php
/* Agenda-bestand (.ics) voor een sessie. Bevat geen persoonsgegevens. */
require dirname(__DIR__) . '/app/bootstrap.php';

$s = sessie((string) ($_GET['s'] ?? ''));
$inst = $s ? installateur($s['installateur']) : null;
if (!$s || !$inst) niet_gevonden();

$esc = fn(string $t) => addcslashes(str_replace(["\r\n", "\n"], '\n', $t), ',;');
$utc = new DateTimeZone('UTC');
$start = $s['start']->setTimezone($utc);
$einde = $s['start']->modify('+2 hours')->setTimezone($utc);
$loc = $s['loc'];

$regels = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//energie.cafe//NL',
    'CALSCALE:GREGORIAN',
    'METHOD:PUBLISH',
    'BEGIN:VEVENT',
    'UID:' . $s['id'] . '@energie.cafe',
    'DTSTAMP:' . gmdate('Ymd\THis\Z'),
    'DTSTART:' . $start->format('Ymd\THis\Z'),
    'DTEND:' . $einde->format('Ymd\THis\Z'),
    'SUMMARY:' . $esc('Energiecafé bij ' . $inst['naam']),
    'LOCATION:' . $esc(($loc['naam'] ?? '') . ', ' . adres_regel($loc)),
    'DESCRIPTION:' . $esc("Gratis infoavond over slim omgaan met energie, met Tijs Neutjens.\nKom een tiental minuten vooraf.\n" . url($inst['slug'])),
    'URL:' . url($inst['slug']),
    'BEGIN:VALARM',
    'TRIGGER:-P1D',
    'ACTION:DISPLAY',
    'DESCRIPTION:' . $esc('Morgen: Energiecafé bij ' . $inst['naam']),
    'END:VALARM',
    'END:VEVENT',
    'END:VCALENDAR',
];

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="energiecafe-' . $s['datum'] . '.ics"');
echo implode("\r\n", $regels) . "\r\n";
