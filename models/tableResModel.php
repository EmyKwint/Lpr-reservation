<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Modèle pour gérer les éléments
 */
class TableResModel
{
    private $table_name;
    private $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'lpr_tables_reservations';
    }
    /**
     * Récupérer tous les éléments
     * 
     * @return array Liste des éléments
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY id_table DESC";
        return $this->wpdb->get_results($sql);
    }
    /**
     * Récupérer un élément par son ID
     * 
     * @param int $id ID de l'élément
     * @return object|null L'élément ou null si non trouvé
     */
    public function getById($id)
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id_table = %d",
                $id
            )
        );
    }
        /**
     * Créer la table
     */
    public function createTable()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
                id_table_reservation INT(11) NOT NULL AUTO_INCREMENT,
                id_table INT(11),
                id_reservation INT(11),
                status INT(100) DEFAULT 1,
                PRIMARY KEY  (id_table_reservation)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function lier($idReservation, $idTable) 
    {
        return $this->wpdb->insert(
            $this->table_name,
            [
                'id_reservation' => $idReservation,
                'id_table'      => $idTable        
            ],
            ['%d', '%d']
        );
    }

    public function supprimerParReservation($idReservation)
    {
        return $this->wpdb->delete(
            $this->table_name,
            ['id_reservation' => $idReservation],
            ['%d']
        );
    }

    /**
     * Supprimer la table
     */
    public function dropTable()
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }
}