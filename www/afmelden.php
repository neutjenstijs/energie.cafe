<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$token = (string) ($_GET['t'] ?? $_POST['t'] ?? '');
$ins = $token !== '' ? vind_inschrijving_token($token) : null;
$sessie = $ins ? sessie($ins['sessie']) : null;
$inst = $sessie ? installateur($sessie['installateur']) : null;
$status = 'vraag';

if (!$ins || !$sessie || !$inst) {
    $status = 'onbekend';
} elseif ($ins['geannuleerd']) {
    $status = 'al';
} elseif (sessie_voorbij($sessie)) {
    $status = 'voorbij';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    annuleer_inschrijving($ins, 'deelnemer zelf');
    $status = 'gedaan';
}

pagina('afmelden', [
    'titel' => 'Afmelden',
    'noindex' => true,
    'status' => $status,
    'ins' => $ins,
    'sessie' => $sessie,
    'inst' => $inst,
    'token' => $token,
]);
