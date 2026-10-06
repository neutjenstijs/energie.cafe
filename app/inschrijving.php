<?php
declare(strict_types=1);

/**
 * Valideert en bewaart een inschrijving. Gebruikt door het publieke formulier
 * en door het beheer (manuele invoer).
 *
 * Geeft ['ok' => true, 'inschrijving' => [...], 'sessie' => [...]]
 * of ['ok' => false, 'fouten' => [veld => boodschap]] terug.
 */
function verwerk_inschrijving(array $in, array $inst, string $bron = 'website', string $door = ''): array
{
    $v = [
        'sessie' => trim((string) ($in['sessie'] ?? '')),
        'voornaam' => trim((string) ($in['voornaam'] ?? '')),
        'naam' => trim((string) ($in['naam'] ?? '')),
        'email' => strtolower(trim((string) ($in['email'] ?? ''))),
        'telefoon' => trim((string) ($in['telefoon'] ?? '')),
        'postcode' => trim((string) ($in['postcode'] ?? '')),
        'personen' => (int) ($in['personen'] ?? 0),
        'vraag' => trim((string) ($in['vraag'] ?? '')),
        'contact_ok' => !empty($in['contact_ok']) ? 1 : 0,
    ];
    $manueel = $bron === 'manueel';
    $fouten = [];

    $sessie = sessie($v['sessie']);
    if (!$sessie || $sessie['installateur'] !== $inst['slug']) {
        $fouten['sessie'] = 'Kies een datum.';
    } elseif (sessie_voorbij($sessie)) {
        $fouten['sessie'] = 'Deze avond is al voorbij.';
    } elseif (!empty($sessie['gesloten']) && !$manueel) {
        $fouten['sessie'] = 'De inschrijvingen voor deze avond zijn gesloten.';
    }
    if ($v['voornaam'] === '' || mb_strlen($v['voornaam']) > 60) $fouten['voornaam'] = 'Vul je voornaam in.';
    if ($v['naam'] === '' || mb_strlen($v['naam']) > 80) $fouten['naam'] = 'Vul je achternaam in.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($v['email']) > 160) $fouten['email'] = 'Vul een geldig e-mailadres in.';
    $cijfers = preg_replace('/\D+/', '', $v['telefoon']);
    if (strlen($cijfers) < 9 || strlen($cijfers) > 15) $fouten['telefoon'] = 'Vul een geldig telefoonnummer in.';
    if (!preg_match('/^\d{4}$/', $v['postcode']) && !preg_match('/^[A-Za-z0-9 -]{4,8}$/', $v['postcode'])) $fouten['postcode'] = 'Vul je postcode in.';
    if (!in_array($v['personen'], [1, 2], true)) $fouten['personen'] = 'Kies 1 of 2 personen.';
    if (mb_strlen($v['vraag']) > 1500) $fouten['vraag'] = 'Je vraag is te lang (max. 1500 tekens).';

    if ($fouten) return ['ok' => false, 'fouten' => $fouten, 'waarden' => $v];

    $pdo = db();
    $pdo->exec('BEGIN IMMEDIATE'); // voorkomt dat twee gelijktijdige inschrijvingen samen over de limiet gaan
    try {
        $st = $pdo->prepare('SELECT COALESCE(SUM(personen),0) FROM inschrijvingen WHERE sessie = ? AND geannuleerd IS NULL');
        $st->execute([$sessie['id']]);
        $bezet = (int) $st->fetchColumn();
        $vrij = (int) $sessie['plaatsen'] - $bezet;
        if (!$manueel && $v['personen'] > $vrij) {
            $pdo->exec('ROLLBACK');
            $msg = $vrij <= 0
                ? 'Deze avond is intussen volzet. Kies een andere datum of laat je e-mailadres achter voor nieuwe datums.'
                : 'Er is nog maar 1 plaats vrij op deze avond. Schrijf je in voor 1 persoon of kies een andere datum.';
            return ['ok' => false, 'fouten' => ['sessie' => $msg], 'waarden' => $v];
        }
        $v['installateur'] = $inst['slug'];
        $v['bron'] = $bron;
        $v['ingevoerd_door'] = $door;
        $v['token'] = token(18);
        $v['aangemaakt'] = nu();
        $pdo->prepare('INSERT INTO inschrijvingen (installateur, sessie, voornaam, naam, email, telefoon, postcode, personen, vraag, contact_ok, bron, ingevoerd_door, token, aangemaakt)
                       VALUES (:installateur, :sessie, :voornaam, :naam, :email, :telefoon, :postcode, :personen, :vraag, :contact_ok, :bron, :ingevoerd_door, :token, :aangemaakt)')
            ->execute($v);
        $v['id'] = (int) $pdo->lastInsertId();
        $pdo->exec('COMMIT');
    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) $pdo->exec('ROLLBACK');
        throw $ex;
    }

    return ['ok' => true, 'inschrijving' => $v, 'sessie' => $sessie];
}

/** Na een geslaagde inschrijving: mails versturen. */
function na_inschrijving(array $ins, array $sessie, array $inst, bool $bevestig_klant = true): void
{
    $bez = bezetting();
    if ($bevestig_klant) mail_bevestiging($ins, $sessie, $inst);
    mail_melding($ins, $sessie, $inst, $bez);
}

/** Annuleert een inschrijving. Na de start van de avond enkel nog door een admin. */
function annuleer_inschrijving(array $ins, string $door, bool $admin = false): bool
{
    if ($ins['geannuleerd']) return true;
    $sessie = sessie($ins['sessie']);
    if ($sessie && sessie_voorbij($sessie) && !$admin) return false;
    db()->prepare('UPDATE inschrijvingen SET geannuleerd = ?, geannuleerd_door = ? WHERE id = ?')->execute([nu(), $door, $ins['id']]);
    if ($sessie && ($inst = installateur($sessie['installateur']))) {
        mail_annulering_melding($ins, $sessie, $inst, $door);
    }
    return true;
}

function vind_inschrijving_token(string $token): ?array
{
    $st = db()->prepare('SELECT * FROM inschrijvingen WHERE token = ?');
    $st->execute([$token]);
    return $st->fetch() ?: null;
}

/** "Hou me op de hoogte" voor een installateur. */
function verwerk_update(array $in, array $inst): array
{
    $v = [
        'voornaam' => trim((string) ($in['voornaam'] ?? '')),
        'email' => strtolower(trim((string) ($in['email'] ?? ''))),
        'postcode' => trim((string) ($in['postcode'] ?? '')),
    ];
    $fouten = [];
    if ($v['voornaam'] === '' || mb_strlen($v['voornaam']) > 60) $fouten['voornaam'] = 'Vul je voornaam in.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $fouten['email'] = 'Vul een geldig e-mailadres in.';
    if ($v['postcode'] !== '' && !preg_match('/^[A-Za-z0-9 -]{4,8}$/', $v['postcode'])) $fouten['postcode'] = 'Vul een geldige postcode in.';
    if ($fouten) return ['ok' => false, 'fouten' => $fouten, 'waarden' => $v];

    $st = db()->prepare('SELECT * FROM updates WHERE installateur = ? AND email = ?');
    $st->execute([$inst['slug'], $v['email']]);
    $bestaand = $st->fetch();
    if ($bestaand) {
        db()->prepare('UPDATE updates SET voornaam = ?, postcode = ?, uitgeschreven = NULL WHERE id = ?')->execute([$v['voornaam'], $v['postcode'], $bestaand['id']]);
        return ['ok' => true, 'update' => array_merge($bestaand, $v), 'nieuw' => false];
    }
    $v['installateur'] = $inst['slug'];
    $v['token'] = token(18);
    $v['aangemaakt'] = nu();
    db()->prepare('INSERT INTO updates (installateur, voornaam, email, postcode, token, aangemaakt) VALUES (:installateur, :voornaam, :email, :postcode, :token, :aangemaakt)')->execute($v);
    mail_update_melding($v, $inst);
    return ['ok' => true, 'update' => $v, 'nieuw' => true];
}
