# energie.cafe — beheer en online zetten

## Hoe zit de site in elkaar?

| Map | Wat | Publiek? |
|---|---|---|
| `www/` | De website (document root) | ja |
| `config/installateurs.php` | **Installateurs, locaties en datums** — het bestand dat je aanpast | nee |
| `config/site.php` | Algemene instellingen (meldingsadres, admins, cijfers, referenties) | nee |
| `app/` | De code: pagina's, mails, inschrijflogica | nee |
| `data/` | Databank met inschrijvingen, geheimen, sessies — **staat niet in git** | nee |

Pagina's:
- `energie.cafe/` — startpagina voor de eindklant
- `energie.cafe/techneutjens`, `/bes`, ... — pagina per installateur met inschrijfformulier
- `energie.cafe/beheer/` — login voor installateurs en voor jou (inloggen met een link per mail, geen wachtwoord)
- `energie.cafe/privacy/` — privacyverklaring

## Dagelijks beheer

### Een datum toevoegen of wijzigen
Open `config/installateurs.php`, zoek de installateur en voeg een regel toe onder `sessies`:

```php
['id' => 'bes-2026-11-19', 'datum' => '2026-11-19', 'uur' => '19:00', 'locatie' => 'kantoor', 'plaatsen' => 50],
```

- `id` is uniek en wijzig je **nooit** meer zodra er inschrijvingen zijn.
- `plaatsen` = maximum aantal **personen**. Volzet gebeurt automatisch.
- Inschrijvingen tijdelijk dichtzetten: `'gesloten' => true` toevoegen.
- Voorbije datums verdwijnen vanzelf van de site, laat ze gerust staan.

Daarna committen en pushen naar `main`; de site wordt automatisch bijgewerkt.

### Een nieuwe installateur
Kopieer een blok in `config/installateurs.php`, geef het een nieuwe sleutel (dat wordt het adres, bv. `'elparo'` → energie.cafe/elparo), zet het logo in `www/assets/img/` en vul `meldingen` en `beheerders` in. Met `'status' => 'voorbeeld'` kan je de pagina eerst tonen zonder dat ze op de homepage staat of inschrijvingen ontvangt.

### Wie krijgt welke mail?
| Gebeurtenis | Deelnemer | Installateur (`meldingen`) | info@techneutjens.be |
|---|---|---|---|
| Inschrijving via website | bevestiging in stijl van de installateur, met agenda-link en afmeldlink | melding | kopie van de melding |
| Manuele inschrijving in beheer | optioneel bevestiging | melding | kopie |
| Afmelding | – | melding | kopie |
| "Hou me op de hoogte" | – | melding | kopie |

Het onderwerp begint altijd met de installateur, bv. `[BES] Nieuwe inschrijving ...`, zodat je meteen ziet of het om TechNeutjens gaat.

### Afspraken die in de code zitten
- Eén inschrijving = 1 of 2 personen. Met meer → opnieuw inschrijven.
- Annuleren kan tot de start van de avond (door deelnemer of installateur). Daarna enkel nog door een admin, zodat het aantal bij de start van de avond vastligt (facturatie, art. 5 overeenkomst).
- Manuele inschrijvingen in het beheer mogen boven het maximum gaan.
- Bewaartermijn: inschrijvingen worden 12 maanden na de avond automatisch geanonimiseerd; "hou me op de hoogte" na 2 jaar gewist.

## Online zetten (eenmalig, samen met PDSS)

1. **Hosting** voor `energie.cafe` bij Combell (Linux, PHP 8.1 of hoger met `pdo_sqlite`; standaard aanwezig).
2. **AutoGit** activeren op die hosting (zelfde werkwijze als techneutjens.be, zie `DEPLOY-AUTOGIT.md` in de techneutjens-repo). Vul de remote in `.github/workflows/deploy-combell.yml` in (`COMBELL_REMOTE` en `COMBELL_SSH_HOST`) en zet de secret `COMBELL_SSH_KEY` in GitHub.
3. **Document root** = `www/`. `data/` staat als `shared_folder` in `.autogit.yml` en blijft dus bewaard over deploys heen. Controleer dat de map schrijfbaar is voor PHP.
4. **Mailbox** `inschrijving@energie.cafe` aanmaken (of een SMTP-account) en **SPF + DKIM** voor energie.cafe instellen, anders belanden mails in spam.
5. **`data/geheim.php`** op de server aanmaken (niet in git):

   ```php
   <?php
   return [
       'smtp' => [
           'host' => 'smtp-auth.mailprotect.be',   // Combell SMTP, of wat PDSS opgeeft
           'poort' => 587,
           'gebruiker' => 'inschrijving@energie.cafe',
           'wachtwoord' => '••••••',
       ],
   ];
   ```

   Zonder `smtp` worden de mails via PHP `mail()` van de hosting verstuurd.
6. **HTTPS**: Let's Encrypt-certificaat voor `energie.cafe` en `www.energie.cafe`, en "Force HTTPS" aanzetten in My Combell.
7. **DNS** van energie.cafe naar de nieuwe hosting laten wijzen (PDSS).
8. **Test** na livegang: schrijf jezelf in op een TechNeutjens-datum, controleer de drie mails, log in op `/beheer/`, annuleer de testinschrijving.

## Lokaal testen (zonder PHP te installeren)

```bash
cd tools
npm install
npm start
```

Open daarna http://localhost:8088. Mails worden lokaal niet verstuurd maar als HTML bewaard in `data/mails/` (ook de inloglink voor het beheer). Lokaal staat in `data/geheim.php`:

```php
<?php return ['dev' => true, 'url' => 'http://localhost:8088'];
```
