<?php
/**
 * Classe de gestion de l'interface d'administration
 */
class RXG_SMI_Admin {

    /**
     * Instance du crawler
     */
    protected $crawler;

    /**
     * Instance de la base de données
     */
    protected $db;

    /**
     * Constructeur
     */
    public function __construct($crawler, $db) {
        $this->crawler = $crawler;
        $this->db = $db;
    }

    /**
     * Ajoute les menus d'administration
     */
    public function add_plugin_admin_menu() {
        // Menu principal
        add_menu_page(
            __('RXG Maillage Interne', 'rxg-smi'),
            __('Maillage Interne', 'rxg-smi'),
            'manage_options',
            'rxg-smi',
            array($this, 'display_plugin_admin_dashboard'),
            'dashicons-networking',
            100
        );
        
        // Sous-menu: Tableau de bord
        add_submenu_page(
            'rxg-smi',
            __('Tableau de bord', 'rxg-smi'),
            __('Tableau de bord', 'rxg-smi'),
            'manage_options',
            'rxg-smi',
            array($this, 'display_plugin_admin_dashboard')
        );
        
        // Sous-menu: Pages
        add_submenu_page(
            'rxg-smi',
            __('Pages', 'rxg-smi'),
            __('Pages', 'rxg-smi'),
            'manage_options',
            'rxg-smi-pages',
            array($this, 'display_plugin_pages')
        );
        
        // Sous-menu: Liens
        add_submenu_page(
            'rxg-smi',
            __('Liens', 'rxg-smi'),
            __('Liens', 'rxg-smi'),
            'manage_options',
            'rxg-smi-links',
            array($this, 'display_plugin_links')
        );
        
        // Sous-menu: Paramètres
        add_submenu_page(
            'rxg-smi',
            __('Paramètres', 'rxg-smi'),
            __('Paramètres', 'rxg-smi'),
            'manage_options',
            'rxg-smi-settings',
            array($this, 'display_plugin_settings')
        );
    }

    /**
     * Enregistre les styles d'administration
     */
    public function enqueue_admin_styles($hook) {
        // N'inclure les styles que sur les pages du plugin
        if (strpos($hook, 'rxg-smi') === false) {
            return;
        }
        
        wp_enqueue_style(
            'rxg-smi-admin-styles',
            RXG_SMI_PLUGIN_URL . 'admin/css/rxg-smi-admin.css',
            array(),
            RXG_SMI_VERSION
        );
    }

    /**
     * Enregistre les scripts d'administration
     */
    public function enqueue_admin_scripts($hook) {
        // N'inclure les scripts que sur les pages du plugin
        if (strpos($hook, 'rxg-smi') === false) {
            return;
        }
        
        wp_enqueue_script(
            'rxg-smi-admin-scripts',
            RXG_SMI_PLUGIN_URL . 'admin/js/rxg-smi-admin.js',
            array('jquery'),
            RXG_SMI_VERSION,
            true
        );
        
        // Ajouter des variables pour le JavaScript
        wp_localize_script(
            'rxg-smi-admin-scripts',
            'rxg_smi',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('rxg_smi_nonce'),
            )
        );
    }

    /**
     * Affiche le tableau de bord principal
     */
    public function display_plugin_admin_dashboard() {
        global $wpdb;
        
        // Tables
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        $table_terms = $wpdb->prefix . 'rxg_smi_page_terms';
        
        // Récupérer les statistiques de base
        $page_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages");
        $link_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_links");
        $internal_links = $wpdb->get_var("SELECT COUNT(*) FROM $table_links WHERE external = 0");
        $external_links = $wpdb->get_var("SELECT COUNT(*) FROM $table_links WHERE external = 1");

        // Pages sans liens sortants
        $no_outlinks = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages WHERE outbound_links_count = 0");
        
        // Dernière analyse
        $last_analyzed = $wpdb->get_var("SELECT MAX(last_crawled) FROM $table_pages");
        
        // Pages orphelines (sans liens entrants)
        $orphan_pages = $wpdb->get_var("SELECT COUNT(*) FROM $table_pages WHERE inbound_links_count = 0");
                
        // Profondeur maximale
        $max_depth = $wpdb->get_var("SELECT MAX(depth) FROM $table_pages");
        
        // Statistiques des taxonomies
        $taxonomies_count = $wpdb->get_var("SELECT COUNT(DISTINCT taxonomy) FROM $table_terms");
        $terms_count = $wpdb->get_var("SELECT COUNT(DISTINCT term_id) FROM $table_terms");
        
        // Pages les plus liées
        $top_pages = $wpdb->get_results("
            SELECT id, title, inbound_links_count, outbound_links_count, depth 
            FROM $table_pages 
            ORDER BY inbound_links_count DESC 
            LIMIT 10
        ");
        
        // Textes d'ancre les plus utilisés
        $top_anchors = $wpdb->get_results("
            SELECT anchor_text, COUNT(*) as count
            FROM $table_links
            WHERE anchor_text != ''
            GROUP BY anchor_text
            ORDER BY count DESC
            LIMIT 10
        ");
        
        // Clusters taxonomiques (simplifié)
        $taxonomy_clusters = array(); // À implémenter complètement si nécessaire
        
        // Inclure le template
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-dashboard.php';
    }

    /**
     * Affiche la liste des pages
     */
    public function display_plugin_pages() {
        // Récupérer les pages depuis la base de données
        $pages = $this->db->get_pages();
        
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-pages.php';
    }

    /**
     * Affiche la liste des liens
     */
    public function display_plugin_links() {
        // Récupération des pages pour le filtre
        $pages = $this->db->get_pages(array('limit' => 1000));
        
        // Si une page est sélectionnée, afficher ses liens
        $page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
        $direction = isset($_GET['direction']) ? sanitize_text_field($_GET['direction']) : 'outbound';
        
        $links = array();
        if ($page_id > 0) {
            $links = $this->db->get_page_links($page_id, $direction);
        }
        
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-links.php';
    }

    /**
     * Affiche la page des paramètres
     */
    public function display_plugin_settings() {
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-settings.php';
    }

    /**
     * Gère l'analyse manuelle du site
     */
    public function handle_manual_analysis() {
        // Vérifier les autorisations
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.', 'rxg-smi'));
        }
        
        // Vérifier le nonce
        check_admin_referer('rxg_smi_analyze_site', 'rxg_smi_nonce');
        
        // Lancer l'analyse
        $count = $this->crawler->analyze_site();
        
        // Rediriger avec un message
        wp_redirect(add_query_arg(
            array(
                'page' => 'rxg-smi',
                'analyzed' => 1,
                'count' => $count
            ),
            admin_url('admin.php')
        ));
        exit;
    }
}
