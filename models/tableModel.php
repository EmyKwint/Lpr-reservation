<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Modèle pour gérer les éléments
 */
class TableModel
{
    private $table_name;
    private $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'lpr_tables';
    }
    /**
     * Récupérer tous les éléments
     * 
     * @return array Liste des éléments
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY id_table ASC";
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

    public function create($data) 
    {
        $result = $this->wpdb->insert(
            $this->table_name,
            ['nombre_places' => intval($data['nombre_places'])],
            ['%d']
        );
        return ($result !== false) ? $this->wpdb->insert_id : false;
    }

    public function update($id, $data)
    {
        return $this->wpdb->update(
            $this->table_name,
            ['nombre_places' => intval($data['nombre_places'])],
            ['%d'],
            ['id_table' => $id],
            ['%d']
        ) !== false;
    }
    public function delete($id)
    {
        return $this->wpdb->delete(
            $this->table_name,
            ['id_table' => $id],
            ['%d']
        ) !== false;
    }

    public function createTable()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
                id_table INT(11) NOT NULL AUTO_INCREMENT,
                nombre_places INT(11),
                status INT(100) DEFAULT 1,
                PRIMARY KEY  (id_table)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Supprimer la table
     */
    public function dropTable()
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }

    public function getAvailableTables($date, $service) 
    {
        $table_reservation_link = $this->wpdb->prefix . 'lpr_tables_reservations';
        $table_reservation      = $this->wpdb->prefix . 'lpr_reservations';

        $query = $this->wpdb->prepare("
            SELECT id_table, nombre_places 
            FROM {$this->table_name} 
            WHERE id_table NOT IN (
                SELECT rt.id_table 
                FROM $table_reservation_link rt
                JOIN $table_reservation r ON rt.id_reservation = r.id_reservation
                WHERE r.date = %s AND r.service = %d
            )
            ORDER BY nombre_places ASC", 
            $date, 
            $service
        );

        return $this->wpdb->get_results($query, ARRAY_A);
    }
}






























