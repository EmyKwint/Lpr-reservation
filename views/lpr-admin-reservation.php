<?php
// Sécurité
if (!defined('ABSPATH')) {
    exit;
}
//Affichage
$page_title  = $is_edit ? 'Modifier une réservation' : 'Ajouter une réservation';
$button_text = $is_edit ? 'Mettre à jour' : 'Ajouter';
$name_value  = $is_edit ? esc_attr($reservation->nom) : '';
?>

<div class="wrap">
    <h1><?php echo $is_edit ? 'Modifier la réservation' : 'Nouvelle réservation'; ?></h1>

    <form method="post" action="<?php echo admin_url('admin.php?page=lpr-admin-reservation'); ?>">
        <input type="hidden" name="action" value="lpr_save_reservation">

        <?php wp_nonce_field('lpr_admin_save', 'lpr_admin_nonce'); ?>
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_reservation" value="<?php echo $reservation->id_reservation; ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th><label for="nom">Nom</label></th>
                <td><input name="nom" type="text" id="nom" value="<?php echo $is_edit ? esc_attr($reservation->nom) : ''; ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="prenom">Prénom</label></th>
                <td><input name="prenom" type="text" id="prenom" value="<?php echo $is_edit ? esc_attr($reservation->prenom) : ''; ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="telephone">Téléphone</label></th>
                <td><input name="telephone" type="text" id="telephone" value="<?php echo $is_edit ? esc_attr($reservation->telephone) : ''; ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="mail">Email</label></th>
                <td><input name="mail" type="text" id="mail" value="<?php echo $is_edit ? esc_attr($reservation->mail) : ''; ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="date">Date</label></th>
                <td><input name="date" type="date" id="date" value="<?php echo $is_edit ? esc_attr($reservation->date) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="service">Service</label></th>
                <td>
                    <select name="service" id="service">
                        <option value="">-- Choisir --</option>
                        <option value="midi" <?php echo ($is_edit && $reservation->service == 1) ? 'selected' : ''; ?>>Midi</option>
                        <option value="soir" <?php echo ($is_edit && $reservation->service == 2) ? 'selected' : ''; ?>>Soir</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="heure">Heure</label></th>
                <td>
                    <select 
                    name="heure" 
                    id="heure" 
                    data-selected="<?php echo $is_edit ? esc_attr($reservation->heure) : ''; ?>"
                    required>
                        <option value="">Choissez d'abord un service</option>
                    </select>
                </td>
            </tr>
            <?php if ($is_edit): ?>
            <tr>
                <th>Tables assignées</th>
                <td>
                    <span class="dashicons dashicons-grid-view"></span> 
                    <strong>
                        <?php echo !empty($reservation->numeros_tables) ? esc_html($reservation->numeros_tables) : 'Non assignées'; ?>
                    </strong>
                    <p class="description">Les tables sont calculées automatiquement selon la disponibilité au moment de la création.</p>
                </td>
            </tr>
            <?php endif; ?>

            <tr>
                <th><label for="personnes">Nombre de personnes</label></th>
                <td><input name="personnes" type="number" id="personnes" value="<?php echo $is_edit ? esc_attr($reservation->personnes) : '2'; ?>" min="1"></td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="lpr_res_submit" id="submit" class="button button-primary" value="Enregistrer la réservation">
        </p>
    </form>
</div>