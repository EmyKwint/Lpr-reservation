<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class TableHandlers
{
    private $service;

    public function __construct($tableService)   
    {
        $this->service = $tableService;
    }

    public function handleActions()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'deleteTab') {
            $this->handleDeletion();
        }
        if (isset($_POST['lpr_tab_submit'])) {
            $this->processTable();
        }
    }

    private function handleDeletion()
    {
        if (isset($_POST['page']) && $_GET['page'] == 'lpr-reservation-menu' && isset($_GET['id'])) {
            $id = intval($_GET['id']);

            if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_tab_' . $id)) {
                wp_die('Action non autorisée');
            }

            $this->service->supprimerTable($id);

            wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&deleted=1'));
            exit;        
        }
    }

    private function processTable()
    {
        if (isset($_POST['lpr_tab_submit'])) {
            // Vérifier le nonce
            if (!isset($_POST['lpr_admin_nonce']) 
                || !wp_verify_nonce($_POST['lpr_admin_nonce'], 'lpr_admin_save')) {
                wp_die('Action non autorisée');
            }
            // Vérifier les capacités
            if (!current_user_can('manage_options')) {
                wp_die('Vous n\'avez pas les permissions nécessaires');
            }

            // Validation
            $valid    = 1;
            $error    = "";
            //var
            $places = intval($_POST['places']);
            //Verif
            if(!is_numeric($places)) {
                $valid = 0;
                $error .= "Nombre invalide";
            }

            if($valid == 1) {
                $data = ['nombre_places' => $places];
                $id = isset($_POST['id_table']) ? intval($_POST['id_table']) : null;
                $result = $this->service->saveTable($data, $id);

                if ($result) {
                    $param = $id ? 'updated=1' : 'added=1';
                    wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&' . $param));
                } else {
                    wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&error=complet'));
                }
                exit;
            } else {
                $_SESSION['status'] = $error;
                wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&error=validation'));
                exit;
            }
        }
    }
}