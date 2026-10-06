<?php
declare(strict_types=1);

/*
 * Gedeelde code voor energie.cafe: configuratie, databank, sessies,
 * hulpfuncties. Elke pagina in www/ begint met:
 *   require dirname(__DIR__) . '/app/bootstrap.php';
 */

const ROOT = __DIR__ . '/..';
date_default_timezone_set('Europe/Brussels');
mb_internal_encoding('UTF-8');

/* ---------- Configuratie ---------- */

function site(?string $key = null)
{
    static $site;
    $site ??= require ROOT . '/config/site.php';
    return $key === null ? $site : ($site[$key] ?? null);
}

function geheim(string $key, $default = null)
{
    static $geheim;
    if ($geheim === null) {
        $f = ROOT . '/data/geheim.php';
        $geheim = is_file($f) ? require $f : [];
    }
    return $geheim[$key] ?? $default;
}

function app_key(): string
{
    $key = geheim('app_key');
    if ($key) return $key;
    // Geen sleutel ingesteld: maak er eenmalig één aan in data/.
    $f = ROOT . '/data/app_key';
    if (!is_file($f)) {
        @mkdir(dirname($f), 0750, true);
        file_put_contents($f, bin2hex(random_bytes(32)));
    }
    return trim((string) file_get_contents($f));
}

function is_dev(): bool
{
    return (bool) geheim('dev', false);
}

/** Alle installateurs (behalve 'verborgen'), met slug erbij. */
function installateurs(): array
{
    static $all;
    if ($all === null) {
        $all = [];
        foreach (require ROOT . '/config/installateurs.php' as $slug => $i) {
            if (($i['status'] ?? 'actief') === 'verborgen') continue;
            $i['slug'] = $slug;
            $i['meldingen'] ??= [];
            $i['beheerders'] ??= [];
            $i['locaties'] ??= [];
            $sessies = [];
            foreach ($i['sessies'] ?? [] as $s) {
                $s['installateur'] = $slug;
                $s['loc'] = $i['locaties'][$s['locatie']] ?? reset($i['locaties']) ?: [];
                $s['start'] = new DateTimeImmutable($s['datum'] . ' ' . $s['uur']);
                $sessies[] = $s;
            }
            usort($sessies, fn($a, $b) => $a['start'] <=> $b['start']);
            $i['sessies'] = $sessies;
            $all[$slug] = $i;
        }
    }
    return $all;
}

function installateur(string $slug): ?array
{
    return installateurs()[$slug] ?? null;
}

function actieve_installateurs(): array
{
    return array_filter(installateurs(), fn($i) => $i['status'] === 'actief');
}

/** Sessie opzoeken op id, over alle installateurs heen. */
function sessie(string $id): ?array
{
    foreach (installateurs() as $i) {
        foreach ($i['sessies'] as $s) {
            if ($s['id'] === $id) return $s;
        }
    }
    return null;
}

function komende_sessies(array $inst): array
{
    $nu = new DateTimeImmutable();
    return array_values(array_filter($inst['sessies'], fn($s) => $s['start'] > $nu));
}

function sessie_voorbij(array $s): bool
{
    return $s['start'] <= new DateTimeImmutable();
}

/* ---------- Databank ---------- */

function db(): PDO
{
    static $pdo;
    if ($pdo) return $pdo;
    $dir = ROOT . '/data';
    if (!is_dir($dir)) mkdir($dir, 0750, true);
    $pdo = new PDO('sqlite:' . $dir . '/energiecafe.sqlite', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA busy_timeout = 5000');
    $pdo->exec('PRAGMA foreign_keys = ON');
    migreer($pdo);
    opschonen($pdo);
    return $pdo;
}

/**
 * Bewaartermijnen uit de privacyverklaring, max. één keer per dag uitgevoerd:
 *  - inschrijvingen: 12 maanden na de avond geanonimiseerd (aantallen blijven);
 *  - "hou me op de hoogte": na 2 jaar gewist;
 *  - verlopen inloglinks: gewist.
 */
function opschonen(PDO $pdo): void
{
    $vlag = ROOT . '/data/laatste_opschoning';
    if (is_file($vlag) && filemtime($vlag) > time() - 86400) return;
    @touch($vlag);

    $grens = new DateTimeImmutable('-12 months');
    $oud = [];
    foreach (installateurs() as $i) {
        foreach ($i['sessies'] as $s) if ($s['start'] < $grens) $oud[] = $s['id'];
    }
    $anon = "voornaam = '(gewist)', naam = '', email = '', telefoon = '', postcode = '', vraag = ''";
    if ($oud) {
        $in = implode(',', array_fill(0, count($oud), '?'));
        $pdo->prepare("UPDATE inschrijvingen SET $anon WHERE email <> '' AND sessie IN ($in)")->execute($oud);
    }
    // Vangnet voor sessies die intussen uit de configuratie verwijderd zijn.
    $pdo->prepare("UPDATE inschrijvingen SET $anon WHERE email <> '' AND aangemaakt < ?")->execute([(new DateTimeImmutable('-18 months'))->format('Y-m-d H:i:s')]);
    $pdo->prepare('DELETE FROM updates WHERE aangemaakt < ?')->execute([(new DateTimeImmutable('-2 years'))->format('Y-m-d H:i:s')]);
    $pdo->prepare('DELETE FROM logins WHERE verloopt < ?')->execute([time() - 86400]);
}

function migreer(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inschrijvingen (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            installateur TEXT NOT NULL,
            sessie TEXT NOT NULL,
            voornaam TEXT NOT NULL,
            naam TEXT NOT NULL,
            email TEXT NOT NULL,
            telefoon TEXT NOT NULL,
            postcode TEXT NOT NULL,
            personen INTEGER NOT NULL,
            vraag TEXT NOT NULL DEFAULT '',
            contact_ok INTEGER NOT NULL DEFAULT 0,
            bron TEXT NOT NULL DEFAULT 'website',
            ingevoerd_door TEXT NOT NULL DEFAULT '',
            token TEXT NOT NULL UNIQUE,
            aangemaakt TEXT NOT NULL,
            geannuleerd TEXT,
            geannuleerd_door TEXT
        );
        CREATE INDEX IF NOT EXISTS idx_ins_sessie ON inschrijvingen(sessie);
        CREATE INDEX IF NOT EXISTS idx_ins_inst ON inschrijvingen(installateur);

        CREATE TABLE IF NOT EXISTS updates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            installateur TEXT NOT NULL,
            voornaam TEXT NOT NULL,
            email TEXT NOT NULL,
            postcode TEXT NOT NULL DEFAULT '',
            token TEXT NOT NULL UNIQUE,
            aangemaakt TEXT NOT NULL,
            uitgeschreven TEXT,
            UNIQUE(installateur, email)
        );

        CREATE TABLE IF NOT EXISTS logins (
            hash TEXT PRIMARY KEY,
            email TEXT NOT NULL,
            verloopt INTEGER NOT NULL,
            gebruikt INTEGER NOT NULL DEFAULT 0,
            aangemaakt INTEGER NOT NULL
        );
    ");
}

/** Aantal bezette plaatsen (personen) per sessie-id. */
function bezetting(?array $ids = null): array
{
    $rows = db()->query("SELECT sessie, SUM(personen) p, COUNT(*) n FROM inschrijvingen WHERE geannuleerd IS NULL GROUP BY sessie")->fetchAll();
    $uit = [];
    foreach ($rows as $r) $uit[$r['sessie']] = ['personen' => (int) $r['p'], 'inschrijvingen' => (int) $r['n']];
    return $uit;
}

function vrije_plaatsen(array $s, array $bez): int
{
    return max(0, (int) $s['plaatsen'] - ($bez[$s['id']]['personen'] ?? 0));
}

function nu(): string
{
    return (new DateTimeImmutable())->format('Y-m-d H:i:s');
}

function token(int $bytes = 16): string
{
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

/* ---------- Uitvoer ---------- */

function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $pad = ''): string
{
    return rtrim((string) (geheim('url') ?? site('url')), '/') . '/' . ltrim($pad, '/');
}

function asset(string $pad): string
{
    $f = ROOT . '/www/assets/' . $pad;
    $v = is_file($f) ? substr(md5((string) filemtime($f)), 0, 8) : '0';
    return '/assets/' . $pad . '?v=' . $v;
}

const DAGEN = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];
const MAANDEN = [1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];

/** "zaterdag 17 oktober 2026" */
function datum_lang(DateTimeImmutable $d, bool $jaar = true): string
{
    return DAGEN[(int) $d->format('w')] . ' ' . $d->format('j') . ' ' . MAANDEN[(int) $d->format('n')] . ($jaar ? ' ' . $d->format('Y') : '');
}

/** "za 17/10" */
function datum_kort(DateTimeImmutable $d): string
{
    return mb_substr(DAGEN[(int) $d->format('w')], 0, 2) . ' ' . $d->format('d/m');
}

function uur(DateTimeImmutable $d): string
{
    return $d->format('i') === '00' ? $d->format('G') . 'u' : $d->format('G\ui');
}

function adres_regel(array $loc): string
{
    return trim(($loc['adres'] ?? '') . ', ' . ($loc['postcode'] ?? '') . ' ' . ($loc['gemeente'] ?? ''), ' ,');
}

function maps_link(array $loc): string
{
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(($loc['naam'] ?? '') . ', ' . adres_regel($loc));
}

function view(string $naam, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require ROOT . '/app/views/' . $naam . '.php';
    return (string) ob_get_clean();
}

/** Volledige pagina renderen binnen de layout. */
function pagina(string $naam, array $data = []): void
{
    $data['inhoud'] = view($naam, $data);
    echo view('layout', $data);
}

function redirect(string $pad, int $code = 303): never
{
    header('Location: ' . $pad, true, $code);
    exit;
}

function niet_gevonden(): never
{
    http_response_code(404);
    pagina('404', ['titel' => 'Pagina niet gevonden', 'noindex' => true]);
    exit;
}

/* ---------- Sessie, CSRF, spam ---------- */

function start_sessie(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = (($_SERVER['HTTPS'] ?? '') !== '' && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('ec_sessie');
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 30,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.gc_maxlifetime', (string) (60 * 60 * 24 * 30));
    $pad = ROOT . '/data/sessies';
    if (!is_dir($pad)) @mkdir($pad, 0750, true);
    if (is_dir($pad) && is_writable($pad)) session_save_path($pad);
    session_start();
}

function csrf_token(): string
{
    start_sessie();
    return $_SESSION['csrf'] ??= token(24);
}

function csrf_veld(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    start_sessie();
    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('De pagina is verlopen. Ga terug, vernieuw de pagina en probeer opnieuw.');
    }
}

/** Ondertekend tijdstempel, zodat bots die te snel posten opvallen (zonder cookies). */
function formulier_stempel(): string
{
    $t = (string) time();
    return $t . '.' . substr(hash_hmac('sha256', $t, app_key()), 0, 16);
}

function spam_verdacht(): bool
{
    if (trim((string) ($_POST['website'] ?? '')) !== '') return true; // honeypot
    $parts = explode('.', (string) ($_POST['stempel'] ?? ''));
    if (count($parts) !== 2) return true;
    [$t, $sig] = $parts;
    if (!hash_equals(substr(hash_hmac('sha256', $t, app_key()), 0, 16), $sig)) return true;
    $leeftijd = time() - (int) $t;
    return $leeftijd < 3 || $leeftijd > 60 * 60 * 24;
}

/* ---------- Beheer: wie is ingelogd? ---------- */

function ingelogd_email(): ?string
{
    start_sessie();
    return $_SESSION['beheer_email'] ?? null;
}

function is_admin(?string $email): bool
{
    return $email !== null && in_array(strtolower($email), array_map('strtolower', site('admins')), true);
}

/** Installateurs die deze gebruiker mag zien. */
function beheerbare_installateurs(?string $email): array
{
    if ($email === null) return [];
    if (is_admin($email)) return installateurs();
    return array_filter(installateurs(), fn($i) => in_array(strtolower($email), array_map('strtolower', $i['beheerders']), true));
}

function mag_inloggen(string $email): bool
{
    return is_admin($email) || beheerbare_installateurs($email) !== [];
}

require __DIR__ . '/mail.php';
require __DIR__ . '/inschrijving.php';
require __DIR__ . '/iconen.php';
