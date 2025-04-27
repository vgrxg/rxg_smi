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
     * Instance de l'analyseur de hiérarchie
     */
    protected $hierarchy_analyzer;
    
    /**
     * Instance de l'analyseur de taxonomies
     */
    protected $taxonomy_analyzer;
    
    /**
     * Instance de l'analyseur d'ancres
     */
    protected $anchor_analyzer;
    
    /**
     * Instance de l'analyseur de contenu
     */
    protected $content_analyzer;
    
    /**
     * Instance de l'analyseur sémantique
     */
    protected $semantic_analyzer;

    /**
     * Constructeur
     */
    public function __construct($crawler, $db, $hierarchy_analyzer = null, $taxonomy_analyzer = null, $anchor_analyzer = null, $content_analyzer = null, $semantic_analyzer = null) {
        $this->crawler = $crawler;
        $this->db = $db;
        $this->hierarchy_analyzer = $hierarchy_analyzer;
        $this->taxonomy_analyzer = $taxonomy_analyzer;
        $this->anchor_analyzer = $anchor_analyzer;
        $this->content_analyzer = $content_analyzer;
        $this->semantic_analyzer = $semantic_analyzer;
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
        
        // Sous-menu: Analyse Sémantique
        if ($this->semantic_analyzer) {
            add_submenu_page(
                'rxg-smi',
                __('Analyse Sémantique', 'rxg-smi'),
                __('Analyse Sémantique', 'rxg-smi'),
                'manage_options',
                'rxg-smi-semantic',
                array($this, 'display_semantic_analysis')
            );
        }
                
        // Sous-menu: Taxonomies
        add_submenu_page(
            'rxg-smi',
            __('Taxonomies', 'rxg-smi'),
            __('Taxonomies', 'rxg-smi'),
            'manage_options',
            'rxg-smi-taxonomies',
            array($this, 'display_plugin_taxonomies')
        );

        // Sous-menu: Opportunités
        add_submenu_page(
            'rxg-smi',
            __('Opportunités', 'rxg-smi'),
            __('Opportunités', 'rxg-smi'),
            'manage_options',
            'rxg-smi-opportunities',
            array($this, 'display_plugin_opportunities')
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
        // Sous-menu: Documentation
        add_submenu_page(
            'rxg-smi',
            __('Documentation', 'rxg-smi'),
            __('Documentation', 'rxg-smi'),
            'manage_options',
            'rxg-smi-documentation',
            array($this, 'display_plugin_documentation')
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
     * Affiche la page de documentation
     */
    public function display_plugin_documentation() {
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-documentation.php';
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
 * Affiche l'interface d'analyse sémantique
 */
public function display_semantic_analysis() {
    $page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
    
    if ($page_id > 0) {
        // Récupérer les détails de la page
        global $wpdb;
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $page_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_pages WHERE id = %d", $page_id));
        
        if ($page_details) {
            // Récupérer les termes sémantiques
            $semantic_terms = $this->semantic_analyzer->get_page_top_terms($page_id);
            
            // Récupérer les suggestions de liens sémantiques
            $semantic_links = $this->semantic_analyzer->get_semantic_link_suggestions($page_id);
            
            // Récupérer la carte thématique
            $thematic_map = $this->semantic_analyzer->get_page_thematic_map($page_id);
            
            // Inclure le template
            include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-semantic-page.php';
        } else {
            echo '<div class="notice notice-error"><p>' . __('Page non trouvée.', 'rxg-smi') . '</p></div>';
        }
    } else {
        // Récupérer les opportunités globales
        global $wpdb;
        $table_semantic_similarities = $wpdb->prefix . 'rxg_smi_semantic_similarities';
        $table_pages = $wpdb->prefix . 'rxg_smi_pages';
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Pages avec haute similarité sémantique mais sans liens
        $high_similarity_pages = $wpdb->get_results("
            SELECT p1.id as page1_id, p1.title as page1_title, 
                   p2.id as page2_id, p2.title as page2_title,
                   s.similarity
            FROM $table_semantic_similarities s
            INNER JOIN $table_pages p1 ON p1.id = s.page_id_1
            INNER JOIN $table_pages p2 ON p2.id = s.page_id_2
            WHERE s.similarity > 0.6
            AND NOT EXISTS (
                SELECT 1 FROM $table_links l 
                WHERE (l.source_id = s.page_id_1 AND l.target_id = s.page_id_2)
                   OR (l.source_id = s.page_id_2 AND l.target_id = s.page_id_1)
            )
            ORDER BY s.similarity DESC
            LIMIT 20
        ");
        
        // Clusters thématiques
        $thematic_clusters = $this->semantic_analyzer->get_thematic_clusters();
        
        // Inclure le template
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-semantic.php';
    }
}

/**
 * Gère l'analyse sémantique manuelle
 */
public function handle_semantic_analysis() {
    // Vérifier les autorisations
    if (!current_user_can('manage_options')) {
        wp_die(__('Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.', 'rxg-smi'));
    }
    
    // Vérifier le nonce
    check_admin_referer('rxg_smi_semantic_analysis', 'rxg_smi_nonce');
    
    // Lancer l'analyse
    $count = $this->semantic_analyzer->analyze_site_content();
    
    // Rediriger avec un message
    wp_redirect(add_query_arg(
        array(
            'page' => 'rxg-smi-semantic',
            'analyzed' => 1,
            'count' => $count
        ),
        admin_url('admin.php')
    ));
    exit;
}

    /**
     * Affiche la liste des pages
     */
    public function display_plugin_pages() {
        global $wpdb;
        
        // Récupérer les paramètres de filtrage
        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'juice_score';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
        $min_depth = isset($_GET['min_depth']) ? intval($_GET['min_depth']) : '';
        $max_depth = isset($_GET['max_depth']) ? intval($_GET['max_depth']) : '';
        
        // Configurer les arguments
        $args = array(
            'post_type' => $post_type,
            'orderby' => $orderby,
            'order' => $order,
            'min_depth' => $min_depth,
            'max_depth' => $max_depth,
            'limit' => 50
        );
        
        // Récupérer les pages depuis la base de données
        $pages = $this->db->get_pages($args);
        
        // Inclure le template
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
     * Affiche les opportunités de maillage interne
     */
    public function display_plugin_opportunities() {
        global $wpdb;
        
        // Vérifier si une page spécifique est demandée
        $page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 0;
        
        if ($page_id > 0) {
            // Récupérer les détails de la page
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            $page_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_pages WHERE id = %d", $page_id));
            
            // Générer des suggestions
            $taxonomy_suggestions = $this->taxonomy_analyzer->get_taxonomy_suggestions($page_id);
            $potential_links = $this->taxonomy_analyzer->get_potential_links($page_id);
            $suggested_anchors = $this->anchor_analyzer->generate_anchor_suggestions($page_id);
            
            // Inclure le template
            include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-opportunities.php';
        } else {
            // Liste des opportunités générales
            $table_pages = $wpdb->prefix . 'rxg_smi_pages';
            
            // Pages orphelines
            $orphan_pages = $this->hierarchy_analyzer->get_orphan_pages();
            
            // Pages sans liens sortants
            $no_outbound_pages = $wpdb->get_results("
                SELECT * FROM $table_pages 
                WHERE outbound_links_count = 0 
                ORDER BY juice_score DESC
            ");
            
            // Pages avec ratio mots/liens élevé
            $high_ratio_pages = $wpdb->get_results("
                SELECT * FROM $table_pages 
                WHERE word_count > 500 AND outbound_links_count > 0 AND word_link_ratio > 300 
                ORDER BY word_link_ratio DESC 
                LIMIT 10
            ");
            
            // Inclure le template
            include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-opportunities.php';
        }
    }


    /**
     * Affiche la visualisation des taxonomies
     */
    public function display_plugin_taxonomies() {
        global $wpdb;
        
        // Récupérer la taxonomie demandée
        $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : 'category';
        $term_id = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;
        
        // Récupérer toutes les taxonomies
        $taxonomies = $this->db->get_taxonomies();
        
        // Utiliser la première taxonomie si aucune n'est spécifiée
        if (empty($taxonomies)) {
            echo '<div class="notice notice-warning"><p>' . __('Aucune taxonomie trouvée. Veuillez analyser le site.', 'rxg-smi') . '</p></div>';
            return;
        }
        
        if (empty($taxonomy) && !empty($taxonomies)) {
            $taxonomy = $taxonomies[0];
        }
        
        // Récupérer les termes de cette taxonomie
        $terms = $this->db->get_terms_by_taxonomy($taxonomy);
        
        // Récupérer les pages pour un terme spécifique
        $term_pages = array();
        if ($term_id > 0) {
            $term_pages = $this->db->get_pages_by_taxonomy($taxonomy, $term_id);
        }
        
        // Inclure le template
        include_once RXG_SMI_PLUGIN_DIR . 'admin/partials/rxg-smi-admin-taxonomies.php';
    }


    /**
     * Gère l'analyse manuelle du site
     */
    public function handle_manual_analysis() {

        error_log('Début de handle_manual_analysis');
        
        // Vérifier les autorisations
        if (!current_user_can('manage_options')) {
            error_log('Erreur: permissions insuffisantes');
            wp_die(__('Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.', 'rxg-smi'));
        }
        
        // Vérifier le nonce
        if (!isset($_POST['rxg_smi_nonce']) || !wp_verify_nonce($_POST['rxg_smi_nonce'], 'rxg_smi_analyze_site')) {
            error_log('Erreur: vérification du nonce échouée');
            wp_die(__('Vérification de sécurité échouée.', 'rxg-smi'));
        }
        
        error_log('Lancement de l\'analyse');

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
