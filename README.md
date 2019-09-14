<p align="center"><img src="http://www.dhbw-mannheim.de/fileadmin/templates/default/img/DHBW_d_MA_46mm_4c.svg"></p>

# DHBW Mobile Apps RESTful API

> RESTful API geschreiben in PHP mit Lumen v5.8

Projekt für Vorlesung Mobile Apps (5. & 6. Semester) mit dem Ziel, eine Java App zu entwickeln und Mehtoden der Projektarbeit anzuwenden.
In diesem Fall wird eine Emfpangs- & Meetingapplikation entwickelt, mit der Mitarbeiter deren Meetings verwalten können.

## Verwendete Komponenten
- [Lumen v5.8](https://lumen.laravel.com/docs/5.8)
- [PostgreSQL Datenbank](https://www.postgresql.org/)
- [Laravel/SwitftMail](https://laravel.com/docs/5.8/mail)
- [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)
- [firebase/php-jwt](https://github.com/firebase/php-jwt)

## Aufbau
- Models für Datenbank in `app/Models/*.php`
    
    Datenbank Models sind aufgebaut mit dem Laravel ORM (Eloquent) und sind über "Relationships" miteinander verküpft ([Docs](https://laravel.com/docs/5.8/eloquent))
- Routen mittels Route-Gruppen in `routes/web.php` definiert und verweisen von dort auf die
- Controller in `app/Http/Controllers/*Controller.php`

    In diesen Controllern werden die Datenbankqueries (abfrage, updates, inserts & deletes) über die Models ausgeführt
- Mail Versand bei:
    - Update Meeting und neuem Datum: allen Teilnehmern ein Update geschickt (`app/Mail/MeetingUpdated.php`)
    - Delete Meeting: allen Teilnehmern eine Absage zugeschickt (`app/Mail/MeetingCanceled.php`)
    - Create Bundle: dem User werden die Meeting Daten und eine Auflistung aller Teilnehmer zugeschickt (`app/Mail/MeetingBundleCreated.php`) +
    - Create Visitor: dem neuem Besuch werden die Meeting Informationen zugeschickt (`app/Mail/VisitorCreated.php`)
    - Check In Visitor: dem Veranstalter wird eine Infromation geschickt, dass der Gast eingetroffen ist (`app/Mail/VisitorCheckedIn.php`)
- `app/Http/Middleware/RoleMiddleware.php` zum Checken, ob die Rolle des authentifizierten (über JWT) Users <b>mindestens</b> vorliegt

## Routen
- `auth/login` zum Login mit "email" & "password"
- `auth/register` zum Registrieren mit "name", "email", "password" & "role"
- `auth/forget` zum Wiederherstellen des Passworts mit "email"
- `auth/reset` zum Setzen des neuen Passworts mit "email", "token", "password" & "password_confirmation"

> alle API Routen benötigen den über `auth/login` angeforderten JWT der bei jedem Request als Header übergeben werden muss:

````bash
Authorization {{JSON_WEB_TOKEN}}
````

- Postman Collection mit Routen, Header und Body: `postman_collection.json`

#### Meeting & Besucher Shortcut Route
`POST` --> `/v0/meetings/bundle`

Daten müssen in folgendem Format vorliegen:
````json
{
    "user_id": 1,
    "room_id": 1,
    "date": "2000-01-01 12:00:00",
    "visitor": [
        {
            "name": "Max Mustermann",
            "email": "example@example.com",
            "company_id": 1,
            "check_in": "OPTIONAL",
            "check_out": "OPTIONAL"
        }
    ]
}
````

## Setup
- [Composer](https://getcomposer.org/download/) herunterladen
- `composer install` im Dateipfad ausführen zum installieren der Packages
- Datei `.env.example` kopieren zu `.env` und die fehlenden Felder (APP_KEY, Passwörter) ergänzen
- Lokal ausführen mit: `php -S localhost:8000 -t public`
