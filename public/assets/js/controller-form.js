// Controller-Formular: nur die Felder der gewählten Anbindung anzeigen (LAN oder RS485).

(function () {
    const radios = document.querySelectorAll('input[name="transport"]');
    const sections = document.querySelectorAll('[data-transport]');

    function update() {
        const checked = document.querySelector('input[name="transport"]:checked');
        const value = checked ? checked.value : 'lan';

        sections.forEach(section => {
            section.classList.toggle('d-none', section.dataset.transport !== value);
        });
    }

    radios.forEach(radio => radio.addEventListener('change', update));
    update();
})();
