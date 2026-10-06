<?php
require dirname(__DIR__) . '/app/bootstrap.php';

pagina('home', [
    'titel' => '',
    'installateurs' => actieve_installateurs(),
    'bez' => bezetting(),
    'canonical' => url(),
]);
