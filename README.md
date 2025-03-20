# Emergency Tracking Tool - Einsatzverwaltungs Software für die Freiwillige Feuerwehr

### Eine kleine Software für die Erfassung von Einsatzkräften, zuordnung zu den Gruppen und die verwaltung dieser.

<br/>

> [!WARNING]  
> **Diese Software befindet sich noch in der Entwicklungs- und Erprobungsphase**

<br/>

### Inhaltsverzeichnis

- **[Anleitung zur Installation](#anleitung-zur-installation)**
- **[Fragen und Antworten](#fragen-und-antworten)**
  <br/><br/><br/>

### Über dieses Tool

Das Tool basiert auf dem [Symfony Framework](https://symfony.com) und wurde für die Freiwillige Feuerwehr Söhlde geschrieben.
<br/><br/>


------------------------------------

<br/>

## Anleitung zur Installation

Da die Software auf Symfony basiert, gelten die Systemforraussetzungen und Installationsanweisungen von [Symfony](https://symfony.com/doc/current/setup.html)
<br/>
Mit folgenden CLI Befehlen können dann die Nötigen Pakete installiert und die Datenbank vorbereitet werden:

```
composer install --no-dev --optimize-autoloader

php bin/console doctrine:schema:update --dump-sql

php bin/console doctrine:migrations:migrate

php bin/console cache:clear
```

<br/>


------------------------------------

<br/>

## Fragen und Antworten

<details>
<summary>[Klick] Für wen ist das Tool gedacht?</summary>
<br/>
Geschrieben wurde das Tool für die Freiwillige Feuerwehr Söhlde. Wenn du es auch nutzen möchtes kannst du dies gerne tun, aber ich werde vorraussichtlich keine Änderungsanfragen von außerhalb bearbeiten und auch keinen Support geben.
  
---
</details>

<details>
<summary>[Klick] Kann ich an dem Projekt mitarbeiten?</summary>
<br/>
Im moment ist das Projekt nicht dafür gedacht weiter aufgezogen zu werden. Wenn du möchtest kannst du es aber gerne Kopieren und dein eigenes daraus machen.
  
---

</details>
