<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class ReservationService 
{
    private $reservationModel;
    private $tableModel;
    private $tableResModel;

    public function __construct($resModel, $tabModel, $linkModel) 
    {
    $this->reservationModel = $resModel;
    $this->tableModel       = $tabModel;
    $this->tableResModel    = $linkModel;
    }

    public function saveReservation($data, $id = null) 
    {
        if($id) {
            return $this->reservationModel->update($id, $data);
        }
        $tablesLibres = $this->tableModel->getAvailableTables($data['date'], $data['service']);

        $selectedTables = [];
        $cumulPlaces = 0;

        foreach ($tablesLibres as $table) {
            if ($cumulPlaces < $data['personnes']) {
                $selectedTables[] = $table['id_table'];
                $cumulPlaces += (int)$table['nombre_places'];
            } else {
                break;
            }
        }
        if($cumulPlaces < $data['personnes']) {
            return false;
        }

        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $newResId = $this->reservationModel->create($data);

            foreach($selectedTables as $tableId) {
                $this->tableResModel->lier($newResId, $tableId);
            }
            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }   
    }
    public function supprimerReservation($id)
    {
        $this->tableResModel->supprimerParReservation($id);
        return $this->reservationModel->delete($id);
    }
}
