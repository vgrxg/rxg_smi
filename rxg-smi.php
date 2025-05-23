<?php
/**
 * Plugin Name: RXG Site Maillage Interne
 * Plugin URI: https://votresite.com/rxg-smi
 * Description: Cartographie et analyse du maillage interne de votre site WordPress
 * Version: 2.0.0
 * Author: Votre Nom
 * Author URI: https://votresite.com
 * Text Domain: rxg-smi
 * Domain Path: /languages
 */

// Si ce fichier est appelé directement, on sort
if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('RXG_SMI_VERSION', '2.0.0');
define('RXG_SMI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RXG_SMI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RXG_SMI_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Inclusion des fichiers principaux
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-db.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-hierarchy-analyzer.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-taxonomy-analyzer.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-anchor-analyzer.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-content-analyzer.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-semantic-analyzer.php'; // Nouveau !
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-crawler.php';
require_once RXG_SMI_PLUGIN_DIR . 'admin/class-rxg-smi-admin.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-ajax.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-exporter.php';
require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-visualization.php';

/**
 * Démarre le plugin
 */
function rxg_smi_init() {
    // Initialiser la base de données
    $db = new RXG_SMI_DB();
    
    // Initialiser les analyseurs spécialisés
    $hierarchy_analyzer = new RXG_SMI_Hierarchy_Analyzer($db);
    $taxonomy_analyzer = new RXG_SMI_Taxonomy_Analyzer($db);
    $anchor_analyzer = new RXG_SMI_Anchor_Analyzer($db);
    $content_analyzer = new RXG_SMI_Content_Analyzer($db);
    $semantic_analyzer = new RXG_SMI_Semantic_Analyzer($db); // Nouveau !
    
    // Initialiser le crawler avec les analyseurs
    $crawler = new RXG_SMI_Crawler($db, $hierarchy_analyzer, $taxonomy_analyzer, $anchor_analyzer, $content_analyzer);
    
    // Initialiser l'interface d'administration
    $admin = new RXG_SMI_Admin($crawler, $db, $hierarchy_analyzer, $taxonomy_analyzer, $anchor_analyzer, $content_analyzer, $semantic_analyzer);
    
    // Initialiser le gestionnaire AJAX
    rxg_smi_init_ajax_handler($db, $taxonomy_analyzer, $anchor_analyzer, $semantic_analyzer);
    
    // Enregistrer les hooks d'administration
    add_action('admin_menu', array($admin, 'add_plugin_admin_menu'));
    
    // Hook pour l'action d'analyse manuelle
    add_action('admin_post_rxg_smi_analyze_site', array($admin, 'handle_manual_analysis'));
    
    // Nouveau hook pour l'analyse sémantique
    add_action('admin_post_rxg_smi_semantic_analysis', array($admin, 'handle_semantic_analysis'));
    
    // Planifier l'analyse du site
    if (!wp_next_scheduled('rxg_smi_daily_analysis')) {
        $schedule = get_option('rxg_smi_schedule', 'daily');
        if ($schedule !== 'manual') {
            wp_schedule_event(time(), $schedule, 'rxg_smi_daily_analysis');
        }
    }
    
    add_action('rxg_smi_daily_analysis', array($crawler, 'analyze_site'));
    
    // Ajouter un hook pour l'analyse sémantique
    add_action('rxg_smi_semantic_analysis', array($semantic_analyzer, 'analyze_site_content'));
    
    // Planifier l'analyse sémantique après l'analyse du site
    add_action('rxg_smi_daily_analysis', function() {
        // Planifier l'analyse sémantique 5 minutes après l'analyse du site
        wp_schedule_single_event(time() + 300, 'rxg_smi_semantic_analysis');
    });
    // Initialiser l'exportateur et la visualisation
    $exporter = new RXG_SMI_Exporter($db);
    $visualization = new RXG_SMI_Visualization($db, $exporter);

    // Ajouter les actions pour l'export et la visualisation
    add_action('admin_post_rxg_smi_export_json', array($exporter, 'handle_export_request'));
    add_action('admin_post_rxg_smi_export_csv', array($visualization, 'export_csv'));
    add_action('wp_ajax_rxg_smi_get_visualization_data', array($visualization, 'get_visualization_data'));  
}

/**
 * Fonction exécutée lors de l'activation du plugin
 */
function rxg_smi_activate() {
    require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-activator.php';
    RXG_SMI_Activator::activate();
}
register_activation_hook(__FILE__, 'rxg_smi_activate');

/**
 * Fonction exécutée lors de la désactivation du plugin
 */
function rxg_smi_deactivate() {
    require_once RXG_SMI_PLUGIN_DIR . 'includes/class-rxg-smi-deactivator.php';
    RXG_SMI_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'rxg_smi_deactivate');

/**
 * Enregistre les traductions
 */
function rxg_smi_load_textdomain() {
    load_plugin_textdomain('rxg-smi', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'rxg_smi_load_textdomain');

/**
 * Fonction utilitaire pour récupérer l'ID de post à partir de l'ID de page interne
 */
function get_post_id_by_page_id($page_id) {
    global $wpdb;
    $table_pages = $wpdb->prefix . 'rxg_smi_pages';
    return $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $table_pages WHERE id = %d", $page_id));
}

// Initialisation du plugin
rxg_smi_init();