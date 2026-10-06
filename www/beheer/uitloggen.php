<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';

start_sessie();
$_SESSION = [];
session_destroy();
redirect('/beheer/');
