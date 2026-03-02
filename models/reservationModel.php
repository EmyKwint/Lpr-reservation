<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Modèle pour gérer les éléments
 */
class ReservationModel
{
    private $table_name;
    private $wpdb;
    
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'lpr_reservations';
    }
    /**
     * Récupérer tous les éléments
     * 
     * @return array Liste des éléments
     */
    public function getAll()
    {
        $table_res  = $this->table_name;
        $table_link = $this->wpdb->prefix . 'lpr_tables_reservations';
        $table_tab  = $this->wpdb->prefix . 'lpr_tables';

        $sql = "SELECT r.*, GROUP_CONCAT(t.id_table SEPARATOR ', ') as numero_tables
        FROM $table_res r
        LEFT JOIN $table_link tr ON r.id_reservation = tr.id_reservation
        LEFT JOIN $table_tab t ON tr.id_table = t.id_table
        GROUP BY r.id_reservation
        ORDER BY id_reservation DESC";

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
        $table_res = $this->table_name; // lpr_reservations
        $table_link = $this->wpdb->prefix . 'lpr_tables_reservations';
        $table_tab = $this->wpdb->prefix . 'lpr_tables';
        
        $sql = $this->wpdb->prepare("
            SELECT r.*, GROUP_CONCAT(t.id_table SEPARATOR ', ') as numeros_tables
            FROM $table_res r
            LEFT JOIN $table_link tr ON r.id_reservation = tr.id_reservation
            LEFT JOIN $table_tab t ON tr.id_table = t.id_table
            WHERE r.id_reservation = %d
            GROUP BY r.id_reservation",
            $id
        );

        return $this->wpdb->get_row($sql);
    }
    
    /**
     * Créer un nouvel élément
     * 
     * @param string $name Nom de l'élément
     * @return int|false ID de l'élément créé ou false en cas d'erreur
     */
public function create($data)
{
    $result = $this->wpdb->insert(
        $this->table_name,
        [
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'date'      => $data['date'],
            'heure'     => $data['heure'],
            'service'   => intval($data['service']),
            'personnes' => intval($data['personnes']),
            'telephone' => $data['telephone'],
            'mail'      => $data['mail'],
        ],
        ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s']
    );
    return ($result !== false) ? $this->wpdb->insert_id : false;
}
    
    /**
     * Mettre à jour un élément
     * 
     * @param int $id ID de l'élément
     * @param string $name Nouveau nom
     * @return bool True si succès, false sinon
     */
    public function update($id, $data)
    {
        return $this->wpdb->update(
            $this->table_name,
            [
                'nom'       => $data['nom'],
                'prenom'    => $data['prenom'],
                'date'      => $data['date'],
                'heure'     => $data['heure'],
                'service'   => intval($data['service']),
                'personnes' => intval($data['personnes']),
                'telephone' => $data['telephone'],
                'mail'      => $data['email'],
            ],
            ['id_reservation' => $id],
            ['%s','%s','%s','%s','%d','%d','%s','%s'],
            ['%d']
        ) !== false;
    }

    /**
     * Supprimer un élément
     * 
     * @param int $id ID de l'élément à supprimer
     * @return bool True si succès, false sinon
     */
    public function delete($id)
    {
        return $this->wpdb->delete(
            $this->table_name,
            ['id_reservation' => $id],
            ['%d']
        ) !== false;
    }
    
    /**
     * Créer la table
     */
    public function createTable()
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
                id_reservation INT(11) NOT NULL AUTO_INCREMENT,
                id_user INT(11),
                date DATE NOT NULL,
                heure TIME NOT NULL,
                service INT(1) NOT NULL,
                personnes INT(2) NOT NULL,
                telephone VARCHAR(50) NOT NULL,
                mail VARCHAR(100) NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                status INT(100) DEFAULT 1,
                PRIMARY KEY  (id_reservation)
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
}