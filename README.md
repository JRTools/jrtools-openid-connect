# OIDC Client

WordPress-Plugin für die Anmeldung per OpenID Connect (Authorization Code Flow mit PKCE).

Unterstützt alle gängigen OIDC-Provider: **Keycloak**, **Microsoft Entra ID (Azure AD)**, **Google**, **Okta**, **Auth0**, **easyVerein** und jeden anderen standardkonformen Anbieter.

---

## Inhaltsverzeichnis

- [Für wen ist dieses Plugin?](#für-wen-ist-dieses-plugin)
- [Voraussetzungen](#voraussetzungen)
- [Installation](#installation)
- [Schnellstart](#schnellstart)
- [Einstellungen im Überblick](#einstellungen-im-überblick)
  - [Provider](#provider)
  - [Client](#client)
  - [Benutzerverwaltung](#benutzerverwaltung)
  - [Erweiterte Optionen](#erweiterte-optionen)
  - [Rollen-Mapping](#rollen-mapping)
- [Feature-Dokumentation](#feature-dokumentation)
  - [Authorization Code Flow mit PKCE](#authorization-code-flow-mit-pkce)
  - [Auto-Discovery](#auto-discovery)
  - [Token-Refresh](#token-refresh)
  - [Token-Verschlüsselung](#token-verschlüsselung)
  - [Session-Management](#session-management)
  - [Frontchannel- und Backchannel-Logout](#frontchannel--und-backchannel-logout)
  - [Account-Linking](#account-linking)
  - [Rollen-Mapping](#rollen-mapping-1)
  - [Active-Claim (Konto aktivieren/deaktivieren)](#active-claim)
  - [Profilbild synchronisieren](#profilbild-synchronisieren)
  - [E-Mail und Passwort sperren](#e-mail-und-passwort-sperren)
  - [Login-Formular ausblenden / Auto-Login](#login-formular-ausblenden--auto-login)
  - [Angemeldet bleiben](#angemeldet-bleiben)
  - [Login-Log](#login-log)
  - [Debug-Modus](#debug-modus)
- [Provider-spezifische Konfigurationshinweise](#provider-spezifische-konfigurationshinweise)
  - [Keycloak](#keycloak)
  - [Microsoft Entra ID (Azure AD)](#microsoft-entra-id-azure-ad)
  - [Google](#google)
  - [Okta / Auth0](#okta--auth0)
  - [easyVerein](#easyverein)
- [Konfiguration auf Provider-Seite](#konfiguration-auf-provider-seite)
- [Fehlersuche](#fehlersuche)
  - [Häufige Fehler und Lösungen](#häufige-fehler-und-lösungen)
  - [Debug-Modus aktivieren](#debug-modus-aktivieren)
- [Sicherheitshinweise](#sicherheitshinweise)
- [Für Entwickler](#für-entwickler)
  - [Architektur](#architektur)
  - [Klassen-Übersicht](#klassen-übersicht)
  - [WordPress-Optionen (Datenbankschlüssel)](#wordpress-optionen-datenbankschlüssel)
  - [User-Meta-Keys](#user-meta-keys)
  - [Action- und Filter-Hooks](#action--und-filter-hooks)
  - [REST-API-Endpunkte](#rest-api-endpunkte)
  - [Lokale Entwicklungsumgebung](#lokale-entwicklungsumgebung)
  - [Tests ausführen](#tests-ausführen)
  - [Release erstellen](#release-erstellen)
  - [Übersetzungen](#übersetzungen)
- [Changelogs](#changelog)
- [Lizenz](#lizenz)

---

## Für wen ist dieses Plugin?

| Zielgruppe | Nutzen |
|---|---|
| **Endanwender** | Meldet sich mit einem Klick per SSO an – kein separates WordPress-Passwort nötig |
| **Administratoren** | Zentrale Benutzerverwaltung über den IdP; automatische Rollen-Synchronisation; Zugang via WordPress-Login bleibt als Fallback erhalten |
| **Entwickler** | Erweiterbar über WordPress-Hooks; vollständige PHPUnit-Testsuite; CI/CD über GitHub Actions |
| **Sicherheitsbeauftragte** | PKCE-geschützter Flow; Token-Verschlüsselung (AES-256-CBC); Backchannel-Logout; JTI-Replay-Schutz |

---

## Voraussetzungen

| Anforderung | Minimum |
|---|---|
| PHP | 7.4 oder höher |
| WordPress | 5.9 oder höher |
| PHP-Extension | `openssl` (für JWT-Signaturprüfung und Token-Verschlüsselung) |
| OIDC-Provider | Muss Authorization Code Flow unterstützen |

> **Hinweis:** Die Extension `openssl` ist in der Regel bereits aktiviert. Prüfe mit `phpinfo()`, ob `openssl` in der Liste erscheint.

---

## Installation

### Variante A: ZIP hochladen (empfohlen für Produktivumgebungen)

1. Die neueste `oidc-client-x.y.z.zip` von der [Releases-Seite](https://github.com/johannesroesch/oidc-client/releases) herunterladen.
2. Im WordPress-Admin zu **Plugins → Neu hinzufügen → Plugin hochladen** navigieren.
3. Die ZIP-Datei auswählen und auf **Jetzt installieren** klicken.
4. Plugin aktivieren.

### Variante B: Manuell per FTP/SSH

1. Den Ordner `oidc-client` in das Verzeichnis `wp-content/plugins/` hochladen.
2. Im WordPress-Admin unter **Plugins** aktivieren.

### Variante C: Composer (für Entwickler)

```bash
composer require johannesroesch/oidc-client
```

Nach der Aktivierung legt das Plugin automatisch die Datenbanktabelle `wp_oidc_login_log` an.

---

## Schnellstart

> Diese Anleitung ist für den häufigsten Fall: ein Provider mit Discovery-URL (z.B. Keycloak, Entra ID, Google).

### Schritt 1: Plugin konfigurieren

1. WordPress-Admin → **Einstellungen → OIDC Client**
2. **Discovery URL** eintragen (z.B. `https://keycloak.example.com/realms/myrealm/.well-known/openid-configuration`)
3. Auf **Abrufen** klicken → Endpunkte werden automatisch ausgefüllt
4. **Provider-Name** eingeben (erscheint im Login-Button, z.B. `Keycloak`)
5. **Client ID** und **Client Secret** aus der Provider-Konfiguration eintragen
6. Auf **Einstellungen speichern** klicken

### Schritt 2: Provider konfigurieren

Im OIDC-Provider (z.B. Keycloak) folgende URI als erlaubte **Redirect URI** eintragen:

```
https://deine-wordpress-seite.de/wp-login.php?oidc_callback=1
```

Den genauen Wert findest du auf der Einstellungsseite unter **Konfiguration auf OIDC-Provider-Seite**.

### Schritt 3: Testen

1. WordPress-Admin abmelden
2. Zur Login-Seite navigieren
3. Den Button **Anmelden mit [Provider-Name]** anklicken
4. Nach erfolgreicher Authentifizierung beim Provider sollte die Weiterleitung zurück zu WordPress erfolgen

---

## Einstellungen im Überblick

### Provider

| Einstellung | Beschreibung |
|---|---|
| **Discovery URL** | URL zum `/.well-known/openid-configuration`-Dokument des Providers. Nach Klick auf „Abrufen" werden alle Endpunkte automatisch ausgefüllt. |
| **Provider-Name** | Freitext, der im Login-Button angezeigt wird: „Anmelden mit _Name_". |
| **Issuer** | Wird automatisch aus der Discovery-URL befüllt. Muss mit dem `iss`-Claim im ID-Token übereinstimmen. |
| **Authorization Endpoint** | URL des Authorization-Endpoints. |
| **Token Endpoint** | URL des Token-Endpoints. |
| **Userinfo Endpoint** | URL des Userinfo-Endpoints (optional, wenn alle Claims im ID-Token enthalten sind). |
| **JWKS URI** | URL der JSON Web Key Sets – wird für die JWT-Signaturprüfung benötigt. |
| **PKCE (S256)** | PKCE gemäß RFC 7636 aktivieren (Standard: aktiviert). Deaktivieren nur wenn der Provider kein PKCE unterstützt und `invalid_client`-Fehler auftreten. |

### Client

| Einstellung | Beschreibung |
|---|---|
| **Client ID** | Die Client-ID, wie sie beim Provider registriert ist. |
| **Client Secret** | Das Client-Secret. Wird verschlüsselt gespeichert. |
| **Scopes** | Leerzeichen-getrennte OAuth2-Scopes (Standard: `openid email profile`). |
| **Token-Endpoint Authentifizierung** | `client_secret_post` (Standard, z.B. Keycloak, easyVerein) oder `client_secret_basic` (z.B. Entra ID, Okta). Bei `invalid_client`-Fehlern die andere Methode testen. |

### Benutzerverwaltung

| Einstellung | Beschreibung |
|---|---|
| **Benutzer automatisch anlegen** | Wenn aktiviert, wird beim ersten Login automatisch ein WordPress-Konto erstellt, sofern die E-Mail-Adresse noch nicht bekannt ist. Wenn deaktiviert, müssen Konten vorab manuell angelegt oder verknüpft werden. |
| **Standard-Rolle für neue Benutzer** | WordPress-Rolle, die automatisch erstellten Konten zugewiesen wird (z.B. `subscriber`). |
| **Debug-Modus** | Zeigt bei Fehlern die vollständige Provider-Antwort im Fehlertext an. **Nur temporär zur Fehlersuche aktivieren!** |

### Erweiterte Optionen

| Einstellung | Beschreibung |
|---|---|
| **End-Session Endpoint** | URL des Logout-Endpoints beim Provider. Wird für den Frontchannel-Logout benötigt. Wird bei Discovery automatisch befüllt. |
| **Token-Refresh** | Speichert Access- und Refresh-Token nach dem Login und erneuert das Access-Token automatisch, wenn es abläuft. Voraussetzung für Session-Management und Token-Verschlüsselung. |
| **Active-Claim** | Name eines Claims (z.B. `active` oder `email_verified`), dessen Wert `true` oder `1` sein muss, damit der Login erlaubt wird. Nützlich zum Sperren inaktiver Konten auf Provider-Seite. |
| **Profilbild synchronisieren** | Übernimmt das `picture`-Claim als WordPress-Avatar. |
| **WP-Login-Formular ausblenden** | Blendet das Standard-WordPress-Loginformular aus. Das Formular ist weiterhin über `?showlogin=1` erreichbar (für Admins als Fallback). |
| **Auto-Login** | Leitet nicht angemeldete Besucher der Login-Seite direkt zum OIDC-Provider weiter. |
| **Button-Icon URL** | URL zu einem Bild, das links im Login-Button angezeigt wird (z.B. Provider-Logo). Empfohlene Größe: 20×20 px. |
| **Token-Verschlüsselung** | Speichert Access-, Refresh- und ID-Token verschlüsselt (AES-256-CBC) in der Datenbank. Erfordert PHP-OpenSSL und aktivierten Token-Refresh. |
| **E-Mail sperren** | OIDC-Nutzer können ihre E-Mail-Adresse im WordPress-Profil nicht selbst ändern. |
| **Passwort sperren** | OIDC-Nutzer können ihr Passwort im WordPress-Profil nicht selbst ändern. |
| **Session-Management** | Bindet die WordPress-Session an den Token-Ablauf. Bei jedem Seitenaufruf wird das Access-Token geprüft; ist es abgelaufen, wird ein Refresh versucht. Bei Misserfolg wird die Session beendet. Erfordert Token-Refresh. |
| **Angemeldet bleiben** | Steuert das Auth-Cookie: `Nie` = Cookie gilt nur für die Browser-Session. `Immer` = dauerhaftes Cookie (14 Tage). |
| **JWKS-Cache leeren** | Schaltfläche zum sofortigen Löschen des gecachten JWKS-Transients (z.B. nach Key-Rotation beim Provider). |

### Rollen-Mapping

Das Rollen-Mapping weist OIDC-Claims automatisch WordPress-Rollen zu.

| Einstellung | Beschreibung |
|---|---|
| **Rollen-Claim** | Name des Claims, der die Rollen enthält, z.B. `roles` oder `groups`. |
| **Mapping-Tabelle** | Jeweils ein Claim-Wert (z.B. `editor-group`) und die zugehörige WordPress-Rolle (z.B. `editor`). |

Enthält ein Claim mehrere Werte (Array), werden alle gemappt: der erste als primäre Rolle (`set_role`), weitere als zusätzliche Rollen (`add_role`). Wenn kein Eintrag der Mapping-Tabelle passt, bleibt die bestehende WordPress-Rolle erhalten.

---

## Feature-Dokumentation

### Authorization Code Flow mit PKCE

Das Plugin implementiert den **Authorization Code Flow** gemäß OpenID Connect Core 1.0 mit **PKCE** (Proof Key for Code Exchange, RFC 7636).

**Ablauf:**

```
Browser           WordPress          OIDC-Provider
   |                   |                    |
   |-- Login-Button -->|                    |
   |                   |-- ?code_challenge->|
   |<-- Redirect ------|                    |
   |                                        |
   |--- Anmeldung beim Provider ----------->|
   |<-- Redirect mit ?code ----------------|
   |                                        |
   |-- ?oidc_callback=1 + code ----------->|
   |                   |-- token request -->|
   |                   |<-- tokens ---------|
   |                   |-- userinfo ------->|
   |                   |<-- claims ---------|
   |                   |                    |
   |<-- eingeloggt ----|                    |
```

- **State-Parameter:** CSRF-Schutz; wird als WordPress-Transient für 5 Minuten gespeichert.
- **Nonce:** Replay-Schutz im ID-Token.
- **PKCE:** Verhindert Authorization Code Interception auch bei öffentlichen Clients. Kann deaktiviert werden, wenn der Provider kein PKCE unterstützt.

---

### Auto-Discovery

Wenn der Provider eine **Discovery URL** (`/.well-known/openid-configuration`) bereitstellt, können alle Endpunkte mit einem Klick automatisch befüllt werden:

1. Discovery URL eintragen
2. Auf **Abrufen** klicken
3. Issuer, Authorization Endpoint, Token Endpoint, Userinfo Endpoint, JWKS URI und End-Session Endpoint werden automatisch ausgefüllt
4. PKCE-Checkbox wird automatisch gesetzt, wenn der Provider S256 unterstützt

---

### Token-Refresh

Wenn **Token-Refresh** aktiviert ist, speichert das Plugin nach dem Login:
- Access-Token
- Refresh-Token
- Ablaufzeitpunkt des Access-Tokens
- ID-Token (immer, auch ohne Refresh – für Logout benötigt)

Das Access-Token wird automatisch erneuert, wenn es weniger als 60 Sekunden gültig ist. Schlägt der Refresh fehl (z.B. weil der Refresh-Token abgelaufen ist), wird eine `WP_Error`-Instanz zurückgegeben.

---

### Token-Verschlüsselung

Wenn **Token-Verschlüsselung** aktiviert ist, werden alle gespeicherten Tokens (Access, Refresh, ID) mit **AES-256-CBC** verschlüsselt:

- Schlüsselmaterial: `SHA256(AUTH_KEY . SECURE_AUTH_KEY)` (aus `wp-config.php`)
- IV: zufällig generiert pro Verschlüsselung (16 Bytes)
- Format im Datenbankfeld: `enc:<base64(IV + Ciphertext)>`
- **Transparente Migration:** Vorhandene Klartext-Tokens funktionieren weiterhin – sie werden beim nächsten Schreiben automatisch verschlüsselt.

> **Voraussetzung:** PHP-Extension `openssl` muss aktiv sein. Token-Refresh muss aktiviert sein.

---

### Session-Management

Mit aktiviertem **Session-Management** wird bei **jedem Seitenaufruf** für eingeloggte OIDC-Nutzer geprüft:

1. Ist das Access-Token noch gültig? → weiter
2. Ist das Access-Token abgelaufen → Refresh versuchen
3. Schlägt der Refresh fehl (Refresh-Token abgelaufen oder widerrufen) → Session sofort beenden, Nutzer zur Login-Seite weiterleiten

Dies stellt sicher, dass Benutzer, die auf Provider-Seite deaktiviert oder ausgeloggt wurden, auch in WordPress zeitnah ausgeloggt werden.

> **Voraussetzung:** Token-Refresh muss aktiviert sein.

---

### Frontchannel- und Backchannel-Logout

#### Frontchannel-Logout

Wenn ein OIDC-Nutzer sich aus WordPress ausloggt und ein **End-Session Endpoint** konfiguriert ist:

1. WordPress beendet die lokale Session
2. Browser wird zum End-Session Endpoint des Providers weitergeleitet (`id_token_hint` wird mitgesendet)
3. Provider beendet die Provider-Session
4. Browser wird zurück zur WordPress-Login-Seite weitergeleitet

#### Backchannel-Logout (RFC 7009 / OpenID Connect Backchannel Logout 1.0)

Der Backchannel-Logout ermöglicht es dem Provider, WordPress direkt (Server-zu-Server) über einen Logout zu informieren:

**Endpoint:** `POST /wp-json/oidc-client/v1/backchannel-logout`

**Validierungen:**
- JWT-Signatur (RS256 via JWKS)
- `iss`- und `aud`-Claims
- `iat`-Claim (nicht zu alt, nicht in der Zukunft)
- `nonce` darf **nicht** vorhanden sein
- `events`-Claim muss `http://schemas.openid.net/event/backchannel-logout` enthalten
- `sub` oder `sid` muss vorhanden sein
- **JTI-Replay-Schutz:** Einmal verwendete Logout-Tokens werden 24 Stunden geblockt

Bei erfolgreichem Logout werden alle WordPress-Sessions des Benutzers (`WP_Session_Tokens`) und alle gespeicherten Tokens gelöscht.

---

### Account-Linking

Bestehende WordPress-Konten können mit einem OIDC-Anbieter verknüpft werden:

**Als eingeloggter Benutzer:**
1. WordPress-Admin → Eigenes Profil
2. Abschnitt **OpenID Connect** aufrufen
3. Auf **Mit OIDC-Anbieter verknüpfen** klicken
4. Nach der OIDC-Anmeldung wird das Konto verknüpft

**Verknüpfung aufheben:**
1. WordPress-Admin → Eigenes Profil → **OpenID Connect**
2. Auf **Verknüpfung aufheben** klicken (Bestätigungsdialog)

Nach der Verknüpfung wird der OIDC `sub`-Claim als `_oidc_subject`-User-Meta gespeichert. Künftige Logins über OIDC werden über diese Kennung einem bestehenden Konto zugeordnet – auch wenn sich die E-Mail-Adresse ändert.

**Automatische Verknüpfung:** Wenn ein Benutzer sich per OIDC anmeldet und ein WordPress-Konto mit derselben E-Mail-Adresse existiert (aber noch kein `_oidc_subject` gesetzt ist), wird das Konto automatisch verknüpft.

---

### Rollen-Mapping

Das Rollen-Mapping erlaubt es, OIDC-Claims automatisch auf WordPress-Rollen abzubilden.

**Beispiel-Konfiguration:**

| Rollen-Claim | `groups` |
|---|---|
| `wordpress-admins` | `administrator` |
| `wordpress-editors` | `editor` |
| `wordpress-authors` | `author` |

Wenn der Userinfo-Response `"groups": ["wordpress-editors"]` enthält, wird die WordPress-Rolle auf `editor` gesetzt.

**Mehrere Rollen:** Wenn das Claim mehrere Werte enthält (z.B. `["wordpress-editors", "wordpress-authors"]`), werden alle passenden WordPress-Rollen zugewiesen.

**Kein Mapping:** Wenn kein Mapping-Eintrag für den Claim-Wert gefunden wird, bleibt die bestehende WordPress-Rolle unverändert.

---

### Active-Claim

Der **Active-Claim** ermöglicht es, den Login auf Provider-Seite zu steuern, ohne das WordPress-Konto zu deaktivieren.

**Konfiguration:** Trage im Feld „Active-Claim" den Namen des Claims ein, z.B.:
- `active` – typisch bei Keycloak/easyVerein (Wert muss `true` sein)
- `email_verified` – Login nur wenn E-Mail bestätigt (Wert muss `true` sein)

Wenn der Claim-Wert `false`, `0`, `""` oder `null` ist, wird der Login mit der Meldung „Dein Konto ist deaktiviert" verweigert.

---

### Profilbild synchronisieren

Wenn **Profilbild synchronisieren** aktiviert ist, wird das `picture`-Claim (eine URL zu einem Profilbild) aus dem Userinfo-Response als WordPress-Avatar verwendet.

Das Bild wird als `_oidc_avatar_url`-User-Meta gespeichert und über den `get_avatar_url`-Filter eingebunden. Eigene Gravatar-Einstellungen des Nutzers werden dadurch überschrieben.

---

### E-Mail und Passwort sperren

Wenn aktiviert, können OIDC-verknüpfte Nutzer in ihrem WordPress-Profil keine E-Mail-Adresse und/oder kein Passwort ändern:

- Das E-Mail-Feld wird als readonly dargestellt mit einem Hinweis-Text
- Der Passwort-Abschnitt wird ausgeblendet mit einem Hinweis-Text
- Serverseitig wird eine Änderung auch dann verhindert, wenn jemand das readonly-Attribut umgeht

Davon betroffen sind **nur** Nutzer mit einem verknüpften OIDC-Konto (`_oidc_subject`-Meta).

---

### Login-Formular ausblenden / Auto-Login

**WP-Login-Formular ausblenden:**
- Das Standard-WordPress-Login-Formular (Benutzername/Passwort) wird ausgeblendet
- Nur der OIDC-Login-Button wird angezeigt
- Für Admins als Fallback: `wp-login.php?showlogin=1`

**Auto-Login:**
- Wenn ein nicht angemeldeter Benutzer `wp-login.php` aufruft, wird er sofort zum OIDC-Provider weitergeleitet
- Kein Login-Button wird angezeigt
- Ausnahmen: `?showlogin=1`, `?loggedout=true`, `?oidc_error=...`

> **Wichtig:** Aktiviere Auto-Login erst, wenn der OIDC-Login zuverlässig funktioniert. Sonst ist eine Anmeldung als Administrator nicht mehr möglich (Fallback: `?showlogin=1`).

---

### Angemeldet bleiben

Die Einstellung steuert, welcher Cookie-Typ nach dem OIDC-Login gesetzt wird:

| Option | Verhalten |
|---|---|
| **Nie** | Session-Cookie (wird beim Schließen des Browsers gelöscht) |
| **Immer** | Dauerhaftes Auth-Cookie (14 Tage, Standard-WordPress-Verhalten) |

---

### Login-Log

Unter **Werkzeuge → OIDC Login-Log** werden alle Login-Versuche protokolliert:

| Spalte | Inhalt |
|---|---|
| Zeitstempel | Datum und Uhrzeit des Versuchs |
| Benutzer | WordPress-Login-Name (verlinkt zum Profil) |
| IP-Adresse | IP-Adresse des Clients (aus `REMOTE_ADDR`) |
| Status | ✓ Erfolg / ✗ Fehler |
| Meldung | Fehlermeldung oder „OIDC-Anmeldung erfolgreich" |

Die Log-Tabelle unterstützt Paginierung (25 Einträge pro Seite).

---

### Debug-Modus

Im Debug-Modus werden bei Fehlern zusätzliche Informationen in der Fehlermeldung angezeigt:
- Die vollständige Antwort des Providers
- Die gesendeten Parameter (Client-Secret wird maskiert als `***`)

> **Achtung:** Aktiviere den Debug-Modus nur temporär zur Fehlersuche und deaktiviere ihn danach wieder. Die angezeigte Information enthält möglicherweise sensible Daten.

Unabhängig vom Debug-Modus werden bei Token-Endpoint-Fehlern Details in das WordPress-Error-Log geschrieben (`WP_DEBUG_LOG`).

---

## Provider-spezifische Konfigurationshinweise

### Keycloak

**Discovery URL:**
```
https://<host>/realms/<realm-name>/.well-known/openid-configuration
```

**Empfohlene Einstellungen:**
- Token-Endpoint Authentifizierung: `client_secret_post`
- PKCE: aktiviert

**Rollen aus Keycloak mappen:**
- Claim-Name: `roles` (Realm-Rollen) oder `resource_access.<client-id>.roles` (Client-Rollen)
- In Keycloak muss ein Mapper konfiguriert sein, der die Rollen als Claim ausgibt

**Scopes für Rollen-Claim:**
```
openid email profile roles
```

---

### Microsoft Entra ID (Azure AD)

**Discovery URL:**
```
https://login.microsoftonline.com/<tenant-id>/v2.0/.well-known/openid-configuration
```

**Empfohlene Einstellungen:**
- Token-Endpoint Authentifizierung: `client_secret_basic`
- PKCE: aktiviert

**Rollen mappen:**
- Claim-Name: `roles`
- Rollen müssen in der App-Registrierung als App-Rollen definiert und Benutzern/Gruppen zugewiesen sein

**Scopes:**
```
openid email profile
```

---

### Google

**Discovery URL:**
```
https://accounts.google.com/.well-known/openid-configuration
```

**Empfohlene Einstellungen:**
- Token-Endpoint Authentifizierung: `client_secret_post`
- PKCE: aktiviert (Google unterstützt S256)
- Active-Claim: `email_verified`

**Scopes:**
```
openid email profile
```

> Google unterstützt kein Rollen-Mapping – Rollen müssen in WordPress manuell vergeben werden.

---

### Okta / Auth0

**Okta Discovery URL:**
```
https://<okta-domain>/.well-known/openid-configuration
```

**Auth0 Discovery URL:**
```
https://<auth0-domain>/.well-known/openid-configuration
```

**Empfohlene Einstellungen:**
- Token-Endpoint Authentifizierung: `client_secret_basic`

**Rollen bei Auth0:**
- Auth0 benötigt eine Action/Rule, die Rollen als Custom Claim in den Token einfügt, z.B. `https://example.com/roles`

---

### easyVerein

**Discovery URL:**
```
https://easyverein.com/api/v1/o/.well-known/openid-configuration
```

**Empfohlene Einstellungen:**
- Token-Endpoint Authentifizierung: `client_secret_post`
- PKCE: aktiviert

**Active-Claim für aktive Mitglieder:**
- Claim-Name: `active`

---

## Konfiguration auf Provider-Seite

Auf der Einstellungsseite unter **Konfiguration auf OIDC-Provider-Seite** findest du alle URIs, die du beim Provider eintragen musst:

| Parameter | URI |
|---|---|
| **Redirect URI (Callback URL)** | `https://deine-seite.de/wp-login.php?oidc_callback=1` |
| **Post-logout Redirect URI** | `https://deine-seite.de/wp-login.php` |
| **Backchannel Logout URI** | `https://deine-seite.de/wp-json/oidc-client/v1/backchannel-logout` |
| **Allowed Origin / CORS Origin** | `https://deine-seite.de` |
| **Initiate Login URI** | `https://deine-seite.de/wp-login.php` |

Die exakten Werte werden auf der Einstellungsseite angezeigt und können mit einem Klick kopiert werden.

---

## Fehlersuche

### Häufige Fehler und Lösungen

#### `invalid_client`

**Mögliche Ursachen:**
1. **Falsche Client-ID oder Client-Secret** → Im Feld nachprüfen; kein Leerzeichen am Anfang/Ende
2. **Falsche Authentifizierungsmethode** → `client_secret_post` auf `client_secret_basic` umstellen (oder umgekehrt)
3. **PKCE nicht vom Provider unterstützt** → PKCE-Checkbox deaktivieren

#### `Ungültiger oder abgelaufener State-Parameter`

**Ursache:** Die 5-Minuten-Frist zwischen dem Klick auf den Login-Button und der Rückkehr des Providers wurde überschritten, oder WordPress-Transients werden nicht korrekt gespeichert.

**Lösung:**
- Prüfe, ob `wp_options` schreibbar ist
- Bei Object-Caching: Sicherstellen, dass Transients korrekt gespeichert werden
- Serverzeit prüfen (muss korrekt konfiguriert sein)

#### `ID-Token Issuer stimmt nicht überein`

**Ursache:** Der `iss`-Claim im ID-Token stimmt nicht mit dem konfigurierten Issuer überein.

**Lösung:** Discovery URL erneut abrufen; den angezeigten Issuer-Wert manuell mit dem `iss`-Claim im Token vergleichen (Debug-Modus aktivieren).

#### `Der Provider hat keine E-Mail-Adresse zurückgegeben`

**Ursache:** Der `email`-Scope fehlt oder der Provider gibt E-Mail-Adressen nicht im Standard-Claim zurück.

**Lösung:**
- Scopes um `email` ergänzen
- Prüfen, ob der Provider E-Mails ggf. unter einem anderen Claim-Namen liefert
- Im Profil des Benutzers beim Provider sicherstellen, dass eine E-Mail-Adresse hinterlegt ist

#### `Kein lokales Konto für diese E-Mail-Adresse vorhanden`

**Ursache:** „Benutzer automatisch anlegen" ist deaktiviert, und es existiert noch kein WordPress-Konto mit dieser E-Mail.

**Lösung:**
- „Benutzer automatisch anlegen" aktivieren, **oder**
- Konto in WordPress anlegen und danach über das Profil mit OIDC verknüpfen

#### `Sitzung abgelaufen. Bitte erneut anmelden.`

**Ursache:** Session-Management ist aktiv, das Access-Token ist abgelaufen, und der Refresh-Token ist ebenfalls ungültig oder widerrufen.

**Lösung:** Der Benutzer muss sich erneut anmelden. Dieses Verhalten ist beabsichtigt.

#### Login-Schleife (ständige Weiterleitung)

**Ursache:** Auto-Login ist aktiviert, aber die Anmeldung beim Provider schlägt sofort fehl.

**Lösung:** `wp-login.php?showlogin=1` aufrufen → Auto-Login wird übersprungen → Normale WordPress-Anmeldung als Admin möglich → Einstellungen korrigieren.

---

### Debug-Modus aktivieren

1. WordPress-Admin → **Einstellungen → OIDC Client**
2. **Debug-Modus** aktivieren und speichern
3. Login erneut versuchen
4. Fehlermeldung auf der Login-Seite zeigt nun die vollständige Provider-Antwort

Zusätzlich können in `wp-config.php` folgende Konstanten gesetzt werden:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );  // Log nach wp-content/debug.log
```

Das Plugin schreibt bei Token-Endpoint-Fehlern automatisch Details in das Error-Log.

---

## Sicherheitshinweise

### PKCE aktiviert lassen

PKCE (S256) verhindert Authorization Code Interception. Deaktiviere es nur, wenn der Provider es ausdrücklich nicht unterstützt.

### HTTPS verwenden

Der gesamte OIDC-Flow muss über HTTPS laufen. Das Plugin erzwingt `sslverify: true` bei allen HTTP-Anfragen zum Provider.

### Client-Secret schützen

Das Client-Secret wird in der WordPress-Datenbank gespeichert. Stelle sicher:
- Datenbankzugriff ist auf minimale Berechtigungen beschränkt
- Datenbankbackups sind verschlüsselt
- Token-Verschlüsselung aktivieren (schützt Tokens at rest)

### `AUTH_KEY` und `SECURE_AUTH_KEY` sichern

Die Token-Verschlüsselung verwendet `AUTH_KEY` und `SECURE_AUTH_KEY` aus `wp-config.php` als Schlüsselmaterial. Ändere diese Werte nicht, nachdem die Verschlüsselung aktiviert wurde – bestehende Tokens können dann nicht mehr entschlüsselt werden.

### WP-Login-Formular und Auto-Login

Wenn das WP-Login-Formular ausgeblendet und Auto-Login aktiviert ist, ist der Fallback `?showlogin=1` der einzige Weg für eine direkte WordPress-Anmeldung. Merke dir diesen URL für Notfälle.

### Backchannel-Logout-Endpoint ist öffentlich

Der REST-Endpunkt für Backchannel-Logout ist öffentlich zugänglich (kein WordPress-Auth erforderlich), da er vom OIDC-Provider ohne Benutzerinteraktion aufgerufen wird. Das Plugin validiert den Logout-Token vollständig (Signatur, Claims, JTI-Replay-Schutz).

---

## Für Entwickler

### Architektur

Das Plugin ist in eigenständige PHP-Klassen unterteilt. Jede Klasse registriert ihre eigenen WordPress-Hooks im Konstruktor und wird zentral in `oidc-client.php` instanziiert.

```
oidc-client.php          Einstiegspunkt: Konstanten, require_once, oidc_client_init()
├── class-oidc-jwt-helper.php     Statische JWT/JWKS-Hilfsmethoden (kein State)
├── class-oidc-log.php            Datenbanklog + Admin-Log-Seite
├── class-oidc-tokens.php         Token-Speicherung, Refresh, Verschlüsselung
├── class-oidc-roles.php          Rollen-Mapping-Logik
├── class-oidc-logout.php         Frontchannel- + Backchannel-Logout
├── class-oidc-profile.php        Account-Linking, E-Mail/Passwort-Sperre
├── class-oidc-admin.php          Settings-API, Discovery-AJAX, Cache-AJAX
├── class-oidc-auth.php           Authorization Code Flow, Callback, Session-Check
└── class-oidc-login.php          Login-Button, Fehlermeldung, Auto-Login
```

### Klassen-Übersicht

#### `OIDC_JWT_Helper` (statisch)

| Methode | Beschreibung |
|---|---|
| `base64url_decode($input)` | Base64url-Dekodierung (RFC 4648 §5) |
| `parse_jwt($jwt)` | JWT in `[header, claims, parts]` zerlegen |
| `get_jwks($jwks_uri)` | JWKS abrufen (1 Stunde Transient-Cache) |
| `verify_signature($parts, $header, $jwks_uri)` | RS256-Signatur prüfen |
| `jwk_to_pem($jwk)` | RSA-JWK zu PEM-Public-Key konvertieren |

#### `OIDC_Tokens`

| Methode | Beschreibung |
|---|---|
| `store_tokens($user_id, $tokens)` | Tokens nach Login speichern (mit optionaler Verschlüsselung) |
| `get_id_token($user_id)` | ID-Token lesen (entschlüsselt) |
| `get_valid_access_token($user_id)` | Access-Token liefern, bei Bedarf automatisch erneuern |
| `clear_tokens($user_id)` | Access- und Refresh-Token löschen |
| `clear_all_tokens($user_id)` | Alle Token-Metas löschen (inkl. ID-Token) |

#### `OIDC_Roles`

| Methode | Beschreibung |
|---|---|
| `apply_role_mapping($user_id, $userinfo)` | Rollen-Mapping aus Einstellungen auf User anwenden |

#### `OIDC_Auth`

| Hook | Methode | Beschreibung |
|---|---|---|
| `login_init` | `handle_callback()` | OIDC-Callback verarbeiten |
| `oidc_initiate_login` | `initiate_login($extra_params)` | Redirect zum Provider starten |
| `init` | `check_session_validity()` | Session bei jedem Request prüfen |
| `get_avatar_url` | `filter_avatar_url()` | OIDC-Profilbild einbinden |

---

### WordPress-Optionen (Datenbankschlüssel)

Alle Optionen sind über die WordPress Settings API (`get_option`, `update_option`) zugänglich:

| Option | Typ | Beschreibung |
|---|---|---|
| `oidc_discovery_url` | URL | Discovery-URL des Providers |
| `oidc_provider_name` | String | Name des Providers (für Login-Button) |
| `oidc_issuer` | String | Erwarteter `iss`-Claim |
| `oidc_authorization_endpoint` | URL | Authorization Endpoint |
| `oidc_token_endpoint` | URL | Token Endpoint |
| `oidc_userinfo_endpoint` | URL | Userinfo Endpoint |
| `oidc_jwks_uri` | URL | JWKS URI |
| `oidc_end_session_endpoint` | URL | End-Session Endpoint (für Logout) |
| `oidc_pkce_supported` | `1`/`''` | PKCE aktivieren |
| `oidc_client_id` | String | Client-ID |
| `oidc_client_secret` | String | Client-Secret |
| `oidc_scopes` | String | OAuth2-Scopes (leerzeichen-getrennt) |
| `oidc_token_auth_method` | `client_secret_post`/`client_secret_basic` | Token-Endpoint-Authentifizierung |
| `oidc_debug_mode` | `1`/`''` | Debug-Modus |
| `oidc_create_user` | `1`/`''` | Benutzer automatisch anlegen |
| `oidc_default_role` | String | Standard-Rolle für neue Benutzer |
| `oidc_enable_refresh` | `1`/`''` | Token-Refresh aktivieren |
| `oidc_active_claim` | String | Name des Active-Claims |
| `oidc_sync_avatar` | `1`/`''` | Profilbild synchronisieren |
| `oidc_hide_wp_login` | `1`/`''` | WP-Login-Formular ausblenden |
| `oidc_auto_login` | `1`/`''` | Auto-Login aktivieren |
| `oidc_button_icon_url` | URL | URL des Login-Button-Icons |
| `oidc_token_encryption` | `1`/`''` | Token-Verschlüsselung aktivieren |
| `oidc_lock_email` | `1`/`''` | E-Mail-Änderung sperren |
| `oidc_lock_password` | `1`/`''` | Passwort-Änderung sperren |
| `oidc_session_management` | `1`/`''` | Session-Management aktivieren |
| `oidc_remember_me` | `always`/`never` | Angemeldet-bleiben-Steuerung |
| `oidc_role_claim` | String | Name des Rollen-Claims |
| `oidc_role_mapping` | JSON | Rollen-Mapping als JSON-Objekt |

---

### User-Meta-Keys

| Meta-Key | Typ | Beschreibung |
|---|---|---|
| `_oidc_subject` | String | `sub`-Claim des Providers – eindeutige Kennung |
| `_oidc_id_token` | String | ID-Token (ggf. verschlüsselt mit `enc:`-Prefix) |
| `_oidc_access_token` | String | Access-Token (ggf. verschlüsselt) |
| `_oidc_access_token_expires` | int | Unix-Timestamp des Token-Ablaufs |
| `_oidc_refresh_token` | String | Refresh-Token (ggf. verschlüsselt) |
| `_oidc_avatar_url` | String | URL des Profilbilds vom Provider |

---

### Action- und Filter-Hooks

Das Plugin stellt eigene Hooks bereit und nutzt Standard-WordPress-Hooks:

#### Actions

| Hook | Beschreibung |
|---|---|
| `oidc_initiate_login` | Startet den OIDC-Login-Flow. Kann mit optionalem `$extra_params`-Array aufgerufen werden (z.B. `['prompt' => 'login']`). |
| `do_action('oidc_initiate_login')` | Trigger-Hook zum Starten des OIDC-Flows aus eigenem Code |

**Beispiel: OIDC-Login aus eigenem Code starten**

```php
// Normaler Login
do_action( 'oidc_initiate_login' );

// Login mit erzwungener erneuter Anmeldung beim Provider
do_action( 'oidc_initiate_login', array( 'prompt' => 'login' ) );
```

#### Filter

Das Plugin nutzt den `get_avatar_url`-Filter intern. Es gibt aktuell keine eigenen öffentlichen Filter-Hooks.

---

### REST-API-Endpunkte

| Methode | Pfad | Beschreibung |
|---|---|---|
| `POST` | `/wp-json/oidc-client/v1/backchannel-logout` | Backchannel-Logout-Endpoint (öffentlich, validiert via JWT) |

**Request-Body:**
```
Content-Type: application/x-www-form-urlencoded

logout_token=<signed-jwt>
```

**Response 200:** Logout erfolgreich (oder Benutzer nicht gefunden – idempotent)
**Response 400:** Ungültiger oder fehlender Logout-Token

---

### Lokale Entwicklungsumgebung

**Voraussetzungen:** PHP, Composer, Docker (für wp-env)

```bash
# Abhängigkeiten installieren
make install

# Alle Checks in einem Schritt (install + lint + test)
make ci
```

Das Makefile kennt folgende Targets:

| Target | Befehl | Beschreibung |
|---|---|---|
| `make install` | `composer install` | Alle Dev-Dependencies installieren |
| `make test` | `vendor/bin/phpunit` | Unit-Tests ausführen |
| `make lint` | `vendor/bin/phpcs` | Code-Style prüfen (PHPCS + WPCS) |
| `make fix` | `vendor/bin/phpcbf` | Auto-fixbare Code-Style-Fehler beheben |
| `make build` | `bash bin/build.sh` | Distributions-ZIP erstellen |
| `make ci` | `install + lint + test` | Vollständiger CI-Lauf |
| `make clean` | `rm -rf dist vendor` | Build-Artefakte bereinigen |

---

### Tests ausführen

Das Plugin enthält PHPUnit-Unit-Tests mit [Brain\Monkey](https://brain-wp.github.io/BrainMonkey/) für das Mocking von WordPress-Funktionen.

```bash
# Tests ausführen
vendor/bin/phpunit

# Einzelne Test-Klasse
vendor/bin/phpunit tests/Unit/JwtHelperTest.php

# Einzelner Test
vendor/bin/phpunit --filter test_base64url_decode_standard
```

**Test-Klassen:**

| Datei | Testet | Tests |
|---|---|---|
| `tests/Unit/JwtHelperTest.php` | `OIDC_JWT_Helper` | base64url-Dekodierung, JWT-Parsing, DER-Encoding, JWK→PEM |
| `tests/Unit/TokensTest.php` | `OIDC_Tokens` | encrypt/decrypt-Roundtrip, Legacy-Plaintext, IV-Randomness |
| `tests/Unit/RolesTest.php` | `OIDC_Roles` | Rollen-Mapping, kein Match, Array-Claims, ungültige Rollen |
| `tests/Unit/AuthTest.php` | `OIDC_Auth` | Zufalls-String, Code-Verifier, PKCE-Challenge (S256) |

**Bootstrap:** `tests/bootstrap.php` definiert:
- WordPress-Konstanten (`ABSPATH`, `AUTH_KEY`, `SECURE_AUTH_KEY`, etc.)
- `WP_Error`-Stub
- `WP_User`-Stub mit Call-Tracking für `set_role()`/`add_role()`

---

### Release erstellen

Ein Release wird über GitHub Actions automatisch ausgeführt, wenn ein Git-Tag mit `v` Präfix gepusht wird:

```bash
git tag v1.2.0
git push origin v1.2.0
```

GitHub Actions führt dann automatisch aus:
1. `composer install --no-dev` (keine Dev-Dependencies im ZIP)
2. `bash bin/build.sh` → erstellt `dist/oidc-client-1.2.0.zip`
3. GitHub Release mit dem ZIP als Asset anlegen

Das ZIP enthält nur produktionsrelevante Dateien – alle Dev-Dateien sind in `.distignore` ausgeschlossen.

**Manuell bauen:**

```bash
make build
# Ergebnis: dist/oidc-client-<VERSION>.zip
```

---

### Übersetzungen

Das Plugin nutzt das WordPress i18n-System (`__()`, `_e()`, `esc_html__()`, etc.) mit der Text-Domain `oidc-client`.

Mitgelieferte Übersetzungen:

| Locale | Datei | Sprache |
|---|---|---|
| `de_DE` | `languages/oidc-client-de_DE.po` | Deutsch |
| `en_US` | `languages/oidc-client-en_US.po` | Englisch |
| `fr_FR` | `languages/oidc-client-fr_FR.po` | Französisch |
| `es_ES` | `languages/oidc-client-es_ES.po` | Spanisch |
| `sv_SE` | `languages/oidc-client-sv_SE.po` | Schwedisch |

**Eigene Übersetzung erstellen:**

```bash
# Vorlage aus POT-Datei
cp languages/oidc-client.pot languages/oidc-client-<locale>.po

# Übersetzungen in der .po-Datei eintragen
# Dann .mo-Datei kompilieren:
msgfmt languages/oidc-client-<locale>.po -o languages/oidc-client-<locale>.mo
```

Die `.mo`-Dateien müssen im selben Verzeichnis wie die `.po`-Dateien liegen.

---

### Datenbankschema

Das Plugin legt eine eigene Tabelle an (`{prefix}oidc_login_log`), die beim Aktivieren erstellt wird:

```sql
CREATE TABLE wp_oidc_login_log (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id   BIGINT UNSIGNED NOT NULL DEFAULT 0,
    timestamp DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip        VARCHAR(45)     NOT NULL DEFAULT '',
    success   TINYINT(1)      NOT NULL DEFAULT 0,
    message   TEXT            NOT NULL
);
```

---

## Changelog

Alle Änderungen sind in [CHANGELOG.md](CHANGELOG.md) dokumentiert.

---

## Lizenz

GPL-2.0-or-later – siehe [LICENSE](LICENSE).

Copyright (C) 2026 Johannes Rösch
