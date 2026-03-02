//Logique d'heure par service
document.addEventListener('DOMContentLoaded', function() {
    const selectService = document.querySelector('#service');
    const selectHour    = document.querySelector('#heure');
    const savedHour     = selectHour.dataset.selected || "";

    const planning = {
        'midi': ['12:00','12:15', '12:30', '12:45', '13:00'],
        'soir': ['19:00','19:30', '20:00', '20:30', '21:00']
    }

    function updateHour(valeurService) {
        const defaultOption = new Option('-- heure --', "");
        selectHour.replaceChildren(defaultOption);

        const crenaux       = planning[valeurService] || [];

        crenaux.forEach(heure => {
            const hDisplay = heure.replace(':', 'h');
            const option   = new Option(hDisplay, heure);

            if (savedHour !== "" && savedHour.includes(heure)) {
            option.selected = true;
        }

        selectHour.appendChild(option);
        });
    }

    selectService.addEventListener('change', function() {
        console.log("Service sélectionné :", this.value);
        updateHour(this.value);
    });

    if(selectService.value) {
        updateHour(selectService.value);
    }
});