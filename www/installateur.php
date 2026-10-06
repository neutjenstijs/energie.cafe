<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$inst = installateur((string) ($_GET['slug'] ?? ''));
if (!$inst) niet_gevonden();

$fouten = $waarden = $update_fouten = $update_waarden = [];
$update_ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $inst['status'] === 'actief') {
    $actie = $_POST['actie'] ?? '';
    if (spam_verdacht()) {
        // Stil negeren: doe alsof het gelukt is, zonder iets te bewaren.
        if ($actie !== 'update') redirect('/bedankt.php');
        $update_ok = true;
    } elseif ($actie === 'inschrijven') {
        $r = verwerk_inschrijving($_POST, $inst);
        if ($r['ok']) {
            na_inschrijving($r['inschrijving'], $r['sessie'], $inst);
            start_sessie();
            $_SESSION['bedankt'] = $r['inschrijving']['token'];
            redirect('/bedankt.php');
        }
        $fouten = $r['fouten'];
        $waarden = $r['waarden'];
    } elseif ($actie === 'update') {
        $r = verwerk_update($_POST, $inst);
        if ($r['ok']) {
            $update_ok = true;
        } else {
            $update_fouten = $r['fouten'];
            $update_waarden = $r['waarden'];
        }
    }
}

pagina('installateur', [
    'titel' => 'Energiecafé bij ' . $inst['naam'] . ' in ' . $inst['gemeente'],
    'beschrijving' => 'Gratis infoavond over thuisbatterijen, dynamische tarieven en slim energiebeheer bij ' . $inst['naam'] . ' in ' . $inst['gemeente'] . '. Met Tijs Neutjens. Schrijf je in.',
    'nav' => 'installateur',
    'menu_naam' => $inst['naam'],
    'noindex' => $inst['status'] === 'voorbeeld',
    'canonical' => url($inst['slug']),
    'inst' => $inst,
    'bez' => bezetting(),
    'fouten' => $fouten,
    'waarden' => $waarden,
    'update_fouten' => $update_fouten,
    'update_waarden' => $update_waarden,
    'update_ok' => $update_ok,
]);
