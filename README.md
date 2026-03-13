# Familjespel

Webbaserat familjespel med TV som huvudskärm och mobil som controller.

## Fas 1
Den här fasen innehåller:

- PHP-baserat projektskelett
- enkel router
- grundläggande vyer
- config
- databasschema
- grund för vidare utveckling

## Fas 2
Den här fasen innehåller:

- skapa spelrum
- generera spelkod
- host-lobby
- anslutning via spelkod
- namn, ålder och avatar
- player-lobby
- enkel polling för uppdatering av lobbyn

## Struktur

- `public/` – publik webbrot
- `app/` – kärnlogik, controllers, repositories och views
- `config/` – app- och databasinställningar
- `database/` – schema
- `storage/` – loggar och cache senare

## Kommande fas
Nästa steg är:

- starta spelet från host-lobbyn
- första spelbara rundan
- turordning
- visa fråga på TV
- svar från mobil
- rätt/fel
- poäng
