<?php
// Sécurité
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Gestions des Réservations / Tables</h1>
    <h2 class="wp-heading-inline">Réservations</h2>
    <a href="<?php echo admin_url('admin.php?page=lpr-admin-reservation'); ?>" class="page-title-action">
        Ajouter une réservation
    </a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Client</th>
                <th>Personnes</th>
                <th>Table(s) assignée(s)</th> 
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservation as $res): ?>
                <tr>
                    <td><?php echo date_i18n('d/m/Y', strtotime($res->date)); ?></td>
                    <td><?php echo date('H:i', strtotime($res->heure)); ?></td>
                    <td><strong><?php echo esc_html($res->nom); ?></strong></td>
                    <td><?php echo esc_html($res->personnes); ?></td>
                    <td>
                        <?php if (!empty($res->numero_tables)): ?>
                            <span class="dashicons dashicons-grid-view" style="font-size:16px; vertical-align:middle;"></span>
                            <?php echo esc_html($res->numero_tables); ?>
                        <?php else: ?>
                            <span style="color:red; font-style:italic;">Aucune table</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=lpr-admin-reservation&id=' . $res->id_reservation); ?>" class="button button-small">
                            Modifier
                        </a>

                        <?php 
                        $delete_url = admin_url('admin.php?page=lpr-reservation-menu&action=deleteRes&id=' . $res->id_reservation);
                        // Sécurité : On ajoute un nonce pour éviter les suppressions accidentelles
                        $delete_url = wp_nonce_url($delete_url, 'delete_res_' . $res->id_reservation);
                        ?>
                        <a href="<?php echo $delete_url; ?>" 
                        class="button button-small button-link-delete" 
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');" 
                        style="color: #a00;">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="wp-heading-inline">Tables</h2>
    <a href="<?php echo admin_url('admin.php?page=lpr-admin-table'); ?>" class="page-title-action">
        Ajouter une table
    </a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Capacité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($table as $tab): ?>
                <tr>
                    <td><?php echo intval($tab->id_table); ?></td>
                    <td>
                        <span class="dashicons dashicons-grid-view" style="font-size:16px; vertical-align:middle;"></span>
                        <?php echo intval($tab->nombre_places); ?>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=lpr-admin-table&id=' . $tab->id_table); ?>" class="button button-small">
                            Modifier
                        </a>

                        <?php 
                        $delete_url = admin_url('admin.php?page=lpr-reservation-menu&action=deleteTab&id=' . $tab->id_table);
                        // Sécurité : On ajoute un nonce pour éviter les suppressions accidentelles
                        $delete_url = wp_nonce_url($delete_url, 'delete_tab_' . $tab->id_table);
                        ?>
                        <a href="<?php echo $delete_url; ?>" 
                        class="button button-small button-link-delete" 
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette table ?');" 
                        style="color: #a00;">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>