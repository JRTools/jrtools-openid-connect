# Changelog

Alle relevanten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.
Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/).
Das Projekt folgt [Semantic Versioning](https://semver.org/lang/de/).

## [Unreleased]

## [1.0.0] – 2026-03-20

### Neu
- **Authorization Code Flow mit PKCE (S256)**: Sichere Anmeldung per OpenID Connect
  gemäß RFC 7636 – verhindert Authorization Code Interception.
- **Discovery**: Automatisches Befüllen aller Endpunkte aus der `/.well-known/openid-configuration`.
- **JWT-Signaturprüfung**: RS256-Validierung gegen den JWKS-Endpoint des Providers,
  mit 1-Stunden-Cache und automatischer Key-Rotation bei unbekanntem `kid`.
- **Token-Verschlüsselung** (AES-256-CBC): Access-, Refresh- und ID-Token werden
  optional verschlüsselt in der Datenbank gespeichert. Transparente Migration von
  Klartext-Tokens über `enc:`-Prefix.
- **Session-Management**: Bindet die WordPress-Session an den Token-Ablauf.
  Bei jedem Request wird das Access-Token geprüft und bei Bedarf per Refresh-Token
  erneuert. Bei Misserfolg wird die Session automatisch beendet.
- **Frontchannel-Logout** und **Backchannel-Logout** (REST-Endpoint `POST /wp-json/oidc/v1/backchannel-logout`).
- **Account-Linking**: Bestehende WordPress-Konten können mit einem OIDC-Anbieter
  verknüpft und wieder getrennt werden.
- **Rollen-Mapping**: Werte aus einem konfigurierbaren Claim werden auf WordPress-Rollen
  abgebildet (zeilenbasierte Konfiguration `claim-wert=rolle`).
- **E-Mail-Adresse sperren**: OIDC-verknüpfte Nutzer können ihre E-Mail-Adresse
  nicht selbst ändern, wenn die Option aktiviert ist.
- **Passwort sperren**: OIDC-verknüpfte Nutzer können ihr Passwort nicht selbst
  ändern, wenn die Option aktiviert ist.
- **Profilbild synchronisieren**: Das `picture`-Claim wird als WordPress-Avatar verwendet.
- **Angemeldet bleiben**: Admin-Einstellung steuert, ob das Auth-Cookie dauerhaft
  (`always`) oder nur für die Sitzung (`never`) gesetzt wird.
- **WP-Login-Formular ausblenden**: Zeigt nur den OIDC-Button an;
  mit `?showlogin=1` weiterhin direkt erreichbar.
- **Auto-Login**: Leitet direkt zum OIDC-Provider weiter, wenn die Login-Seite
  aufgerufen wird.
- **JWKS-Cache leeren**: Schaltfläche in den Einstellungen löscht den gecachten
  JWKS-Transient manuell.
- **Login-Log**: Alle Anmeldeversuche (Erfolg und Fehler) werden in einer eigenen
  Datenbanktabelle protokolliert und sind im Adminbereich einsehbar.
- **Übersetzungen**: de_DE, en_US, fr_FR, es_ES, sv_SE.

[Unreleased]: https://github.com/johannesroesch/oidc-client/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/johannesroesch/oidc-client/releases/tag/v1.0.0
