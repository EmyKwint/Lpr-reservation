document.addEventListener('DOMContentLoaded', function() {
  //Setup langue dayjs
  dayjs.locale('fr');
  let currentViewDate = dayjs().locale('fr');
  
  //MODAL
    //modal-core
  const modal      = document.querySelector('#modal');
  const openModal  = document.querySelector('#btnAppointment');
  const closeModal = document.querySelector('#btnClose');
    //Composants modal
  const midiBtn = document.querySelector("#midiBtn");
  const soirBtn = document.querySelector("#soirBtn");
  const midiDiv = document.querySelector("#midiDiv");
  const soirDiv = document.querySelector("#soirDiv");
  //Main
  function renderCalendar() {
    const grid = document.querySelector('#daysGrid');
    const monthDisplay = document.querySelector('#currentMonth');
    
    //Préparation des calculs
    monthDisplay.textContent = currentViewDate.format('MMMM YYYY');
    
    const startOfMonth = currentViewDate.startOf('month');
    const daysInMonth = currentViewDate.daysInMonth();
    
    
    // Trouver le décalage pour le premier jour (Lundi = 0)
    let startDayColumn = startOfMonth.day() - 1;
    if (startDayColumn === -1) startDayColumn = 6; // Ajustement pour Dimanche
    

    //Création du Fragment
    const fragment = document.createDocumentFragment();

    //Générer les cases vides du début
    for (let i = 0; i < startDayColumn; i++) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'day empty';
      fragment.appendChild(emptyDiv);
    }

    // Générer les jours du mois
    for (let day = 1; day <= daysInMonth; day++) {
      const dateObj = currentViewDate.date(day);
      const whatDay = dateObj.format('ddd');
      const dateString = dateObj.format('YYYY-MM-DD');

      const dayElement = document.createElement('div');
      dayElement.className = 'day';
      dayElement.textContent = day;

      // Vérifier si la date est passée
      if(dateObj.isBefore(dayjs(), 'day')) {
        dayElement.classList.add('past');
      //Verifier si jours de fermeture
      } else if (whatDay === 'Mon' || whatDay === 'Sun') {
        dayElement.classList.add('past');
      } else {
        // On attache l'événement directement à l'objet
        dayElement.addEventListener('click', () => selectDate(dateString));
      }

      fragment.appendChild(dayElement);
    }

    // Mise à jour du DOM
    grid.innerHTML = ""; // On vide l'ancien mois
    grid.appendChild(fragment);
  }
  //Fonction pour gestion réservation
  function selectDate(date) {
    console.log("Date sélectionnée :", date);
    const dateInput = document.querySelector('input[name="date"]');
    if(dateInput) dateInput.value = date;
    const displayDate = document.querySelector('#modalDate');
    if(displayDate) displayDate.textContent = dayjs(date).format('DD MMMM YYYY');
    modal.showModal();
  }
  //API REST
  //formData
  const form = document.querySelector('#resForm');

  form.addEventListener('submit', async function(event) {
    event.preventDefault();

    //Récupération du formulaire
    const formData = new FormData(form);
    const data = {
      date: formData.get('date'),
      heure: formData.get('heure'),
      service: formData.get('service'),
      personnes: formData.get('personnes'),
      telephone: formData.get('telephone'),
      mail: formData.get('mail'),
      nom: formData.get('nom'),
      prenom: formData.get('prenom'),
    };
    console.log(data);

    await sendReservation(data);
    })
  //Envois de la reservation
  async function sendReservation(data) {
    try {
        const response = await fetch(LprConfig.rest_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Optionnel : on peut ajouter le Nonce ici si besoin
                'X-WP-Nonce': LprConfig.nonce 
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();

        if(result.status === 'succes') {
            alert("Merci, Réservation faite (sera remplacée par dialog)");
            modal.closest();
        } else {
            alert("Erreur : " + result.message);
        }
    } catch (erreur) {
        console.error("Erreur Rest")
    }
}
  // Navigation Mois
  document.querySelector('#btnPrev').addEventListener('click', () => {
    currentViewDate = currentViewDate.subtract(1, 'month');
    renderCalendar();
  });
  document.querySelector('#btnNext').addEventListener('click', () => {
    currentViewDate = currentViewDate.add(1, 'month');
    renderCalendar();
  });
  // Affichages horaires
  midiBtn.addEventListener('click', () => {
    midiDiv.classList.remove("disabled");
    if(!soirDiv.classList.contains("disabled")) soirDiv.classList.add("disabled");
  });
  soirBtn.addEventListener('click', () => {
    soirDiv.classList.remove("disabled");
    if(!midiDiv.classList.contains("disabled")) midiDiv.classList.add("disabled");
  });
  // Initialisation au chargement
  renderCalendar();
});