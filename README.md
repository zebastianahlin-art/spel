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

## Fas 3
Den här fasen innehåller:

- starta spel från host-lobby
- första spelbara turordning
- TV-vy för aktiv fråga
- mobilvy för aktiv spelare
- svarsinlämning
- rättning
- enkel poängsättning
- automatisk växling till nästa spelare

## Viktigt i fas 3
För att hålla spelet stabilt använder fas 3 bara:

- godkända frågor i databasen
- frågetypen `multiple_choice`

## Kommande fas
Nästa steg är:

- resultatvisning snyggare
- tärning
- förflyttning på spelplan
- Sverigekarta
- positioner
- specialrutor