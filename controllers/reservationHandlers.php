<?php
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class ReservationHandlers
{
    private $service;

    public function __construct($reservationService) 
    {
        $this->service = $reservationService;
    }

    public function handleActions()
    {
        // On guette la suppression (GET)
        if (isset($_GET['action']) && $_GET['action'] === 'deleteRes') {
            $this->handleDeletion();
        }
        // On guette le formulaire (POST)
        if (isset($_POST['lpr_res_submit'])) {
            $this->processForm();
        }
        // On guette pour le coté user
        if (isset($_POST['lpr_user_res_submit'])) {
            $this->processUserForm();
        }
    }

    private function handleDeletion()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'lpr-reservation-menu' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_res_' . $id)) {
                wp_die('Action non autorisée');
            }
            
            $this->service->supprimerReservation($id);
            
            wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&deleted=1'));
            exit;
        }
    }

    private function dataVerification($post_data) 
    {
        $error = [];
        //Nettoyage data
        $clean = [
            'nom' => sanitize_text_field($post_data['nom'] ?? ''),
            'prenom' => sanitize_text_field($post_data['prenom'] ?? ''),
            'date' => sanitize_text_field($post_data['date'] ?? ''),
            'heure' => hourToTime($post_data['heure'] ?? ''),
            'service' => sanitize_text_field($post_data['service'] ?? ''),
            'personnes' => intval($post_data['personnes'] ?? ''),
            'telephone' => sanitize_text_field($post_data['telephone'] ?? ''),
            'mail' => sanitize_text_field($post_data['mail'] ?? '')
        ];

        if(empty($clean['nom'])) $error[] = "Le nom est obligatoire";
        if(empty($clean['prenom'])) $error[] = "Le prénom est obligatoire";
        if(!isDateValid($clean['date'])) $error[] = "La date est invalide";
        if(!isHourValid($clean['heure'])) $error[] = "L'heure est invalide";
        if(!filter_var($clean['mail'], FILTER_VALIDATE_EMAIL)) $error[] = "Le mail est invalide";
        if($clean['personnes'] <= 0) $error[] = "Le nombre de personnes doit etre supérieur à 0";
        if(!isServiceOk($clean['service'])) {
            $error[] = "Choix de service invalide";
        } else {
            $clean['service'] = intval(serviceTextToInt($clean['service']));
        }

        return [
            'is_valid' => empty($error),
            'errors' => $error,
            'data' => $clean,
        ];
    }

    private function processForm() 
    {    
        // Gestion de l'ajout/modification
        if (isset($_POST['lpr_res_submit'])) {
            // Vérifier le nonce
            if (!isset($_POST['lpr_admin_nonce']) 
                || !wp_verify_nonce($_POST['lpr_admin_nonce'], 'lpr_admin_save')) {
                wp_die('Action non autorisée');
            }
            // Vérifier les capacités
            if (!current_user_can('manage_options')) {
                wp_die('Vous n\'avez pas les permissions nécessaires');
            }

            $validation = $this->dataVerification($_POST);

            if(!$validation['is_valid']) {
                $_SESSION['status'] = implode('<br>', $validation['errors']);
                wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&error=complet'));
                exit;
            }

            $processedData = $validation['data'];
            $id = isset($_POST['id_reservation']) ? intval($_POST['id_reservation']) : null;

            $succes = $this->service->saveReservation($processedData, $id);
            if ($succes) {
                $param = $id ? 'updated=1' : 'added=1';
                wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&' . $param));
            } else {
                wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&error=complet'));
            }
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=lpr-reservation-menu&error=validation'));
            exit;
        }
    }

    private function processUserForm()
    {
        if(isset($_POST['lpr_user_res_submit'])) {
            if (!isset($_POST['lpr_nonce']) || !wp_verify_nonce('lpr_user_reservation_nonce', 'lpr_nonce')) {
            wp_die('Erreur de sécurité');
            }

            $validation = $this->service->dataVerification($_POST);
            
            if(!$validation['is_valid']) {
                $_SESSION['status'] = implode('<br>', $validation['errors']);
                exit;
            }

            $processedData = $validation['data'];
            $id = isset($_POST['id_reservation']) ? intval($_POST['id_reservation']) : null;

            $succes = $this->service->saveReservation($processedData, $id);
            if($succes) {
                $_SESSION['status'] = "Validé";
            } else {
                $_SESSION['status'] = "Erreur traitement";
            }
        } else {
            $_SESSION['status'] = "Non_lance";
        }
    }

    public function processRestRequest(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        if (!$params) {
            return new WP_REST_Response(['status' => 'error', 'message' => 'Aucune donnée reçue'], 400);
        }
        $validate = $this->dataVerification($params);

        if (!$validate['is_valid']) {
            return new WP_REST_Response([
                'status'  => $validate['errors'],
            ], 400); // Code 400 = Mauvaise requête
        }

        $id = isset($params['id_reservation']) ? intval($params['id_reservation']) : null;
        $res_id = $this->service->saveReservation($validate['data'], $id);

        if ($res_id) {
        return new WP_REST_Response([
            'status'  => 'success',
            'message' => 'Réservation confirmée !',
            'id'      => $res_id
        ], 200);
    }

    return new WP_REST_Response(['status' => 'error', 'message' => 'Erreur BDD'], 500);
    }
}
