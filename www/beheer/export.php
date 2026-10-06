<?php
/* CSV-export (opent in Excel) van inschrijvingen of "hou me op de hoogte". */
require dirname(__DIR__, 2) . '/app/bootstrap.php';

header('X-Robots-Tag: noindex, nofollow');
$email = ingelogd_email();
$mijn = beheerbare_installateurs($email);
$slug = (string) ($_GET['i'] ?? '');
$inst = $mijn[$slug] ?? null;
if (!$inst) { http_response_code(403); exit('Geen toegang.'); }

$wat = (string) ($_GET['wat'] ?? 'inschrijvingen');
$sessieId = (string) ($_GET['s'] ?? '');

if ($wat === 'updates') {
    $st = db()->prepare('SELECT voornaam, email, postcode, aangemaakt FROM updates WHERE installateur = ? AND uitgeschreven IS NULL ORDER BY aangemaakt');
    $st->execute([$slug]);
    $rijen = $st->fetchAll();
    $kop = ['Voornaam', 'E-mail', 'Postcode', 'Aangevraagd op'];
    $bestand = 'energiecafe-' . $slug . '-op-de-hoogte.csv';
} else {
    $sql = 'SELECT * FROM inschrijvingen WHERE installateur = ?';
    $par = [$slug];
    if ($sessieId !== '') { $sql .= ' AND sessie = ?'; $par[] = $sessieId; }
    $st = db()->prepare($sql . ' ORDER BY sessie, aangemaakt');
    $st->execute($par);
    $rijen = [];
    foreach ($st->fetchAll() as $r) {
        $s = sessie($r['sessie']);
        $rijen[] = [
            $s ? $s['start']->format('d/m/Y H:i') : $r['sessie'],
            $r['voornaam'], $r['naam'], $r['email'], $r['telefoon'], $r['postcode'], $r['personen'],
            $r['contact_ok'] ? 'ja' : 'nee',
            $r['vraag'],
            $r['bron'] === 'manueel' ? 'manueel (' . $r['ingevoerd_door'] . ')' : 'website',
            date('d/m/Y H:i', strtotime($r['aangemaakt'])),
            $r['geannuleerd'] ? 'geannuleerd ' . date('d/m/Y', strtotime($r['geannuleerd'])) : 'ingeschreven',
        ];
    }
    $kop = ['Avond', 'Voornaam', 'Naam', 'E-mail', 'Telefoon', 'Postcode', 'Personen', 'Mag gecontacteerd worden', 'Vraag', 'Bron', 'Ingeschreven op', 'Status'];
    $bestand = 'energiecafe-' . $slug . ($sessieId !== '' ? '-' . ($s['datum'] ?? $sessieId) : '') . '.csv';
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-z0-9.-]/i', '-', $bestand) . '"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM zodat Excel UTF-8 herkent
fputcsv($out, $kop, ';', '"', '');
foreach ($rijen as $r) {
    // Voorkom dat Excel celinhoud als formule uitvoert.
    $r = array_map(fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : $v, array_values($r));
    fputcsv($out, $r, ';', '"', '');
}
fclose($out);
