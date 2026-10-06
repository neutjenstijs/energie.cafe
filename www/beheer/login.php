<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');

$t = (string) ($_GET['t'] ?? '');
$st = db()->prepare('SELECT * FROM logins WHERE hash = ?');
$st->execute([hash('sha256', $t)]);
$login = $st->fetch();

if (!$login || $login['gebruikt'] || $login['verloopt'] < time() || !mag_inloggen($login['email'])) {
    redirect('/beheer/?verlopen=1');
}

db()->prepare('UPDATE logins SET gebruikt = 1 WHERE hash = ?')->execute([$login['hash']]);
start_sessie();
session_regenerate_id(true);
$_SESSION['beheer_email'] = $login['email'];
redirect('/beheer/');
