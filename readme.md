<p align="center"><img src="http://www.dhbw-mannheim.de/fileadmin/templates/default/img/DHBW_d_MA_46mm_4c.svg"></p>

# DHBW Mobile Apps RESTful API

> RESTful API geschreiben in PHP mit Lumen v5.8

Projekt für Vorlesung Mobile Apps (5. & 6. Semester) mit dem Ziel, eine Java App zu entwickeln und Mehtoden der Projektarbeit anzuwenden.
In diesem Fall wird eine Emfpangs- & Meetingapplikation entwickelt, mit der Mitarbeiter deren Meetings verwalten können.

## Verwendete Komponenten
- [Lumen v5.8](https://lumen.laravel.com/docs/5.8)
- PostgreSQL Datenbnak
- [Laravel/SwitftMail](https://laravel.com/docs/5.8/mail) ist installiert und registriert

## Aufbau
- Models für Datenbank in `app/Models/*.php`
    
    Datenbank Models sind aufgebaut mit dem Laravel ORM (Eloquent) und sind über "Relationships" miteinander verküpft ([Docs](https://laravel.com/docs/5.8/eloquent))
- Routen mittels Route-Gruppen in `routes/web.php` definiert und verweisen von dort auf die
- Controller in `app/Http/Controllers/*Controller.php`

    In diesen Controllern werden die Datenbankqueries (abfrage, updates, inserts & deletes) über die Models ausgeführt
    

## Setup
- [Composer](https://getcomposer.org/download/) herunterladen
- `composer install` im Dateipfad ausführen zum installieren der Packages
- Datei `.env.example` kopieren zu `.env` und die fehlenden Felder (APP_KEY, Passwörter) ergänzen
- Lokal ausführen mit: `php -S localhost:8000 -t public`
