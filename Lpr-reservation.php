<?php
/**
 * Plugin Name: Lpr-reservation
 * Description: Module WordPress permettant de gerer la partie admin et user de la reservation de table 
 * Version: 1.0
 * Author: Louise
 */
// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'models/ReservationModel.php';
require_once plugin_dir_path(__FILE__) . 'models/TableModel.php';
require_once plugin_dir_path(__FILE__) . 'models/TableResModel.php';
require_once plugin_dir_path(__FILE__) . 'services/reservationService.php';
require_once plugin_dir_path(__FILE__) . 'controllers/reservationHandlers.php';
require_once plugin_dir_path(__FILE__) . 'utils/functions.php';
add_action('init', function() {
    if (!session_id()) {
        session_start();
    }
});

/**
 * Classe principale du plugin
 */
class Lpr_Reservation
{    
    private $reservationModel;
    private $tableModel;
    private $tableResModel;
    private $reservationService;
    private $reservationHandlers;

    public function __construct()
    {   //Models     
        $this->reservationModel = new ReservationModel();
        $this->tableModel       = new TableModel();
        $this->tableResModel    = new TableResModel();
        //Service
        $this->reservationService = new ReservationService(
            $this->reservationModel,
            $this->tableModel,
            $this->tableResModel
        );
        //Handlers
        $this->reservationHandlers = new ReservationHandlers($this->reservationService);
        // Hooks WordPress
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'handleActions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueUserScripts']);
        // API REST :3
        add_action('rest_api_init', function () {
            register_rest_route('lpr/v1', '/reservation', [
                'methods'  => 'GET, POST',
                'callback' => [$this->reservationHandlers, 'processRestRequest'],
                'permission_callback' => '__return_true', // Autorise tout le monde (à sécuriser plus tard)
            ]);
        });
        add_shortcode('lpr_reservation_calendar', [$this, 'getShortcode']);
    }
    //Fonctions de Classe
    public function handleActions()
    {
        $this->reservationHandlers->handleActions();
    }

    public function enqueueScripts($hook)
    {
        // Charger uniquement sur nos pages
        if (strpos($hook, 'lpr-admin') === false) {
            return;
        }
        wp_enqueue_script(
            'lpr-admin-crud',
            plugin_dir_url(__FILE__) . 'assets/js/admin/admin-crud.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_enqueue_script(
            'lpr-admin-res',
            plugin_dir_url(__FILE__) . 'assets/js/admin/reservation-admin.js',
            ['jquery'],
            '1.0',
            true
        );
    }
    public function enqueueUserScripts() 
    {
            wp_enqueue_style(
                'lpr-user-style',
                plugin_dir_url(__FILE__) . 'assets/css/calendar.css',
                [],
                '1.0'
            );

            wp_enqueue_script(
                'dayjs',
                'https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js',
                [],
                '1.0',
                true
            );
            wp_enqueue_script('dayjs-fr', 
            'https://cdn.jsdelivr.net/npm/dayjs@1/locale/fr.js', 
            ['dayjs'], 
            '1.0', 
            true
            );
            wp_enqueue_script(
                'lpr-user-calendar',
                plugin_dir_url(__FILE__) . 'assets/js/calendar.js',
                ['dayjs', 'dayjs-fr'],
                '1.0',
                true
            );
            wp_localize_script('lpr-user-calendar', 'LprConfig', [
                'rest_url' => home_url('/index.php?rest_route=/lpr/v1/reservation'),
                'nonce'    => wp_create_nonce('wp_rest')
            ]);
        }

    public function getShortcode()
    {
        $this->enqueueUserScripts();
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/calendar.php';
        return ob_get_clean();
    }

    // Ajouter le menu dans l'admin
    public function addAdminMenu()
    {
        add_menu_page(
            'LPR Reservation',
            'LPR Reservation',
            'manage_options',
            'lpr-reservation-menu',
            [$this, 'renderAdminListingPage'],
            'dashicons-admin-settings',
            30
        );
        // Sous-page : Ajouter un élément
        add_submenu_page(
            'lpr-reservation-menu',           // Parent slug
            'Gerer les reservation',          // Titre de la page
            'Reservations',                     // Titre du menu
            'manage_options',              // Capacité requise
            'lpr-admin-reservation',               // Slug de la page
            [$this, 'renderAdminResPage']  // Fonction de rendu
        );
        // Sous-page : Ajouter un élément
        add_submenu_page(
            'lpr-reservation-menu',           // Parent slug
            'Gerer les tables',          // Titre de la page
            'Tables',                     // Titre du menu
            'manage_options',              // Capacité requise
            'lpr-admin-table',               // Slug de la page
            [$this, 'renderAdminTablePage']  // Fonction de rendu
        );
    }
    
    public function renderAdminResPage()
    {
        $reservation = null;
        $is_edit = false;
        
        // Si un ID est présent, on est en mode édition
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $reservation = $this->reservationModel->getById($id);
            
            if (!$reservation) {
                wp_die('Produit non trouvé');
            }
            
            $is_edit = true;
        }
        
        include plugin_dir_path(__FILE__) . 'views/lpr-admin-reservation.php';
    }
        public function renderAdminTablePage()
    {
        $table   = null;
        $is_edit = false;

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $table = $this->tableModel->getById($id);
            
            if (!$table) {
                wp_die('Produit non trouvé');
            }
            
            $is_edit = true;
        }

        include plugin_dir_path(__FILE__) . 'views/lpr-admin-table.php';
    }
    public function renderAdminListingPage()
    {        
        $reservation = $this->reservationModel->getAll();
        $table = $this->tableModel->getAll();

        include plugin_dir_path(__FILE__) . 'views/lpr-admin-menu.php';
    }

    /**
     * Gérer les actions (suppression, ajout, modification)
     */
    public function createTable()
    {
        $this->reservationModel->createTable();
        $this->tableModel->createTable();
        $this->tableResModel->createTable();
    }
    /**
     * Supprimer la table lors de la désactivation du plugin
     */
    public function dropTable()
    {
        $this->reservationModel->dropTable();
        $this->tableModel->dropTable();
        $this->tableResModel->dropTable();
    }

}

// Démarrer le plugin
$Lpr_admin = new Lpr_Reservation();
register_activation_hook(__FILE__, [$Lpr_admin, 'createTable']);
register_deactivation_hook(__FILE__, [$Lpr_admin, 'dropTable']);