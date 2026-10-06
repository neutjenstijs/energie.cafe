<?php
declare(strict_types=1);

/*
 * Mails versturen.
 *  - Staat er SMTP in data/geheim.php, dan via SMTP (aanbevolen: beste aflevering).
 *  - Anders via PHP mail() van de hosting.
 *  - In ontwikkeling (dev) worden mails als .html in data/mails/ bewaard.
 */

function verstuur_mail(array|string $aan, string $onderwerp, string $html, array $opties = []): bool
{
    $aan = array_values(array_unique(array_filter(array_map('trim', (array) $aan))));
    if ($aan === []) return true;

    $van = site('afzender_email');
    $vanNaam = $opties['van_naam'] ?? site('afzender_naam');
    $antwoord = $opties['antwoord'] ?? null;
    $tekst = $opties['tekst'] ?? html_naar_tekst($html);

    if (is_dev()) {
        $dir = ROOT . '/data/mails';
        if (!is_dir($dir)) mkdir($dir, 0750, true);
        $naam = date('Ymd-His') . '-' . substr(md5($onderwerp . implode(',', $aan) . microtime()), 0, 6) . '.html';
        $kop = '<pre style="background:#eee;padding:8px;font:12px monospace;margin:0">Aan: ' . e(implode(', ', $aan))
            . "\nVan: " . e("$vanNaam <$van>") . "\nAntwoord aan: " . e((string) $antwoord) . "\nOnderwerp: " . e($onderwerp) . '</pre>';
        file_put_contents($dir . '/' . $naam, $kop . $html);
        return true;
    }

    $grens = 'ec-' . bin2hex(random_bytes(8));
    $headers = [
        'Date' => date(DATE_RFC2822),
        'From' => mime_naam($vanNaam) . " <$van>",
        'To' => implode(', ', $aan),
        'Subject' => mime_kop($onderwerp),
        'Message-ID' => '<' . bin2hex(random_bytes(12)) . '@' . substr(strrchr($van, '@'), 1) . '>',
        'MIME-Version' => '1.0',
        'Content-Type' => "multipart/alternative; boundary=\"$grens\"",
    ];
    if ($antwoord) $headers['Reply-To'] = $antwoord;

    $body = "--$grens\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($tekst))
        . "--$grens\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($html))
        . "--$grens--\r\n";

    $smtp = geheim('smtp');
    try {
        if ($smtp) {
            return smtp_verstuur($smtp, $van, $aan, $headers, $body);
        }
        $extra = $headers;
        unset($extra['To'], $extra['Subject']);
        $kopregels = '';
        foreach ($extra as $k => $v) $kopregels .= "$k: $v\r\n";
        return mail(implode(', ', $aan), $headers['Subject'], $body, rtrim($kopregels), '-f' . $van);
    } catch (Throwable $ex) {
        log_fout('mail', $ex->getMessage() . ' | ' . $onderwerp);
        return false;
    }
}

function mime_kop(string $s): string
{
    return preg_match('/[^\x20-\x7E]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
}

function mime_naam(string $s): string
{
    return preg_match('/[^\x20-\x7E]/', $s) ? mime_kop($s) : '"' . addcslashes($s, '"\\') . '"';
}

function html_naar_tekst(string $html): string
{
    $t = preg_replace('#<(br|/p|/tr|/h[1-6]|/li|/div)[^>]*>#i', "\n", $html);
    $t = preg_replace('#<a [^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', '$2 ($1)', (string) $t);
    $t = html_entity_decode(strip_tags((string) $t), ENT_QUOTES, 'UTF-8');
    $t = preg_replace("/[ \t]+/", ' ', $t);
    return trim((string) preg_replace("/\n\s*\n\s*\n+/", "\n\n", (string) $t));
}

function log_fout(string $soort, string $bericht): void
{
    $dir = ROOT . '/data';
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
    @file_put_contents($dir . '/fouten.log', date('c') . " [$soort] $bericht\n", FILE_APPEND);
}

/**
 * Minimale SMTP-client (SSL op 465 of STARTTLS op 587, AUTH LOGIN).
 * $cfg = ['host' => ..., 'poort' => 587, 'gebruiker' => ..., 'wachtwoord' => ...]
 */
function smtp_verstuur(array $cfg, string $van, array $aan, array $headers, string $body): bool
{
    $poort = (int) ($cfg['poort'] ?? 587);
    $host = $cfg['host'];
    $prefix = $poort === 465 ? 'ssl://' : 'tcp://';
    $fp = stream_socket_client($prefix . $host . ':' . $poort, $errno, $errstr, 15);
    if (!$fp) throw new RuntimeException("SMTP verbinding mislukt: $errstr");
    stream_set_timeout($fp, 15);

    $lees = function () use ($fp): string {
        $data = '';
        while (($regel = fgets($fp, 515)) !== false) {
            $data .= $regel;
            if (strlen($regel) < 4 || $regel[3] === ' ') break;
        }
        return $data;
    };
    $cmd = function (string $c, array $ok) use ($fp, $lees): string {
        fwrite($fp, $c . "\r\n");
        $r = $lees();
        if (!in_array((int) substr($r, 0, 3), $ok, true)) {
            throw new RuntimeException('SMTP: ' . trim(explode(' ', $c)[0]) . ' -> ' . trim($r));
        }
        return $r;
    };

    $lees();
    $ehlo = 'EHLO ' . (parse_url(site('url'), PHP_URL_HOST) ?: 'localhost');
    $cmd($ehlo, [250]);
    if ($poort !== 465) {
        $cmd('STARTTLS', [220]);
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
            throw new RuntimeException('SMTP: STARTTLS mislukt');
        }
        $cmd($ehlo, [250]);
    }
    $cmd('AUTH LOGIN', [334]);
    $cmd(base64_encode($cfg['gebruiker']), [334]);
    $cmd(base64_encode($cfg['wachtwoord']), [235]);
    $cmd("MAIL FROM:<$van>", [250]);
    foreach ($aan as $a) $cmd("RCPT TO:<$a>", [250, 251]);
    $cmd('DATA', [354]);
    $kop = '';
    foreach ($headers as $k => $v) $kop .= "$k: $v\r\n";
    $data = $kop . "\r\n" . $body;
    $data = preg_replace('/^\./m', '..', $data); // dot-stuffing
    $cmd($data . "\r\n.", [250]);
    $cmd('QUIT', [221]);
    fclose($fp);
    return true;
}

/* ---------- De concrete mails ---------- */

function mail_layout(array $inst, string $titel, string $inhoud): string
{
    return view('../mails/layout', ['inst' => $inst, 'titel' => $titel, 'inhoud' => $inhoud]);
}

/** Bevestiging naar de deelnemer, in de stijl van de installateur. */
function mail_bevestiging(array $ins, array $sessie, array $inst): bool
{
    $html = mail_layout($inst, 'Je bent ingeschreven', view('../mails/bevestiging', compact('ins', 'sessie', 'inst')));
    return verstuur_mail(
        $ins['email'],
        'Bevestiging: Energiecafé bij ' . $inst['naam'] . ' op ' . datum_lang($sessie['start'], false),
        $html,
        ['antwoord' => $inst['email'], 'van_naam' => 'Energiecafé · ' . $inst['naam']]
    );
}

/** Melding naar de installateur + centrale kopie. */
function mail_melding(array $ins, array $sessie, array $inst, array $bez): bool
{
    $bezet = $bez[$sessie['id']]['personen'] ?? 0;
    $onderwerp = sprintf(
        '[%s] Nieuwe inschrijving %s – %s %s (%d pers.) – %d/%d plaatsen',
        $inst['naam'], datum_kort($sessie['start']), $ins['voornaam'], $ins['naam'], $ins['personen'], $bezet, $sessie['plaatsen']
    );
    $html = mail_layout($inst, 'Nieuwe inschrijving', view('../mails/melding', compact('ins', 'sessie', 'inst', 'bezet')));
    return verstuur_mail(
        array_merge($inst['meldingen'], [site('centrale_melding')]),
        $onderwerp,
        $html,
        ['antwoord' => $ins['email']]
    );
}

function mail_annulering_melding(array $ins, array $sessie, array $inst, string $door): bool
{
    $onderwerp = sprintf('[%s] Afgemeld %s – %s %s (%d pers.)', $inst['naam'], datum_kort($sessie['start']), $ins['voornaam'], $ins['naam'], $ins['personen']);
    $html = mail_layout($inst, 'Afmelding', '<p style="margin:0 0 12px">' . e($ins['voornaam'] . ' ' . $ins['naam']) . ' (' . (int) $ins['personen'] . ' pers.) is afgemeld voor het Energiecafé van ' . e(datum_lang($sessie['start'])) . '.</p><p style="margin:0;color:#666">Afgemeld door: ' . e($door) . '</p>');
    return verstuur_mail(array_merge($inst['meldingen'], [site('centrale_melding')]), $onderwerp, $html);
}

function mail_update_melding(array $upd, array $inst): bool
{
    $onderwerp = sprintf('[%s] Nieuwe interesse: %s wil op de hoogte blijven', $inst['naam'], $upd['voornaam']);
    $html = mail_layout($inst, 'Op de hoogte houden', '<p style="margin:0 0 12px"><strong>' . e($upd['voornaam']) . '</strong> (' . e($upd['email']) . ($upd['postcode'] ? ', ' . e($upd['postcode']) : '') . ') wil een bericht krijgen zodra er nieuwe datums zijn voor het Energiecafé bij ' . e($inst['naam']) . '.</p><p style="margin:0;color:#666">Je vindt alle aanvragen terug in het beheer: <a href="' . e(url('beheer/')) . '">' . e(url('beheer/')) . '</a></p>');
    return verstuur_mail(array_merge($inst['meldingen'], [site('centrale_melding')]), $onderwerp, $html, ['antwoord' => $upd['email']]);
}

function mail_login(string $email, string $link): bool
{
    $inst = ['naam' => 'Energiecafé', 'kleur' => '#DBAA49', 'logo' => null];
    $html = mail_layout($inst, 'Inloggen op energie.cafe', '<p style="margin:0 0 16px">Klik op de knop om in te loggen. De link is 30 minuten geldig en werkt één keer.</p><p style="margin:0 0 16px"><a href="' . e($link) . '" style="display:inline-block;background:#111;color:#fff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:600">Inloggen</a></p><p style="margin:0;color:#666;font-size:13px">Heb je dit niet aangevraagd? Dan mag je deze mail negeren.</p>');
    return verstuur_mail($email, 'Je inloglink voor energie.cafe', $html);
}
