    <div class="calendar">
        <div class="calendar__header">
            <button id="btnPrev">Précédent</button>
            <h2 id="currentMonth"></h2>
            <?php
                if (isset($_SESSION['status'])) echo "<p>" . $_SESSION['status'] . "</p>";
            ?>
            <button id="btnNext">Suivant</button>        
        </div>
        <div class="weekdays">
            <div>Lun</div><div>Mar</div><div>Mer</div><div>Jeu</div><div>Ven</div><div>Sam</div><div>Dim</div>
        </div>
        <div id="daysGrid" class="days-grid"></div>
        <button id="btnAppointment">Choisir</button>
    </div>

    <dialog class="modalRes" id="modal">
        <span id="modalDate"></span>
        <form method="post" action="" id="resForm">

            <?php wp_nonce_field('lpr_user_reservation_nonce', 'lpr_nonce'); ?>

            <div class="form__select-service">
                <input type="hidden" name="date" value="">
                <label for="service">Choisissez votre service : </label>
                 <input id="midiBtn" type="radio" name="service" value="midi"><br>
                  <label for="midiBtn">Midi</label>
                 <input id="soirBtn" type="radio" name="service" value="soir"><br>
                  <label for="soirBtn">Soir</label>
                <div id="midiDiv" class="hour-lunch disabled">
                    <input type="radio" id="12h00" name="heure" value="12h00" checked>
                     <label for="12h00">12h00</label>
                    <input type="radio" id="12h15" name="heure" value="12h15">
                     <label for="12h15">12h15</label>
                    <input type="radio" id="12h30" name="heure" value="12h30">
                     <label for="12h30">12h30</label>
                    <input type="radio" id="12h45" name="heure" value="12h45">
                     <label for="12h45">12h45</label>
                    <input type="radio" id="13h00" name="heure" value="13h00">
                     <label for="13h00">13h00</label>
                </div>
                 <div id="soirDiv" class="hour-night disabled">
                     <input type="radio" id="19h00" name="heure" value="19h00" checked>
                     <label for="19h00">19h00</label>
                    <input type="radio" id="19h30" name="heure" value="19h30">
                     <label for="19h30">19h30</label>
                    <input type="radio" id="20h00" name="heure" value="20h00">
                     <label for="20h00">20h00</label>
                    <input type="radio" id="20h30" name="heure" value="20h30">
                     <label for="20h30">20h30</label>
                    <input type="radio" id="21h00" name="heure" value="21h00">
                     <label for="21h00">21h00</label>
                </div>
            </div>
            <label for="number">Nombre de personnes : </label>
             <input type="text" name="personnes"><br>
            <label for="name">Nom : </label>
             <input type="text" name="nom">
            <label for="name">Prénom : </label>
             <input type="text" name="prenom"><br>
            <label for="mail">Email : </label>
             <input type="text" name="mail" id="mail">
            <label for="phone">Téléphone : </label>
             <input type="text" name="telephone"><br>
            <span>Nous vous enverrons un email de confirmation pour valider votre reservation</span><br>
            <input id="btnClose" type="submit" name="lpr_user_res_submit" value="Valider">
        </form>
    </dialog>