<?php
/**
 * Gestionnaire de requêtes AJAX pour RXG SMI
 * 
 * Ce fichier contient les fonctions de traitement AJAX pour les fonctionnalités interactives
 * de l'interface d'administration.
 */

// Si ce fichier est appelé directement, on sort
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion des requêtes AJAX
 */
class RXG_SMI_Ajax_Handler {
    
    /**
     * Instance de la base de données
     */
    private $db;
    
    /**
     * Instance de l'analyseur de taxonomies
     */
    private $taxonomy_analyzer;
    
    /**
     * Instance de l'analyseur d'ancres
     */
    private $anchor_analyzer;
    
    /**
     * Constructeur
     */
    public function __construct($db, $taxonomy_analyzer, $anchor_analyzer) {
        $this->db = $db;
        $this->taxonomy_analyzer = $taxonomy_analyzer;
        $this->anchor_analyzer = $anchor_analyzer;
        
        // Enregistrement des actions AJAX
        add_action('wp_ajax_rxg_smi_get_terms', array($this, 'ajax_get_terms'));
        add_action('wp_ajax_rxg_smi_get_anchor_suggestions', array($this, 'ajax_get_anchor_suggestions'));
        add_action('wp_ajax_rxg_smi_get_potential_links', array($this, 'ajax_get_potential_links'));
        add_action('wp_ajax_rxg_smi_get_page_anchors', array($this, 'ajax_get_page_anchors'));
        add_action('wp_ajax_rxg_smi_check_anchor_usage', array($this, 'ajax_check_anchor_usage'));
    }
    
    /**
     * Vérifie le nonce des requêtes AJAX
     */
    private function verify_ajax_nonce() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rxg_smi_nonce')) {
            wp_send_json_error(array('message' => __('Vérification de sécurité échouée.', 'rxg-smi')));
        }
    }
    
    /**
     * AJAX: Récupère les termes d'une taxonomie
     */
    public function ajax_get_terms() {
        $this->verify_ajax_nonce();
        
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        
        if (empty($taxonomy)) {
            wp_send_json_error(array('message' => __('Taxonomie non spécifiée', 'rxg-smi')));
        }
        
        $terms = $this->db->get_terms_by_taxonomy($taxonomy);
        wp_send_json_success(array('terms' => $terms));
    }
    
    /**
     * AJAX: Récupère les suggestions d'ancres pour une page
     */
    public function ajax_get_anchor_suggestions() {
        $this->verify_ajax_nonce();
        
        $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
        
        if (empty($page_id)) {
            wp_send_json_error(array('message' => __('ID de page non spécifié', 'rxg-smi')));
        }
        
        $suggestions = $this->anchor_analyzer->generate_anchor_suggestions($page_id);
        wp_send_json_success(array('suggestions' => $suggestions));
    }
    
    /**
     * AJAX: Récupère les liens potentiels pour une page
     */
    public function ajax_get_potential_links() {
        $this->verify_ajax_nonce();
        
        $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
        
        if (empty($page_id)) {
            wp_send_json_error(array('message' => __('ID de page non spécifié', 'rxg-smi')));
        }
        
        $potential_links = $this->taxonomy_analyzer->get_potential_links($page_id);
        wp_send_json_success(array('links' => $potential_links));
    }
    
    /**
     * AJAX: Récupère les statistiques d'ancre pour une page
     */
    public function ajax_get_page_anchors() {
        $this->verify_ajax_nonce();
        
        $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
        
        if (empty($page_id)) {
            wp_send_json_error(array('message' => __('ID de page non spécifié', 'rxg-smi')));
        }
        
        $anchor_stats = $this->anchor_analyzer->get_anchor_stats_details($page_id);
        wp_send_json_success($anchor_stats);
    }
    
    /**
     * AJAX: Vérifie l'utilisation d'une ancre sur le site
     */
    public function ajax_check_anchor_usage() {
        $this->verify_ajax_nonce();
        
        $anchor = isset($_POST['anchor']) ? sanitize_text_field($_POST['anchor']) : '';
        
        if (empty($anchor)) {
            wp_send_json_error(array('message' => __('Texte d\'ancre non spécifié', 'rxg-smi')));
        }
        
        global $wpdb;
        $table_links = $wpdb->prefix . 'rxg_smi_links';
        
        // Compter les occurrences
        $usage_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_links WHERE anchor_text = %s",
            $anchor
        ));
        
        // Compter les pages distinctes
        $page_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT target_id) FROM $table_links WHERE anchor_text = %s AND target_id IS NOT NULL",
            $anchor
        ));
        
        wp_send_json_success(array(
            'usage' => intval($usage_count),
            'pages' => intval($page_count)
        ));
    }
}

/**
 * Initialisation du gestionnaire AJAX
 * 
 * Cette fonction doit être appelée après que les classes
 * de base de données et d'analyse sont initialisées.
 */
function rxg_smi_init_ajax_handler($db, $taxonomy_analyzer, $anchor_analyzer) {
    new RXG_SMI_Ajax_Handler($db, $taxonomy_analyzer, $anchor_analyzer);
}
