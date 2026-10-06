<?php
require dirname(__DIR__) . '/app/bootstrap.php';

start_sessie();
$ins = isset($_SESSION['bedankt']) ? vind_inschrijving_token($_SESSION['bedankt']) : null;
$sessie = $ins ? sessie($ins['sessie']) : null;
$inst = $sessie ? installateur($sessie['installateur']) : null;

pagina('bedankt', [
    'titel' => 'Je bent ingeschreven',
    'noindex' => true,
    'ins' => $ins,
    'sessie' => $sessie,
    'inst' => $inst,
]);
