<?php
// Sécurité
if (!defined('ABSPATH')) {
    exit;
}
//Affichage
$page_title  = $is_edit ? 'Modifier une table' : 'Ajouter une table';
$button_text = $is_edit ? 'Mettre à jour' : 'Ajouter';
$name_value  = $is_edit ? esc_attr($table->id_table) : '';
?>

<div class="wrap">
    <h1><?php echo $is_edit ? 'Modifier la table' : 'Nouvelle table'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin.php?page=lpr-admin-table'); ?>">
        <input type="hidden" name="action" value="lpr_save_table">

        <?php wp_nonce_field('lpr_admin_save', 'lpr_admin_nonce'); ?>
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_table" value="<?php echo $table->id_table; ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th><label for="places">Places</label></th>
                <td><input name="places" type="text" id="places" value="<?php echo $is_edit ? esc_attr($table->nombre_places) : ''; ?>" class="regular-text" required></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="lpr_tab_submit" id="submit" class="button button-primary" value="Enregistrer la table">
        </p>
    </form>

</div>