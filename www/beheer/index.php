<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
start_sessie();
$email = ingelogd_email();

/* ---------- Niet ingelogd: login met e-maillink ---------- */
if ($email === null || !mag_inloggen($email)) {
    unset($_SESSION['beheer_email']);
    $verstuurd = false;
    $fout = isset($_GET['verlopen']) ? 'Deze inloglink is ongeldig of verlopen. Vraag hieronder een nieuwe aan.' : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        $adres = strtolower(trim((string) ($_POST['email'] ?? '')));
        if (!filter_var($adres, FILTER_VALIDATE_EMAIL)) {
            $fout = 'Vul een geldig e-mailadres in.';
        } else {
            if (mag_inloggen($adres)) {
                $st = db()->prepare('SELECT COUNT(*) FROM logins WHERE email = ? AND aangemaakt > ?');
                $st->execute([$adres, time() - 3600]);
                if ((int) $st->fetchColumn() < 5) {
                    $t = token(32);
                    db()->prepare('INSERT INTO logins (hash, email, verloopt, aangemaakt) VALUES (?, ?, ?, ?)')
                        ->execute([hash('sha256', $t), $adres, time() + 1800, time()]);
                    mail_login($adres, url('beheer/login.php?t=' . $t));
                }
            }
            // Altijd dezelfde boodschap, zodat niemand kan nagaan welke adressen toegang hebben.
            $verstuurd = true;
        }
    }
    echo view('beheer/layout', ['titel' => 'Inloggen', 'inhoud' => view('beheer/login', compact('verstuurd', 'fout'))]);
    exit;
}

/* ---------- Ingelogd ---------- */
$admin = is_admin($email);
$mijn = beheerbare_installateurs($email);
$slug = (string) ($_GET['i'] ?? array_key_first($mijn));
$inst = $mijn[$slug] ?? null;
if (!$inst) { http_response_code(403); exit('Geen toegang tot deze installateur.'); }

$melding = $_SESSION['melding'] ?? null;
unset($_SESSION['melding']);
$fouten = [];
$waarden = [];
$open = (string) ($_GET['open'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $actie = $_POST['actie'] ?? '';
    $terug = '/beheer/?i=' . rawurlencode($slug);

    if ($actie === 'toevoegen') {
        $r = verwerk_inschrijving($_POST, $inst, 'manueel', $email);
        if ($r['ok']) {
            na_inschrijving($r['inschrijving'], $r['sessie'], $inst, !empty($_POST['bevestig']));
            $_SESSION['melding'] = ['ok', 'Inschrijving van ' . $r['inschrijving']['voornaam'] . ' ' . $r['inschrijving']['naam'] . ' toegevoegd.'];
            redirect($terug . '&open=' . rawurlencode($r['sessie']['id']) . '#s-' . rawurlencode($r['sessie']['id']));
        }
        $fouten = $r['fouten'];
        $waarden = $r['waarden'];
        $open = $waarden['sessie'] ?? '';
    } elseif ($actie === 'annuleren') {
        $st = db()->prepare('SELECT * FROM inschrijvingen WHERE id = ? AND installateur = ?');
        $st->execute([(int) ($_POST['id'] ?? 0), $slug]);
        $ins = $st->fetch();
        if ($ins && annuleer_inschrijving($ins, $email, $admin)) {
            $_SESSION['melding'] = ['ok', 'Inschrijving van ' . $ins['voornaam'] . ' ' . $ins['naam'] . ' geannuleerd.'];
        } else {
            $_SESSION['melding'] = ['fout', 'Annuleren lukte niet. Na de start van een avond kan enkel de organisator nog annuleren.'];
        }
        redirect($terug . ($ins ? '&open=' . rawurlencode($ins['sessie']) . '#s-' . rawurlencode($ins['sessie']) : ''));
    }
}

$st = db()->prepare('SELECT * FROM inschrijvingen WHERE installateur = ? ORDER BY aangemaakt');
$st->execute([$slug]);
$perSessie = [];
foreach ($st->fetchAll() as $r) $perSessie[$r['sessie']][] = $r;

$st = db()->prepare('SELECT * FROM updates WHERE installateur = ? AND uitgeschreven IS NULL ORDER BY aangemaakt DESC');
$st->execute([$slug]);
$updates = $st->fetchAll();

echo view('beheer/layout', [
    'titel' => 'Beheer · ' . $inst['naam'],
    'email' => $email,
    'inhoud' => view('beheer/dashboard', compact('inst', 'mijn', 'admin', 'perSessie', 'updates', 'melding', 'fouten', 'waarden', 'open', 'email')),
]);
