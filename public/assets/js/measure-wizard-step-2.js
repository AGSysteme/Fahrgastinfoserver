// Maßnahmen-Wizard, Schritt 2: Haltestellen per Drag & Drop wählen, suchen und Linien übernehmen.

const form = document.getElementById('targets-form');
const lineItems = [...document.querySelectorAll('.line-item')];
let dragged = null;

const searchInput = document.getElementById('station-search');
const noResults = document.getElementById('no-results');

function searchTerm() {
    return searchInput.value.trim().toLocaleLowerCase('de-DE');
}

// Haltestellen nach Name filtern; Linien ohne Treffer und leere Bereiche ausblenden
function applyFilter() {
    const term = searchTerm();

    document.querySelectorAll('.station').forEach(el => {
        const name = el.querySelector('.station-name').textContent.toLocaleLowerCase('de-DE');
        el.hidden = term !== '' && !name.includes(term);
    });

    lineItems.forEach(item => {
        const hits = item.querySelectorAll('.station:not([hidden])').length;
        item.hidden = term !== '' && hits === 0;
        if (term !== '' && hits > 0) {
            // Treffer sind nur sichtbar, wenn die Linie aufgeklappt ist
            item.querySelector('.line-panel').classList.remove('d-none');
        }
    });

    const otherHits = document.querySelectorAll('#other-stations .station:not([hidden])').length;
    ['other-title', 'other-hint', 'other-stations'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.hidden = term !== '' && otherHits === 0;
        }
    });

    const totalHits = document.querySelectorAll('.station:not([hidden])').length;
    noResults.classList.toggle('d-none', term === '' || totalHits > 0);
}

// Eine Haltestelle kann in mehreren Linien vorkommen: alle Vorkommen gemeinsam verschieben
function moveStation(id, zone) {
    document.querySelectorAll(`.station[data-station-id="${id}"]`).forEach(el => {
        const target = el.closest('.picker').querySelector(`[data-zone="${zone}"]`);
        if (target && el.parentNode !== target) {
            target.appendChild(el);
        }
    });
}

function selectedIds() {
    const ids = new Set();
    document.querySelectorAll('[data-zone="selected"] .station').forEach(el => ids.add(el.dataset.stationId));
    return ids;
}

function update() {
    const selected = selectedIds();

    lineItems.forEach(item => {
        const box = item.querySelector('.line-toggle');
        const ids = JSON.parse(box.dataset.stations).map(String);
        const count = ids.filter(id => selected.has(id)).length;

        box.checked = ids.length > 0 && count === ids.length;
        box.indeterminate = count > 0 && count < ids.length;
        item.querySelector('.line-count').textContent =
            ids.length ? `${count} von ${ids.length}` : 'keine Haltestellen';

        // Linie ist aufgeklappt, sobald mindestens eine Haltestelle gewählt ist
        item.querySelector('.line-panel').classList.toggle('d-none', count === 0 && searchTerm() === '');
        item.classList.toggle('open', count > 0);
    });

    // Platzhalter in leeren Spalten anzeigen
    document.querySelectorAll('[data-zone]').forEach(zone => {
        zone.querySelector('.drop-zone-empty').classList.toggle('d-none', zone.querySelector('.station') !== null);
    });

    document.getElementById('count').textContent = `(${selected.size} ausgewählt)`;

    applyFilter();
}

// Linie auswählen: alle Haltestellen übernehmen bzw. abwählen (klappt die Linie auf bzw. zu)
document.querySelectorAll('.line-toggle').forEach(box => {
    box.addEventListener('change', () => {
        const ids = JSON.parse(box.dataset.stations).map(String);
        ids.forEach(id => moveStation(id, box.checked ? 'selected' : 'available'));
        update();
    });
});

// Button „Alle abwählen“ je Bereich (wirkt nur auf sichtbare Haltestellen)
document.querySelectorAll('[data-picker-action]').forEach(button => {
    button.addEventListener('click', () => {
        const zone = button.dataset.pickerAction === 'select' ? 'selected' : 'available';
        const ids = [...button.closest('.picker').querySelectorAll('.station:not([hidden])')].map(el => el.dataset.stationId);
        ids.forEach(id => moveStation(id, zone));
        update();
    });
});

// Drag & Drop
document.addEventListener('dragstart', event => {
    const station = event.target.closest('.station');
    if (!station) {
        return;
    }
    dragged = station.dataset.stationId;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', dragged);
});

document.addEventListener('dragend', () => {
    dragged = null;
});

document.querySelectorAll('[data-zone]').forEach(zone => {
    zone.addEventListener('dragover', event => {
        event.preventDefault();
    });

    zone.addEventListener('drop', event => {
        event.preventDefault();
        if (dragged !== null) {
            moveStation(dragged, zone.dataset.zone);
            update();
        }
    });
});

// Gewählte Haltestellen als eindeutige Werte absenden
form.addEventListener('submit', () => {
    form.querySelectorAll('input.generated').forEach(e => e.remove());
    selectedIds().forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'stations[]';
        input.value = id;
        input.className = 'generated';
        form.appendChild(input);
    });
});

// Suche: Enter darf das Formular nicht absenden
searchInput.addEventListener('input', update);
searchInput.addEventListener('keydown', event => {
    if (event.key === 'Enter') {
        event.preventDefault();
    }
});

update();
